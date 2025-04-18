<?php

class ImageUploadAction
{

    private string $destinationDir = "uploads/";
    private array $allowedTypes = ['image/jpeg', 'image/png'];
    private int $maxSizeInBytes = 2_000_000;

    public function execute(array $file): string
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new RuntimeException('Invalid parameters.');
        }
        // https://www.php.net/manual/en/filesystem.constants.php#constant.upload-err-cant-write
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('File size exceeds limit.');
            default:
                throw new RuntimeException('Unknown upload error.');
        }
        if ($file['size'] > $this->maxSizeInBytes) {
            throw new RuntimeException('Exceeded file size limit of ' . $this->maxSizeInBytes . ' bytes.');
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, $this->allowedTypes)) {
            throw new RuntimeException('Invalid file type: ' . $mimeType);
        }

        if (strpos($mimeType, 'image/') === 0 && !getimagesize($file['tmp_name'])) {
            throw new RuntimeException('File is not a valid image.');
        }

        if (!is_dir($this->destinationDir)) {
            mkdir($this->destinationDir, 0755, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'upload_' . uniqid() . '.' . $ext; // to prevent direct file uploading, i used unique id for the image
        $destination = $this->destinationDir . '/' . $filename;


        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new RuntimeException('Failed to move uploaded file.');
        }

        return $destination;
    }
}
