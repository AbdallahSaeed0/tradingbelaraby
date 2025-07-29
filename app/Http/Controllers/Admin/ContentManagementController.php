<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipBanner;
use App\Models\CTAVideo;
use App\Models\ContactForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ContentManagementController extends Controller
{
    /**
     * Display the content management page.
     */
        public function index()
    {
        $scholarshipBanner = ScholarshipBanner::active()->first();
        $ctaVideo = CTAVideo::active()->first();

        return view('admin.settings.content-management.index', compact('scholarshipBanner', 'ctaVideo'));
    }

    /**
     * Store scholarship banner content.
     */
    public function storeScholarshipBanner(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'button_text' => 'required|string|max:255',
            'button_text_ar' => 'nullable|string|max:255',
            'button_url' => 'nullable|url',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();
            $data['is_active'] = $request->has('is_active') && $request->input('is_active') === 'on';

            // Handle image upload
            if ($request->hasFile('background_image')) {
                $image = $request->file('background_image');
                $imageName = 'scholarship-banner-' . time() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('public/content', $imageName);
                $data['background_image'] = str_replace('public/', '', $imagePath);
            }

            // Update or create scholarship banner
            $scholarshipBanner = ScholarshipBanner::updateOrCreate(
                ['id' => 1], // Assuming only one banner
                $data
            );

            return response()->json([
                'success' => true,
                'message' => __('Scholarship banner updated successfully'),
                'scholarship_banner' => $scholarshipBanner
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error updating scholarship banner: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store CTA video content.
     */
    public function storeCTAVideo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'required|string',
            'description_ar' => 'nullable|string',
            'video_url' => 'nullable|url',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();
            $data['is_active'] = $request->has('is_active') && $request->input('is_active') === 'on';

            // Handle image upload
            if ($request->hasFile('background_image')) {
                $image = $request->file('background_image');
                $imageName = 'cta-video-' . time() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('public/content', $imageName);
                $data['background_image'] = str_replace('public/', '', $imagePath);
            }

            // Update or create CTA video
            $ctaVideo = CTAVideo::updateOrCreate(
                ['id' => 1], // Assuming only one CTA video
                $data
            );

            return response()->json([
                'success' => true,
                'message' => __('CTA video updated successfully'),
                'cta_video' => $ctaVideo
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error updating CTA video: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle scholarship banner status.
     */
    public function toggleScholarshipBannerStatus()
    {
        try {
            $scholarshipBanner = ScholarshipBanner::first();
            if ($scholarshipBanner) {
                $scholarshipBanner->update(['is_active' => !$scholarshipBanner->is_active]);
            }

            return response()->json([
                'success' => true,
                'message' => __('Scholarship banner status updated successfully'),
                'is_active' => $scholarshipBanner ? $scholarshipBanner->is_active : false
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error updating scholarship banner status: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle CTA video status.
     */
    public function toggleCTAVideoStatus()
    {
        try {
            $ctaVideo = CTAVideo::first();
            if ($ctaVideo) {
                $ctaVideo->update(['is_active' => !$ctaVideo->is_active]);
            }

            return response()->json([
                'success' => true,
                'message' => __('CTA video status updated successfully'),
                'is_active' => $ctaVideo ? $ctaVideo->is_active : false
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error updating CTA video status: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display contact form submissions.
     */
    public function contactForms()
    {
        $contactForms = ContactForm::orderBy('created_at', 'desc')->paginate(15);

        return view('admin.settings.content-management.contact-forms', compact('contactForms'));
    }

    /**
     * Show contact form details.
     */
    public function showContactForm(ContactForm $contactForm)
    {
        // Mark as read if status is new
        if ($contactForm->status === 'new') {
            $contactForm->markAsRead();
        }

        return view('admin.settings.content-management.contact-form-details', compact('contactForm'));
    }

    /**
     * Update contact form status.
     */
    public function updateContactFormStatus(Request $request, ContactForm $contactForm)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:new,read,replied,closed',
            'admin_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();

            // Update timestamps based on status
            if ($request->status === 'read' && $contactForm->status !== 'read') {
                $data['read_at'] = now();
            } elseif ($request->status === 'replied' && $contactForm->status !== 'replied') {
                $data['replied_at'] = now();
            }

            $contactForm->update($data);

            return response()->json([
                'success' => true,
                'message' => __('Contact form status updated successfully'),
                'contact_form' => $contactForm
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error updating contact form status: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete contact form.
     */
    public function deleteContactForm(ContactForm $contactForm)
    {
        try {
            $contactForm->delete();

            return response()->json([
                'success' => true,
                'message' => __('Contact form deleted successfully')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error deleting contact form: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export contact forms.
     */
    public function exportContactForms(Request $request)
    {
        $format = $request->get('format', 'csv');
        $status = $request->get('status');

        $query = ContactForm::query();

        if ($status) {
            $query->where('status', $status);
        }

        $contactForms = $query->orderBy('created_at', 'desc')->get();

        if ($format === 'csv') {
            $filename = 'contact_forms_' . date('Y-m-d_H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($contactForms) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['ID', 'Name', 'Email', 'Phone', 'Message', 'Status', 'Created At']);

                foreach ($contactForms as $form) {
                    fputcsv($file, [
                        $form->id,
                        $form->name,
                        $form->email,
                        $form->phone,
                        $form->message,
                        $form->status,
                        $form->created_at->format('Y-m-d H:i:s')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return response()->json([
            'success' => false,
            'message' => __('Unsupported export format')
        ]);
    }
}
