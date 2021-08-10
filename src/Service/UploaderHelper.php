<?php

namespace App\Service;

use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    private string $uploadsPath;

    const USER_PROFILE_PIC = 'user_pic';

    public function __construct(string $uploadsPath)
    {
        $this->uploadsPath = $uploadsPath;
    }

    public function uploadUserProfilePic(UploadedFile $uploadedFile): string
    {
        $destination = $this->uploadsPath.'/'.self::USER_PROFILE_PIC;
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$uploadedFile->guessExtension();

        $uploadedFile->move($destination, $newFilename);

        return $newFilename;
    }

    public function getPublicPath(string $path): string
    {
        return '/uploads/'.$path;
    }
}