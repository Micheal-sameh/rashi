<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Google\Service\Drive\Permission;

class GoogleDriveService
{
    protected $client;

    protected $service;

    protected $folderId;

    public function __construct()
    {
        $this->folderId = '1MQUSdtkV519Mv-dr60xzZruoOcVwaEst';

        $this->client = new Client;
        $this->client->setClientId(env('GOOGLE_DRIVE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_DRIVE_CLIENT_SECRET'));
        $this->client->setRedirectUri(env('GOOGLE_DRIVE_REDIRECT_URI'));
        $this->client->setScopes([Drive::DRIVE_FILE]);

        // Set access token from refresh token
        $this->client->setAccessType('offline');
        $this->client->fetchAccessTokenWithRefreshToken(env('GOOGLE_DRIVE_REFRESH_TOKEN'));

        $this->service = new Drive($this->client);
    }

    /**
     * Upload a file to Google Drive
     *
     * @param  string  $fileContent  Binary content of the file
     * @param  string  $fileName  Name for the file
     * @param  string  $mimeType  MIME type of the file
     * @return array File ID and shareable link
     */
    public function uploadFile($fileContent, $fileName, $mimeType = 'application/pdf')
    {
        $file = new DriveFile;
        $file->setName($fileName);
        $file->setParents([$this->folderId]);

        $createdFile = $this->service->files->create($file, [
            'data' => $fileContent,
            'mimeType' => $mimeType,
            'uploadType' => 'multipart',
            'fields' => 'id',
        ]);

        // Make the file publicly accessible
        $permission = new Permission;
        $permission->setType('anyone');
        $permission->setRole('reader');
        $this->service->permissions->create($createdFile->id, $permission);

        // Get shareable link
        $fileId = $createdFile->id;
        $shareableLink = "https://drive.google.com/file/d/{$fileId}/view";

        return [
            'id' => $fileId,
            'link' => $shareableLink,
        ];
    }

    /**
     * Delete a file from Google Drive
     *
     * @param  string  $fileId
     * @return void
     */
    public function deleteFile($fileId)
    {
        try {
            $this->service->files->delete($fileId);
        } catch (\Exception $e) {
            \Log::error('Failed to delete file from Google Drive: '.$e->getMessage());
        }
    }
}
