<?php

namespace App\Service;

use Gedmo\Sluggable\Util\Urlizer;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    const USER_PROFILE_PIC = 'user_pic';

    private RequestStackContext $requestStackContext;
    private FilesystemOperator $publicUploadsStorage;
    private string $uploadedAssetsBaseUrl;

    public function __construct(FilesystemOperator $publicUploadsStorage, RequestStackContext $requestStackContext, string $uploadedAssetsBaseUrl)
    {
        $this->requestStackContext = $requestStackContext;
        $this->publicUploadsStorage = $publicUploadsStorage;
        $this->uploadedAssetsBaseUrl = $uploadedAssetsBaseUrl;
    }

    public function uploadUserProfilePic(File $file, ?string $oldFilename = null): string
    {
        if ($file instanceof UploadedFile) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        } else {
            $originalFilename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
        }

        $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$file->guessExtension();

        $stream = fopen($file->getPathname(), 'r');
        try {
            $this->publicUploadsStorage->writeStream(
                self::USER_PROFILE_PIC.'/'.$newFilename,
                $stream
            );
        } catch (FilesystemException $e) {
            echo 'write error';
        }
        if (is_resource($stream)) {
            fclose($stream);
        }

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
        
    }

    public function getPublicPath(string $path): string
    {
        return $this->uploadedAssetsBaseUrl.'/'.$path;
    }
}