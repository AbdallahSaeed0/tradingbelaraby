<?php

namespace App\Http\Controllers;

use App\Models\ContactForm;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Minimum seconds between rendering the form and submitting it; bots post instantly.
     */
    private const MIN_FILL_SECONDS = 3;

    /**
     * Store contact form submission.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // Spam is answered like a normal success so bots get no signal, but nothing is saved
        if ($reason = $this->spamReason($request, $data)) {
            Log::info('Contact form submission dropped as spam', [
                'reason' => $reason,
                'ip' => $request->ip(),
                'email' => $data['email'],
            ]);

            return $this->thankYou();
        }

        try {
            // Only the form fields; status/admin fields are never taken from the request
            ContactForm::create($data);

            return $this->thankYou();

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('An error occurred while sending your message. Please try again.')
            ], 500);
        }
    }

    private function thankYou()
    {
        return response()->json([
            'success' => true,
            'message' => __('Thank you for your message. We will get back to you soon!'),
        ]);
    }

    /**
     * Why this submission looks like spam, or null if it looks genuine.
     */
    private function spamReason(Request $request, array $data): ?string
    {
        // Honeypot: hidden from people, filled in by form-filling bots
        if (filled($request->input('website'))) {
            return 'honeypot';
        }

        // Time trap: the form carries an encrypted render time
        try {
            $renderedAt = (int) decrypt((string) $request->input('form_token'));
        } catch (DecryptException $e) {
            return 'missing_or_invalid_token';
        }
        if (now()->timestamp - $renderedAt < self::MIN_FILL_SECONDS) {
            return 'submitted_too_fast';
        }

        // Random-string bots: name and subject are both one long mixed-case token
        if ($this->looksRandom($data['name']) && $this->looksRandom($data['subject'])) {
            return 'random_name_and_subject';
        }

        // Link spam
        if (preg_match_all('~https?://|www\.~i', $data['message']) >= 3) {
            return 'too_many_links';
        }

        return null;
    }

    /**
     * A single Latin "word" of 10+ letters that keeps flipping between upper and lower case,
     * e.g. "XDnogUKqCIRtXTOTnUUpTC". Real names (incl. "McDonald") don't flip this often.
     */
    private function looksRandom(string $value): bool
    {
        $value = trim($value);

        if (!preg_match('/^[A-Za-z]{10,}$/', $value)) {
            return false;
        }

        $caseFlips = 0;
        $chars = str_split($value);
        for ($i = 1; $i < count($chars); $i++) {
            if (ctype_upper($chars[$i]) !== ctype_upper($chars[$i - 1])) {
                $caseFlips++;
            }
        }

        $innerUppercase = preg_match_all('/[A-Z]/', substr($value, 1));

        return $caseFlips >= 5 && $innerUppercase >= 3;
    }
}
