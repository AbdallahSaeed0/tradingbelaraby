<?php

namespace App\Services\Payment\CyberSource;

/**
 * Builds the HTTP Signature headers CyberSource's REST API requires on every request.
 * See: Getting Started with REST (developer.cybersource.com).
 */
class HttpSignatureAuth
{
    public function __construct(
        protected string $merchantId,
        protected string $apiKey,
        protected string $sharedSecret,
    ) {
    }

    /**
     * @param string $method HTTP method (GET/POST)
     * @param string $host Request host, e.g. apitest.cybersource.com
     * @param string $resourcePath e.g. /uc/v1/sessions
     * @param string|null $body Raw JSON request body, null for GET requests
     * @return array<string, string> Headers to send with the request
     */
    public function headers(string $method, string $host, string $resourcePath, ?string $body = null): array
    {
        $method = strtoupper($method);
        $date = gmdate('D, d M Y H:i:s T');

        $signedHeaderNames = ['host', 'date', '(request-target)'];
        $signatureLines = [
            'host: ' . $host,
            'date: ' . $date,
            '(request-target): ' . strtolower($method) . ' ' . $resourcePath,
        ];

        $headers = [
            'Host' => $host,
            'Date' => $date,
            'v-c-merchant-id' => $this->merchantId,
        ];

        if ($body !== null) {
            $digest = 'SHA-256=' . base64_encode(hash('sha256', $body, true));
            $headers['Digest'] = $digest;
            $signedHeaderNames[] = 'digest';
            $signatureLines[] = 'digest: ' . $digest;
        }

        $signedHeaderNames[] = 'v-c-merchant-id';
        $signatureLines[] = 'v-c-merchant-id: ' . $this->merchantId;

        $signature = base64_encode(hash_hmac(
            'sha256',
            implode("\n", $signatureLines),
            base64_decode($this->sharedSecret),
            true
        ));

        $headers['Signature'] = sprintf(
            'keyid="%s", algorithm="HmacSHA256", headers="%s", signature="%s"',
            $this->apiKey,
            implode(' ', $signedHeaderNames),
            $signature
        );

        return $headers;
    }
}
