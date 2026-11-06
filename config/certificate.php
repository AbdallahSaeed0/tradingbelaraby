<?php

return [

    /*
    | Prefix of the certificate number: TBA-2026-C35-000123
    */
    'number_prefix' => env('CERTIFICATE_PREFIX', 'TBA'),

    /*
    | Base URL printed on the certificate and encoded in the QR code.
    | The verify page lives at {verify_base_url}/{certificate_number}.
    | Defaults to APP_URL/verify.
    */
    'verify_base_url' => env('CERTIFICATE_VERIFY_URL'),

    /*
    | Signature block defaults (used when the course has no instructor).
    */
    'default_instructor_name' => env('CERTIFICATE_INSTRUCTOR_NAME', 'مها سعيد'),
    'default_instructor_title' => env('CERTIFICATE_INSTRUCTOR_TITLE', 'MFTA · CFTe · CETA'),

    /*
    | Headless Chrome / Chromium binary used to turn the certificate HTML into a PDF.
    | Leave empty to auto-detect. If no browser is found, the certificate is stored
    | as HTML and the student prints it to PDF from their own browser (same design).
    */
    'chrome_path' => env('CERTIFICATE_CHROME_PATH'),

    // Seconds to wait for Chrome before giving up.
    'chrome_timeout' => (int) env('CERTIFICATE_CHROME_TIMEOUT', 60),
];
