<?php

namespace src\support;

use Gumlet\ImageResize;

class Image
{
    public const ALLOWEDS = ['image/png', 'image/jpg', 'image/jpeg', 'image/gif', 'image/svg'];

    public const MAXSIZE = 500000;

    /** @var null|array Image from upload */
    private ?array $image;

    private ?string $relativePath;

    /** @var string|null Error message */
    private ?string $error;

    /**
     * @param array $image
     */
    public function __construct(?array $image = null)
    {
        $this->image = $image;
    }

    /**
     * @param string $dir
     * @return bool
     * @throws \Gumlet\ImageResizeException
     */
    public function upload(string $dir = CONF_UPLOAD_DIR . '/covers', ?int $w = null, ?int $h = null): bool
    {
        if (empty($this->image)) {
            $this->error = "Você precisa informar os dados da imagem";
            return false;
        }
        if (!in_array($this->image['type'], self::ALLOWEDS)) {
            $this->error = 'Tipo de arquivo não permitido';
            return false;
        }
        $name = time() . '-' . pathinfo(str_replace(' ', '-', mb_convert_case($this->image['name'], MB_CASE_LOWER)), PATHINFO_BASENAME);

        if (!move_uploaded_file($this->image['tmp_name'], "{$dir}/{$name}")) {
            $this->error = 'Não foi possível enviar a imagem.';
            return false;
        }
//        if (!is_null($w) && !is_null($h)) {
            $rsz = new ImageResize("{$dir}/{$name}");
            $rsz->resize($w, $h);
            $rsz->save("{$dir}/{$name}");
//        }
        $this->relativePath = mb_strstr($dir, 'uploads') . "/{$name}";
        return true;
    }

    /**
     * @param string|null $fileName
     * @return void
     */
    public function remove(?string $fileName): void
    {
        if (!empty($fileName)) {
            if (file_exists($fileName) && is_file($fileName)) {
                unlink($fileName);
            }
            return;
        }
        $files = scandir(CONF_UPLOAD_DIR . '/covers');
        foreach ($files as $file) {
            $file = CONF_UPLOAD_DIR . "/covers/{$file}";
            if (file_exists($file) && is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * @return array
     */
    public function image(): array
    {
        return $this->image;
    }

    /**
     * @return string|null
     */
    public function relativePath(): ?string
    {
        return ($this->relativePath ?? null);
    }

    /**
     * @return string|null
     */
    public function error(): ?string
    {
        return ($this->error ?? null);
    }
}
