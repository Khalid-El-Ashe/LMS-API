<?php

namespace App\Services;

class FileUploadService
{
    public function upload($file, $path): string
    {
        $filename = time() . '_' . uniqid(). '.' . $file->getClientOriginalExtension();
        return $file->storeAs($path, $filename, 'public');
    }

    public function uploadMany(array $files, $path): array
    {
        $uploadedFiles = [];
        foreach ($files as $file) {
            $uploadedFiles[] = $this->upload($file, $path);
        }
        return $uploadedFiles;
    }
}
