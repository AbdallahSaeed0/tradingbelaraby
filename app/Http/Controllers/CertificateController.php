<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CertificateController extends Controller
{
    /**
     * Show certificate name input form
     */
    public function showCertificateForm(Course $course)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'completed')
            ->first();

        if (!$enrollment) {
            return redirect()->route('student.my-courses')
                ->with('error', 'Course not completed yet.');
        }

        if ($enrollment->certificate_path) {
            return redirect()->route('certificate.download', $enrollment->id);
        }

        // Refresh course to ensure enable_certificate is loaded
        $course->refresh();
        
        if (!$course->enable_certificate) {
            return redirect()->route('courses.learn', $course->id)
                ->with('error', 'This course does not offer certificates.');
        }

        return view('certificates.request', compact('course', 'enrollment'));
    }

    /**
     * Store certificate name and generate certificate
     */
    public function storeCertificateName(Request $request, Course $course)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'completed')
            ->first();

        if (!$enrollment) {
            return back()->with('error', 'Course not completed yet.');
        }

        if (!$course->enable_certificate) {
            return back()->with('error', 'This course does not offer certificates.');
        }

        $request->validate([
            'certificate_name' => 'required|string|max:255',
        ]);

        $enrollment->update([
            'certificate_name' => $request->certificate_name,
        ]);

        // Generate certificate
        try {
            $certificatePath = $this->generateCertificate($course, $enrollment, $request->certificate_name);
            
            $enrollment->update([
                'certificate_path' => $certificatePath,
                'certificate_issued_at' => now(),
            ]);

            return redirect()->route('certificate.download', $enrollment->id)
                ->with('success', 'Certificate generated successfully!');
        } catch (\Exception $e) {
            \Log::error('Certificate generation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate certificate. Please try again.');
        }
    }

    /**
     * Download certificate
     */
    public function download($enrollment)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Get enrollment by ID
        $enrollmentModel = CourseEnrollment::with('course')->findOrFail($enrollment);
        
        if ($enrollmentModel->user_id != $user->id) {
            abort(403);
        }

        if (!$enrollmentModel->certificate_path || !Storage::exists($enrollmentModel->certificate_path)) {
            return redirect()->route('certificate.request', $enrollmentModel->course_id)
                ->with('error', 'Certificate not found. Please regenerate.');
        }

        $fileExtension = pathinfo($enrollmentModel->certificate_path, PATHINFO_EXTENSION);
        $fileName = 'certificate-' . $enrollmentModel->course->slug . '-' . $enrollmentModel->id . '.' . $fileExtension;
        
        // If it's an HTML file, return it as a view instead of download
        if ($fileExtension === 'html') {
            return $this->view($enrollment);
        }

        return Storage::download($enrollmentModel->certificate_path, $fileName);
    }

    /**
     * View certificate
     */
    public function view($enrollment)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Get enrollment by ID
        $enrollmentModel = CourseEnrollment::with('course')->findOrFail($enrollment);
        
        if ($enrollmentModel->user_id != $user->id) {
            abort(403);
        }

        if (!$enrollmentModel->certificate_path || !Storage::exists($enrollmentModel->certificate_path)) {
            return redirect()->route('certificate.request', $enrollmentModel->course_id)
                ->with('error', 'Certificate not found. Please regenerate.');
        }

        $fileExtension = pathinfo($enrollmentModel->certificate_path, PATHINFO_EXTENSION);
        
        // If it's an HTML file, render it directly
        if ($fileExtension === 'html') {
            $htmlContent = Storage::get($enrollmentModel->certificate_path);
            return response($htmlContent)->header('Content-Type', 'text/html');
        }

        // For PDF files, return as file
        return response()->file(Storage::path($enrollmentModel->certificate_path));
    }

    /**
     * Generate certificate as PDF using DomPDF (or HTML fallback)
     */
    private function generateCertificate(Course $course, CourseEnrollment $enrollment, string $studentName): string
    {
        $certificatePath = 'certificates/' . $course->id . '/' . $enrollment->id . '.pdf';
        Storage::makeDirectory(dirname($certificatePath));
        
        // Generate HTML certificate template
        $html = view('certificates.template', [
            'studentName' => $studentName,
            'courseName' => $course->localized_name,
            'completionDate' => $enrollment->completed_at->format('F d, Y'),
            'course' => $course,
        ])->render();

        // Check if DomPDF class exists
        if (class_exists('\Dompdf\Dompdf')) {
            try {
                $dompdf = new \Dompdf\Dompdf();
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'landscape');
                $dompdf->render();
                $pdfContent = $dompdf->output();
                Storage::put($certificatePath, $pdfContent);
                return $certificatePath;
            } catch (\Exception $e) {
                Log::error('PDF generation failed: ' . $e->getMessage());
                // Fall through to HTML fallback
            }
        } else {
            Log::warning('Dompdf class not found. Using HTML fallback. Please install dompdf/dompdf via composer.');
        }
        
        // Fallback: Save as HTML (can be printed to PDF by browser)
        $htmlPath = str_replace('.pdf', '.html', $certificatePath);
        Storage::put($htmlPath, $html);
        Log::info('Certificate saved as HTML. User can print to PDF from browser.');
        return $htmlPath;
    }
}
