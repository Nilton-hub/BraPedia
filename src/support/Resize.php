<?php

namespace src\support;

class Resize
{
    /** @var string  */
    private string $image;

    public function __construct(string $image)
    {
        $this->image = $image;
    }

    /**
     * @param string $path
     * @param int|null $w
     * @param int|null $h
     * @return void
     */
    public function resize(string $path ,int $w = null, int $h = null): void
    {
        if ($w === null && $h === null) {
            throw new \ArgumentCountError('Informe pelomenos um dos parâmetros.');
        }
        $img = $this->image;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $type = ($type === 'jpg' ? 'jpeg' : $type);
        $imageCreateFunc = "imagecreatefrom{$type}";
        $imageCreate = $imageCreateFunc($img);
        [$width, $height] = getimagesize($img);
        if (!is_null($w) && is_null($h)) {
            $h = ($w * $height) / $width;
        }
        if (is_null($w) && !is_null($h)) {
            $w = ($h * $width) / $height;
        }
        $newImage = imagecreatetruecolor($width, $height);
        imagecopyresampled($newImage, $imageCreate, 0, 0, 0, 0, $w, $h, $width, $height);
        if ($type === 'jpeg') {
            imagejpeg($newImage, $path, 76);
        }
        if ($type === 'png') {
            imagepng($newImage, $path, 5);
        }
        if ($type === 'gif') {
            imagegif($newImage, $path);
        }
    }

    /**
     * @return string
     */
    private function getType(): string
    {
        if (pathinfo($this->image, PATHINFO_EXTENSION) === 'png') {
            return 'png';
        }
        if (pathinfo($this->image, PATHINFO_EXTENSION) === 'jpeg') {
            return 'jpeg';
        }
        if (pathinfo($this->image, PATHINFO_EXTENSION) === 'gif') {
            return 'gif';
        }
        return '';
    }
}
