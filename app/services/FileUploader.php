<?php
declare(strict_types=1);

namespace App\services;

class FileUploader
{
    private array $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    private array $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
    private int $maxSize = 2097152; // 2 MB
    private array $errors = [];

    public function uploadAvatar(?array $file): ?string
    {
        $this->errors = [];

        if (!$file || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = "File upload failed with error code: " . $file['error'];
            return null;
        }

        if ($file['size'] > $this->maxSize) {
            $this->errors[] = "File size exceeds the 2MB limit.";
            return null;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedExtensions, true)) {
            $this->errors[] = "Invalid image extension. Only JPG, PNG, and WebP images are permitted.";
            return null;
        }

        // Validate genuine MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $this->allowedMimes, true)) {
            $this->errors[] = "Invalid file type ({$mime}). Only genuine image files are permitted.";
            return null;
        }

        // Target storage directory
        $targetDir = UPLOADS_PATH . '/avatars';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Generate safe randomized filename
        $safeName = 'avatar_' . bin2hex(random_bytes(16)) . '.' . $extension;
        $targetFile = $targetDir . '/' . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
            $this->errors[] = "Failed to store uploaded image on server.";
            return null;
        }

        // Return relative path from web root
        return 'uploads/avatars/' . $safeName;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getFirstError(): ?string
    {
        return $this->errors[0] ?? null;
    }
}
