<?php

namespace App\Helper;

use Carbon\Carbon;
use Storage;
use Image;

class ImageHelper
{
    public static function saveImage($file, $max_width = 1280, $max_height = 0)
    {
        if (!empty($file)) {
            if ($file->isValid()) {
                if (is_string($file)) {
                    $path = 'uploads/' . date('Y-m') . '/' . date('d') . '/' . time() . str_randon(8) . '.jpg';
                    $img = Image::make($file);
                } else {
                    $path = $file->store('uploads/' . date('Y-m') . '/' . date('d'), 'public');
                    $filePath = Storage::disk('public')->get($path);
                    $img = Image::make($filePath);
                }

                if ($max_width > 0 && $max_height == 0) {
                    $img->resize($max_width, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                } else {
                    $img->fit($max_width, $max_height);
                }
                Storage::disk('public')->put($path, $img->encode());
                return $path;
            }
        }
    }
}