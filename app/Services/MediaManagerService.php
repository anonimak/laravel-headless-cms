<?php

namespace App\Services;

class MediaManagerService
{
    // default media directory
    protected $mediaDirectory = 'media';
    // upload media file
    public function uploadMediaFile($file)
    {
        // store the file in the 'media' directory within the 'public' disk
        $path = $file->store($this->mediaDirectory, 'public');
        // return the path to the stored file
        return $path;
    }

    // fetch all in media lists for showing in media manager
    public function getMediaList()
    {
        // get all files in the 'media' directory within the 'public' disk
        $files = \Storage::disk('public')->files($this->mediaDirectory);
        // map the files to their names
        $files = collect($files)->map(function ($file) {
            // return object name and public url of the file
            return (object) [
                'name' => basename($file),
                'url' => \Storage::disk('public')->url($file),
            ];
        })->values()->all();

        return $files;
    }

    // download media file
    public function downloadMediaFile($fileName)
    {
        // get the file from the 'media' directory within the 'public' disk
        $filePath = \Storage::disk('public')->path($this->mediaDirectory . '/' . $fileName);
        // return the file for download
        return $filePath;
    }

    // delete media file
    public function deleteMediaFile($fileName)
    {
        // delete the file from the 'media' directory within the 'public' disk
        \Storage::disk('public')->delete($this->mediaDirectory . '/' . $fileName);
        // return true if the file was deleted successfully
        return !\Storage::disk('public')->exists($this->mediaDirectory . '/' . $fileName);
    }

    // get media file URL
    public function getMediaFileUrl($fileName)
    {
        // get the URL of the file in the 'media' directory within the 'public' disk
        $url = \Storage::disk('public')->url($this->mediaDirectory . '/' . $fileName);
        // return the URL
        return $url;
    }
}
