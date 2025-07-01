<?php

namespace App\Services;

class InputSanitizerService
{
    /**
     * Sanitize string input to prevent XSS and injection attacks
     */
    public function sanitizeString(string $input, bool $allowHtml = false): string
    {
        // Trim whitespace
        $input = trim($input);

        if (!$allowHtml) {
            // Remove all HTML tags
            $input = strip_tags($input);
            // Encode special characters
            $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        } else {
            // Allow only safe HTML tags
            $allowedTags = '<p><br><strong><em><ul><ol><li>';
            $input = strip_tags($input, $allowedTags);
            // Don't encode when allowing HTML
        }

        return $input;
    }

    /**
     * Sanitize email input
     */
    public function sanitizeEmail(string $email): string
    {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    /**
     * Sanitize URL input
     */
    public function sanitizeUrl(string $url): string|false
    {
        $url = trim($url);

        // Add protocol if missing
        if (!preg_match('/^https?:\/\//', $url)) {
            $url = 'https://' . $url;
        }

        return filter_var($url, FILTER_SANITIZE_URL);
    }

    /**
     * Sanitize phone number input
     */
    public function sanitizePhone(string $phone): string
    {
        // Remove all non-numeric characters except +, -, (, ), and spaces
        return preg_replace('/[^0-9\+\-\(\)\s]/', '', trim($phone));
    }

    /**
     * Sanitize business registration number
     */
    public function sanitizeBusinessRegNumber(string $regNumber): string
    {
        // Allow only alphanumeric characters and hyphens
        return preg_replace('/[^a-zA-Z0-9\-]/', '', trim($regNumber));
    }

    /**
     * Sanitize address components
     */
    public function sanitizeAddress(array $address): array
    {
        $sanitized = [];

        foreach ($address as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = $this->sanitizeString($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Validate and sanitize text area input
     */
    public function sanitizeTextArea(string $input, int $maxLength = 1000): string
    {
        $input = trim($input);

        // Remove script tags and other dangerous content
        $input = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i', '', $input);
        $input = preg_replace('/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/i', '', $input);

        // Strip most HTML but allow basic formatting
        $allowedTags = '<p><br><strong><em>';
        $input = strip_tags($input, $allowedTags);

        // Limit length
        if (strlen($input) > $maxLength) {
            $input = substr($input, 0, $maxLength);
        }

        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Check for suspicious patterns in input
     */
    public function containsSuspiciousContent(string $input): bool
    {
        $suspiciousPatterns = [
            '/\b(eval|exec|system|shell_exec|passthru|base64_decode)\s*\(/i',
            '/<script/i',
            '/javascript:/i',
            '/on\w+\s*=/i', // event handlers like onclick=
            '/\$\s*\{/i', // template literals
            '/\b(SELECT\s+.*\s+FROM|INSERT\s+INTO|UPDATE\s+.*\s+SET|DELETE\s+FROM|DROP\s+TABLE|CREATE\s+TABLE|ALTER\s+TABLE)\b/i', // More specific SQL patterns
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sanitize array of inputs recursively
     */
    public function sanitizeArray(array $inputs, bool $allowHtml = false): array
    {
        $sanitized = [];

        foreach ($inputs as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeArray($value, $allowHtml);
            } elseif (is_string($value)) {
                $sanitized[$key] = $this->sanitizeString($value, $allowHtml);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }
}
