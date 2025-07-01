<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class FileValidationService
{
    /**
     * Validate PDF file upload
     */
    public function validatePdfFile(UploadedFile $file): array
    {
        $errors = [];

        // Check file exists and is valid
        if (!$file->isValid()) {
            $errors[] = 'The uploaded file is invalid.';
            return $errors;
        }

        // Check file size (10MB max)
        if ($file->getSize() > 10485760) {
            $errors[] = 'The PDF file must not exceed 10MB.';
        }

        // Check MIME type
        $allowedMimes = ['application/pdf'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            $errors[] = 'Only PDF files are allowed.';
        }

        // Check file extension
        if (strtolower($file->getClientOriginalExtension()) !== 'pdf') {
            $errors[] = 'The file must have a .pdf extension.';
        }

        // Validate PDF header
        if (!$this->isValidPdfFile($file)) {
            $errors[] = 'The file must be a valid PDF document.';
        }

        // Check for malicious content patterns
        if ($this->containsSuspiciousContent($file)) {
            $errors[] = 'The uploaded file contains suspicious content.';
        }

        return $errors;
    }

    /**
     * Check if file is a valid PDF by reading header
     */
    private function isValidPdfFile(UploadedFile $file): bool
    {
        try {
            $handle = fopen($file->getRealPath(), 'r');
            if (!$handle) {
                return false;
            }

            $header = fread($handle, 5);
            fclose($handle);

            return $header === '%PDF-';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check for suspicious content in the file
     */
    private function containsSuspiciousContent(UploadedFile $file): bool
    {
        try {
            // Read first 1024 bytes to check for suspicious patterns
            $handle = fopen($file->getRealPath(), 'r');
            if (!$handle) {
                return false;
            }

            $content = fread($handle, 1024);
            fclose($handle);

            // Check for suspicious patterns
            $suspiciousPatterns = [
                '<?php',
                '<script',
                'javascript:',
                'eval(',
                'exec(',
                'system(',
                'shell_exec(',
                'passthru(',
                'base64_decode(',
            ];

            foreach ($suspiciousPatterns as $pattern) {
                if (stripos($content, $pattern) !== false) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            // If we can't read the file, consider it suspicious
            return true;
        }
    }

    /**
     * Generate secure filename for uploaded file
     */
    public function generateSecureFilename(string $prefix, int $userId, string $extension = 'pdf'): string
    {
        return sprintf(
            '%s_%d_%d_%s.%s',
            $prefix,
            $userId,
            time(),
            bin2hex(random_bytes(8)),
            $extension
        );
    }

    /**
     * Store file securely
     */
    public function storeFileSecurely(UploadedFile $file, string $directory, string $filename): string|false
    {
        try {
            // Ensure the directory exists and is properly configured
            $path = $file->storeAs($directory, $filename, 'private');

            if (!$path) {
                return false;
            }

            // Verify the file was stored correctly
            if (!Storage::disk('private')->exists($path)) {
                return false;
            }

            return $path;
        } catch (\Exception $e) {
            logger()->error('File storage failed', [
                'filename' => $filename,
                'directory' => $directory,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Delete file securely
     */
    public function deleteFileSecurely(string $path): bool
    {
        try {
            if (Storage::disk('private')->exists($path)) {
                return Storage::disk('private')->delete($path);
            }
            return true; // File doesn't exist, consider it deleted
        } catch (\Exception $e) {
            logger()->error('File deletion failed', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }
}
