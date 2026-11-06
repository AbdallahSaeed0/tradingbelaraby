<?php

namespace App\Services;

use App\Models\CourseEnrollment;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

/**
 * Builds the "Certificate of Participation" (design: cert/certificate_template.html)
 * for a completed enrollment and stores it once as a static file.
 *
 * PDF is produced by headless Chrome (correct Arabic shaping). When no Chrome/Chromium
 * is available on the server, the same HTML is stored instead and the student prints it
 * to PDF from their own browser — the design is identical either way.
 */
class CertificateService
{
    /**
     * Assign a certificate number if the enrollment does not have one yet.
     * Format: TBA-{year}-C{course id}-{enrollment id, 6 digits} — unique because the enrollment id is.
     */
    public function ensureNumber(CourseEnrollment $enrollment): string
    {
        if (!$enrollment->certificate_number) {
            $issuedAt = $enrollment->completed_at ?? $enrollment->certificate_issued_at ?? now();
            $enrollment->certificate_number = sprintf(
                '%s-%s-C%d-%06d',
                config('certificate.number_prefix', 'TBA'),
                $issuedAt->format('Y'),
                $enrollment->course_id,
                $enrollment->id
            );
            $enrollment->save();
        }

        return $enrollment->certificate_number;
    }

    public function verifyUrl(string $number): string
    {
        $base = config('certificate.verify_base_url') ?: rtrim(config('app.url'), '/') . '/verify';

        return rtrim($base, '/') . '/' . $number;
    }

    /**
     * All variable fields of the template.
     */
    public function data(CourseEnrollment $enrollment): array
    {
        $enrollment->loadMissing(['course.instructors', 'course.instructor', 'user']);
        $course = $enrollment->course;
        $number = $this->ensureNumber($enrollment);

        // Issue date = the day the student met the condition (finished all lectures).
        $issued = $enrollment->completed_at ?? $enrollment->certificate_issued_at ?? now();

        $instructor = $course->instructors->first() ?? $course->instructor;

        return [
            'student_name_ar' => $enrollment->certificate_name ?: ($enrollment->user->name ?? ''),
            'student_name_en' => $enrollment->certificate_name_en,
            'course_title_ar' => $course->name_ar ?: $course->name,
            // Only show the English line when it differs from the Arabic one.
            'course_title_en' => ($course->name_ar && $course->name !== $course->name_ar) ? $course->name : null,
            'issue_date' => $issued->format('d / m / Y'),
            'hours' => $this->hoursLabel($this->courseHours($course)),
            'certificate_id' => $number,
            'verify_url' => $this->verifyUrl($number),
            'instructor_name' => $instructor->name ?? config('certificate.default_instructor_name'),
            'instructor_title' => config('certificate.default_instructor_title'),
        ];
    }

    /**
     * Course hours: admin-entered value first, then the sum of lecture durations.
     */
    public function courseHours($course): ?float
    {
        if ($course->certificate_hours !== null && (float) $course->certificate_hours > 0) {
            return (float) $course->certificate_hours;
        }

        $minutes = (int) $course->total_duration_minutes;
        if ($minutes <= 0) {
            $minutes = (int) $course->sections()->withSum('lectures', 'duration_minutes')->get()->sum('lectures_sum_duration_minutes');
        }

        return $minutes > 0 ? round($minutes / 60 * 2) / 2 : null; // nearest half hour
    }

    /**
     * Arabic hours label: ساعة واحدة / ساعتان / 3 ساعات / 12 ساعة / 1.5 ساعة
     */
    public function hoursLabel(?float $hours): ?string
    {
        if (!$hours) {
            return null;
        }
        if (floor($hours) != $hours) {
            return rtrim(rtrim(number_format($hours, 1, '.', ''), '0'), '.') . ' ساعة';
        }

        $h = (int) $hours;

        return match (true) {
            $h === 1 => 'ساعة واحدة',
            $h === 2 => 'ساعتان',
            $h >= 3 && $h <= 10 => $h . ' ساعات',
            default => $h . ' ساعة',
        };
    }

    public function qrSvg(string $url): string
    {
        $options = new QROptions([
            'outputType' => QROutputInterface::MARKUP_SVG,
            'eccLevel' => EccLevel::M,
            'addQuietzone' => false,
            'outputBase64' => false,
            'svgAddXmlHeader' => false,
            'drawLightModules' => false,
            'connectPaths' => true,
            'markupDark' => '#000',
        ]);

        return (new QRCode($options))->render($url);
    }

    public function fontFaces(): string
    {
        $dir = resource_path('certificate/fonts');
        $faces = [
            ['Cairo', 400, 'cairo-400', null],
            ['Cairo', 700, 'cairo-700', null],
            ['Cairo', 800, 'cairo-800', null],
            ['Cairo', 400, 'cairo-400-latin', 'U+0000-00FF'],
            ['Cairo', 700, 'cairo-700-latin', 'U+0000-00FF'],
            ['Cairo', 800, 'cairo-800-latin', 'U+0000-00FF'],
            ['Amiri', 700, 'amiri-700', null],
        ];

        $css = '';
        foreach ($faces as [$family, $weight, $file, $range]) {
            $b64 = base64_encode(file_get_contents("$dir/$file.woff2"));
            $css .= "@font-face{font-family:'$family';font-weight:$weight;src:url(data:font/woff2;base64,$b64) format('woff2');"
                . ($range ? "unicode-range:$range;" : '') . "}\n";
        }

        return $css;
    }

    /**
     * Self-contained certificate HTML (fonts and QR embedded).
     */
    public function html(CourseEnrollment $enrollment, bool $showToolbar = false, ?string $backUrl = null): string
    {
        $c = $this->data($enrollment);

        return view('certificates.certificate', [
            'c' => $c,
            'fontFaces' => $this->fontFaces(),
            'qrSvg' => $this->qrSvg($c['verify_url']),
            'showToolbar' => $showToolbar,
            'backUrl' => $backUrl,
        ])->render();
    }

    /**
     * Generate and store the certificate. Returns the storage path (.pdf, or .html as fallback).
     */
    public function generate(CourseEnrollment $enrollment): string
    {
        $html = $this->html($enrollment);
        $base = 'certificates/' . $enrollment->course_id . '/' . $enrollment->id;
        Storage::makeDirectory(dirname($base));

        $pdf = $this->htmlToPdf($html);
        if ($pdf !== null) {
            Storage::put($base . '.pdf', $pdf);
            Storage::delete($base . '.html');

            return $base . '.pdf';
        }

        // Fallback: store the same design with a "print to PDF" toolbar (hidden when printing).
        Storage::put($base . '.html', $this->html($enrollment, true));

        return $base . '.html';
    }

    public function chromePath(): ?string
    {
        $candidates = array_filter([
            config('certificate.chrome_path'),
            'C:\Program Files\Google\Chrome\Application\chrome.exe',
            'C:\Program Files (x86)\Google\Chrome\Application\chrome.exe',
            'C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe',
            '/usr/bin/chromium',
            '/usr/bin/chromium-browser',
            '/usr/bin/google-chrome',
            '/usr/bin/google-chrome-stable',
            '/snap/bin/chromium',
            '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
        ]);

        foreach ($candidates as $path) {
            if (@is_file($path) && @is_executable($path)) {
                return $path;
            }
        }

        return null;
    }

    public function canMakePdf(): bool
    {
        return $this->chromePath() !== null && function_exists('proc_open');
    }

    /**
     * Print HTML to an A4-landscape PDF with headless Chrome. Returns null on any failure.
     */
    public function htmlToPdf(string $html): ?string
    {
        $chrome = $this->chromePath();
        if (!$chrome || !function_exists('proc_open')) {
            Log::warning('Certificate: no Chrome/Chromium found (set CERTIFICATE_CHROME_PATH). Storing HTML instead of PDF.');

            return null;
        }

        // Chrome may still hold its profile files when we try to delete them (Windows),
        // so also sweep leftovers from earlier runs.
        foreach (glob(storage_path('app/certificate-tmp/*'), GLOB_ONLYDIR) ?: [] as $old) {
            if (@filemtime($old) < time() - 3600) {
                \Illuminate\Support\Facades\File::deleteDirectory($old);
            }
        }

        $tmp = storage_path('app/certificate-tmp/' . Str::uuid());
        @mkdir($tmp, 0775, true);
        $in = $tmp . '/certificate.html';
        $out = $tmp . '/certificate.pdf';
        file_put_contents($in, $html);

        $process = new Process([
            $chrome,
            // chrome-headless-shell (installable without root) is always headless and wants the plain flag.
            str_contains($chrome, 'headless-shell') ? '--headless' : '--headless=new',
            '--print-to-pdf-no-header', // older Chrome builds
            '--disable-gpu',
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--hide-scrollbars',
            '--no-first-run',
            '--user-data-dir=' . $tmp . '/profile',
            '--no-pdf-header-footer',
            '--virtual-time-budget=10000',
            '--print-to-pdf=' . $out,
            'file:///' . ltrim(str_replace('\\', '/', $in), '/'),
        ]);
        $process->setTimeout(config('certificate.chrome_timeout', 60));

        try {
            $process->run();
            $pdf = is_file($out) ? file_get_contents($out) : null;
            if (!$pdf || !str_starts_with($pdf, '%PDF')) {
                Log::error('Certificate: Chrome did not produce a PDF.', [
                    'exit' => $process->getExitCode(),
                    'stderr' => Str::limit($process->getErrorOutput(), 2000),
                ]);

                return null;
            }

            return $pdf;
        } catch (\Throwable $e) {
            Log::error('Certificate: Chrome PDF failed: ' . $e->getMessage());

            return null;
        } finally {
            \Illuminate\Support\Facades\File::deleteDirectory($tmp);
        }
    }
}
