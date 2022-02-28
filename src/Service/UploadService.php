<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadService
{
    private $slugger;

    private $uploadFolder = "upload/";

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $newFile, ?string $oldFile = ""): string
    {
        $originalFileName = pathinfo($newFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFileName);
        $fullFilename = $safeFilename . uniqid() . '.' . $newFile->guessExtension();
        $newFile->move($this->uploadFolder, $fullFilename);
        $this->delete($oldFile);
        return $fullFilename;
    }

    public function delete(?string $oldFileName = ""): void
    {
        if ($oldFileName){
            unlink($this->uploadFolder . $oldFileName);
        }
        
    }
}
