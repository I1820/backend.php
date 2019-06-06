<?php
/**
 * Created by PhpStorm.
 * User: Sajjad Rahnama
 * Date: 15/12/17
 * Time: 11:59 PM
 */

namespace App\Repository\Services;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileService
{

    /**
     * @param string $name
     * @param UploadedFile $file
     * @return string
     */
    public function saveFile($name, $file): string
    {
        $user = Auth::user();
        $path = sprintf("users/%s", $user->_id);
        $name = sprintf("%s.%s", $name, $file->getClientOriginalExtension());
        return Storage::disk('public')
            ->putFileAs($path, $file, $name);
    }


    /**
     * @param UploadedFile $file
     * @return string
     */
    public function savePicture($file): string
    {
        $user = Auth::user();
        $path = sprintf("users/%s", $user->_id);
        $name = sprintf("picture.%s", $file->getClientOriginalExtension());
        return Storage::disk('public')
            ->putFileAs($path, $file, $name);
    }


}