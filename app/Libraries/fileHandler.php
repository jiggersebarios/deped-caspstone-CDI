<?php

namespace App\Libraries;

use App\Models\FileModel;

class FileHandler
{
    public static function uploadFile($folderId, $categoryId, $file, $userId)
    {
        if (!$file || !$file->isValid()) {
            return ['error' => 'Invalid file upload.'];
        }

        if (!$categoryId) {
            return ['error' => 'Please select a category.'];
        }

        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/files', $newName);

        $fileModel = new FileModel();
        $fileModel->insert([
            'folder_id'   => $folderId,
            'category_id' => $categoryId,
            'file_name'   => $file->getClientName(),
            'file_path'   => $newName,
            'uploaded_by' => $userId,
            'uploaded_at' => date('Y-m-d H:i:s'),
        ]);

        return ['success' => 'File uploaded successfully.'];
    }

    public static function deleteFile($fileId)
    {
        $fileModel = new FileModel();
        $file = $fileModel->find($fileId);

        if (!$file) {
            return ['error' => 'File not found.'];
        }

        $filePath = WRITEPATH . 'uploads/files/' . $file['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $fileModel->delete($fileId);

        return ['success' => 'File deleted successfully.'];
    }

    public static function downloadFile($fileId)
    {
        $fileModel = new FileModel();
        return $fileModel->find($fileId);
    }
}
