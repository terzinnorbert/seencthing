<?php

namespace App\Directory;

class Preview
{
    const PREVIEW_EXTENSION = 'jpg';
    const PATH = 'preview';

    /**
     * @param $path
     * @return string
     */
    public static function create($path)
    {
        $previewPath = storage_path(self::PATH.'/'.self::getRandomName(self::PREVIEW_EXTENSION));

        \Image::make($path)->widen(
            800,
            function ($constraint) {
                $constraint->upsize();
            }
        )->save($previewPath);

        return $previewPath;
    }

    /**
     * @param $name
     * @return bool
     */
    public static function isSupported($name)
    {
        $name = explode('.', $name);
        $extension = strtolower(end($name));

        return in_array(
            $extension,
            [
                'png',
                'jpeg',
                'jpg',
            ]
        );

    }

    /**
     * @param $extension
     * @return string
     */
    protected static function getRandomName($extension)
    {
        do {
            $name = str_random(36).'.'.$extension;
        } while (file_exists(storage_path(self::PATH.'/'.$name)));

        return $name;
    }
}