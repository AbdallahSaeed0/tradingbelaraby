<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSettings;
use App\Models\ContactForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactManagementController extends Controller
{
    public function index()
    {
        $contactSettings = ContactSettings::first();
        $contactForms = ContactForm::latest()->take(5)->get();
        $totalSubmissions = ContactForm::count();
        $unreadSubmissions = ContactForm::where('status', 'unread')->count();
        $readSubmissions = ContactForm::where('status', 'read')->count();
        $repliedSubmissions = ContactForm::where('status', 'replied')->count();

        return view('admin.settings.contact-management.index', compact(
            'contactSettings',
            'contactForms',
            'totalSubmissions',
            'unreadSubmissions',
            'readSubmissions',
            'repliedSubmissions'
        ));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'map_embed_url' => 'nullable|url|max:1000',
            'map_latitude' => 'nullable|string|max:20',
            'map_longitude' => 'nullable|string|max:20',
            'office_hours' => 'nullable|string|max:500',
            'social_facebook' => 'nullable|url|max:255',
            'social_twitter' => 'nullable|url|max:255',
            'social_youtube' => 'nullable|url|max:255',
            'social_linkedin' => 'nullable|url|max:255',
            'social_snapchat' => 'nullable|url|max:255',
            'social_tiktok' => 'nullable|url|max:255',
            'is_active' => 'nullable|in:0,1',
        ]);

        $data = $request->except(['is_active']);
        $data['is_active'] = $request->input('is_active') == '1';

        $contactSettings = ContactSettings::first();

        if ($contactSettings) {
            $contactSettings->update($data);
        } else {
            ContactSettings::create($data);
        }

        return response()->json([
            'success' => true,
            'message' => __('Contact settings updated successfully!')
        ]);
    }

    public function contactForms()
    {
        $contactForms = ContactForm::latest()->paginate(15);
        $totalSubmissions = ContactForm::count();
        $unreadSubmissions = ContactForm::where('status', 'unread')->count();
        $readSubmissions = ContactForm::where('status', 'read')->count();
        $repliedSubmissions = ContactForm::where('status', 'replied')->count();

        return view('admin.settings.contact-management.contact-forms', compact(
            'contactForms',
            'totalSubmissions',
            'unreadSubmissions',
            'readSubmissions',
            'repliedSubmissions'
        ));
    }

    public function showContactForm(ContactForm $contactForm)
    {
        // Mark as read if unread
        if ($contactForm->status === 'unread') {
            $contactForm->update(['status' => 'read']);
        }

        return view('admin.settings.contact-management.contact-form-details', compact('contactForm'));
    }

    public function updateContactFormStatus(Request $request, ContactForm $contactForm)
    {
        $request->validate([
            'status' => 'required|in:read,unread,replied'
        ]);

        $contactForm->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => __('Contact form status updated successfully!')
        ]);
    }

    public function deleteContactForm(ContactForm $contactForm)
    {
        $contactForm->delete();

        return response()->json([
            'success' => true,
            'message' => __('Contact form submission deleted successfully!')
        ]);
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,mark_read,mark_unread,mark_replied',
            'contact_forms' => 'required|array',
            'contact_forms.*' => 'exists:contact_forms,id'
        ]);

        $contactForms = ContactForm::whereIn('id', $request->contact_forms);
        $count = $contactForms->count();

        switch ($request->action) {
            case 'delete':
                $contactForms->delete();
                $message = "Successfully deleted {$count} contact form submissions.";
                break;
            case 'mark_read':
                $contactForms->update(['status' => 'read']);
                $message = "Marked {$count} submissions as read.";
                break;
            case 'mark_unread':
                $contactForms->update(['status' => 'unread']);
                $message = "Marked {$count} submissions as unread.";
                break;
            case 'mark_replied':
                $contactForms->update(['status' => 'replied']);
                $message = "Marked {$count} submissions as replied.";
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    public function exportContactForms()
    {
        $contactForms = ContactForm::latest()->get();

        $filename = 'contact_forms_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($contactForms) {
            $file = fopen('php://output', 'w');

            // Add headers
            fputcsv($file, [
                'ID', 'Name', 'Email', 'Phone', 'Subject', 'Message', 'Status', 'Created At'
            ]);

            // Add data
            foreach ($contactForms as $form) {
                fputcsv($file, [
                    $form->id,
                    $form->name,
                    $form->email,
                    $form->phone,
                    $form->subject,
                    $form->message,
                    $form->status,
                    $form->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
