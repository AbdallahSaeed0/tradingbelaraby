<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\LectureCompletion;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function __construct(private CertificateService $certificates)
    {
    }

    /**
     * The enrollment of the current user that qualifies for a certificate:
     * status "completed" AND every lecture currently in the course is completed.
     */
    private function eligibleEnrollment(Course $course): ?CourseEnrollment
    {
        $enrollment = CourseEnrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('status', 'completed')
            ->first();

        if (!$enrollment) {
            return null;
        }

        $lectureIds = $course->sections()->with('lectures:id,section_id')->get()
            ->flatMap(fn ($section) => $section->lectures->pluck('id'));

        if ($lectureIds->isEmpty()) {
            return null;
        }

        $done = LectureCompletion::where('user_id', Auth::id())
            ->whereIn('lecture_id', $lectureIds)
            ->where('is_completed', true)
            ->count();

        return $done >= $lectureIds->count() ? $enrollment : null;
    }

    /**
     * Show certificate name input form
     */
    public function showCertificateForm(Course $course)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $course->refresh();
        if (!$course->enable_certificate) {
            return redirect()->route('courses.learn', $course->id)
                ->with('error', 'This course does not offer certificates.');
        }

        $enrollment = $this->eligibleEnrollment($course);
        if (!$enrollment) {
            return redirect()->route('student.my-courses')
                ->with('error', 'Course not completed yet.');
        }

        if ($enrollment->certificate_path && $enrollment->certificate_number) {
            return redirect()->route('certificate.download', $enrollment->id);
        }

        return view('certificates.request', compact('course', 'enrollment'));
    }

    /**
     * Store certificate name and generate certificate
     */
    public function storeCertificateName(Request $request, Course $course)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$course->enable_certificate) {
            return back()->with('error', 'This course does not offer certificates.');
        }

        $enrollment = $this->eligibleEnrollment($course);
        if (!$enrollment) {
            return back()->with('error', 'Course not completed yet.');
        }

        // Once issued, the certificate is a fixed document: the name cannot be changed here.
        if ($enrollment->certificate_path && $enrollment->certificate_number) {
            return redirect()->route('certificate.download', $enrollment->id);
        }

        $data = $request->validate([
            'certificate_name' => 'required|string|max:120',
            'certificate_name_en' => 'nullable|string|max:120',
        ]);

        $enrollment->update([
            'certificate_name' => trim($data['certificate_name']),
            'certificate_name_en' => isset($data['certificate_name_en']) ? trim($data['certificate_name_en']) : null,
            'certificate_issued_at' => now(),
            'certificate_status' => 'active',
        ]);

        try {
            $enrollment->update(['certificate_path' => $this->certificates->generate($enrollment)]);

            return redirect()->route('certificate.download', $enrollment->id)
                ->with('success', 'Certificate generated successfully!');
        } catch (\Throwable $e) {
            Log::error('Certificate generation failed: ' . $e->getMessage(), ['enrollment' => $enrollment->id]);

            return back()->with('error', 'Failed to generate certificate. Please try again.');
        }
    }

    /**
     * Owned enrollment with a usable stored certificate. Upgrades old-style certificates
     * (issued before the new template) and HTML fallbacks once Chrome becomes available.
     */
    private function ownedCertificate($enrollmentId): CourseEnrollment
    {
        $enrollment = CourseEnrollment::with('course')->findOrFail($enrollmentId);

        if ($enrollment->user_id != Auth::id()) {
            abort(403);
        }

        $path = $enrollment->certificate_path;
        $missing = !$path || !Storage::exists($path);
        $legacy = $path && !$enrollment->certificate_number;
        $upgradeable = $path && str_ends_with($path, '.html') && $this->certificates->canMakePdf();

        if ($enrollment->certificate_name && ($missing || $legacy || $upgradeable)) {
            try {
                $enrollment->update(['certificate_path' => $this->certificates->generate($enrollment)]);
            } catch (\Throwable $e) {
                Log::error('Certificate regeneration failed: ' . $e->getMessage(), ['enrollment' => $enrollment->id]);
            }
        }

        return $enrollment;
    }

    /**
     * Download certificate
     */
    public function download($enrollment)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $model = $this->ownedCertificate($enrollment);

        if (!$model->certificate_path || !Storage::exists($model->certificate_path)) {
            return redirect()->route('certificate.request', $model->course_id)
                ->with('error', 'Certificate not found. Please regenerate.');
        }

        // HTML fallback: open it; the page has a "print / save as PDF" button.
        if (str_ends_with($model->certificate_path, '.html')) {
            return $this->view($enrollment);
        }

        return Storage::download($model->certificate_path, 'certificate-' . $model->certificate_number . '.pdf');
    }

    /**
     * View certificate
     */
    public function view($enrollment)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $model = $this->ownedCertificate($enrollment);

        if (!$model->certificate_path || !Storage::exists($model->certificate_path)) {
            return redirect()->route('certificate.request', $model->course_id)
                ->with('error', 'Certificate not found. Please regenerate.');
        }

        if (str_ends_with($model->certificate_path, '.html')) {
            return response(Storage::get($model->certificate_path))->header('Content-Type', 'text/html; charset=UTF-8');
        }

        return response()->file(Storage::path($model->certificate_path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="certificate-' . $model->certificate_number . '.pdf"',
        ]);
    }

    /**
     * Public verification page: /verify/{number}
     * Shows only name, course, date, number and status — no other personal data.
     */
    public function verify(string $number)
    {
        $enrollment = CourseEnrollment::with('course')
            ->where('certificate_number', $number)
            ->whereNotNull('certificate_path')
            ->first();

        $certificate = null;
        if ($enrollment) {
            $data = $this->certificates->data($enrollment);
            $certificate = [
                'name' => $data['student_name_ar'],
                'name_en' => $data['student_name_en'],
                'course' => $data['course_title_ar'],
                'course_en' => $data['course_title_en'],
                'date' => $data['issue_date'],
                'number' => $data['certificate_id'],
                'valid' => ($enrollment->certificate_status ?? 'active') === 'active',
            ];
        }

        return response()
            ->view('certificates.verify', ['certificate' => $certificate, 'number' => $number], $certificate ? 200 : 404)
            ->header('X-Robots-Tag', 'noindex');
    }
}
