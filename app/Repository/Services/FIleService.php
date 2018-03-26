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
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Facades\JWTAuth;

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
        $path = sprintf("users/data/%s", $user->_id);
        $name = sprintf("%s.%s", $name, $file->getClientOriginalExtension());
        return Storage::disk('cdn')
            ->putFileAs($path, $file, $name);
    }


}