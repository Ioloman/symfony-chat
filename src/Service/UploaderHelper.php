<?php

namespace App\Service;

use Gedmo\Sluggable\Util\Urlizer;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    const USER_PROFILE_PIC = 'user_pic';
    const ATTACHMENT = 'attachment';

    private FilesystemOperator $publicUploadsStorage;
    private string $uploadedAssetsBaseUrl;
    private FilesystemOperator $privateUploadsStorage;

    public function __construct(FilesystemOperator $publicUploadsStorage, FilesystemOperator $privateUploadsStorage, string $uploadedAssetsBaseUrl)
    {
        $this->publicUploadsStorage = $publicUploadsStorage;
        $this->uploadedAssetsBaseUrl = $uploadedAssetsBaseUrl;
        $this->privateUploadsStorage = $privateUploadsStorage;
    }

    public function uploadUserProfilePic(File $file, ?string $oldFilename = null): string
    {
        $newFilename = $this->uploadFile($file, self::USER_PROFILE_PIC, $this->publicUploadsStorage);

        if ($oldFilename) {
            try {
                $this->publicUploadsStorage->delete($oldFilename);
            } catch (FilesystemException $e) {
                echo 'delete error';
            }
        }

        return $newFilename;
    }

    public function uploadAttachment(File $file): string
    {
        return $this->uploadFile($file, self::ATTACHMENT, $this->privateUploadsStorage);
    }

    public function getPublicPath(string $path): string
    {
        return $this->uploadedAssetsBaseUrl.'/'.$path;
    }

    /**
     * @param File $file
     * @param string $directory
     * @param FilesystemOperator $storage
     * @return string
     */
    private function uploadFile(File $file, string $directory, FilesystemOperator $storage): string
    {
        if ($file instanceof UploadedFile) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        } else {
            $originalFilename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
        }

        $newFilename = Urlizer::urlize($originalFilename) . '-' . uniqid() . '.' . $file->guessExtension();

        $stream = fopen($file->getPathname(), 'r');
        try {
            $storage->writeStream(
                $directory . '/' . $newFilename,
                $stream
            );
        } catch (FilesystemException $e) {
            echo 'write error';
        }
        if (is_resource($stream)) {
            fclose($stream);
        }
        return $newFilename;
    }
}