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
        $unreadSubmissions = ContactForm::where('status', 'new')->count();
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
        $unreadSubmissions = ContactForm::where('status', 'new')->count();
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
        // Opening a new submission marks it as read
        if ($contactForm->status === 'new') {
            $contactForm->markAsRead();
        }

        return view('admin.settings.contact-management.contact-form-details', compact('contactForm'));
    }

    public function updateContactFormStatus(Request $request, ContactForm $contactForm)
    {
        // Must match the contact_forms.status enum
        $request->validate([
            'status' => 'required|in:new,read,replied,closed'
        ]);

        $contactForm->update($this->statusAttributes($request->status, $contactForm));

        return response()->json([
            'success' => true,
            'message' => __('Contact form status updated successfully!')
        ]);
    }

    /**
     * Status plus the matching timestamps (first read / first reply are kept once set)
     */
    private function statusAttributes(string $status, ContactForm $contactForm): array
    {
        $attributes = ['status' => $status];

        if (in_array($status, ['read', 'replied', 'closed'], true) && !$contactForm->read_at) {
            $attributes['read_at'] = now();
        }
        if ($status === 'replied' && !$contactForm->replied_at) {
            $attributes['replied_at'] = now();
        }

        return $attributes;
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
            'action' => 'required|in:delete,mark_read,mark_unread,mark_replied,mark_closed',
            'contact_forms' => 'required|array',
            'contact_forms.*' => 'exists:contact_forms,id'
        ]);

        $contactForms = ContactForm::whereIn('id', $request->contact_forms);
        $count = $contactForms->count();

        if ($request->action === 'delete') {
            $contactForms->delete();
            $message = "Successfully deleted {$count} contact form submissions.";
        } else {
            $status = [
                'mark_read' => 'read',
                'mark_unread' => 'new',
                'mark_replied' => 'replied',
                'mark_closed' => 'closed',
            ][$request->action];

            foreach ($contactForms->get() as $contactForm) {
                $contactForm->update($this->statusAttributes($status, $contactForm));
            }
            $message = "Marked {$count} submissions as {$status}.";
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
