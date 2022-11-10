<?php

namespace src\core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class View
{
    /** @var Environment $template */
    private Environment $template;
    private string $ext;

    /**
     * @param string $path
     * @param string $ext
     */
    public function __construct(string $path, string $ext = "twig")
    {
        $this->template = new Environment(new FilesystemLoader($path), [
            'auto_reload' => true,
            'autoescape' => false
        ]);
        $this->ext = $ext;
    }

    /**
     * @param string $view
     * @param array $data
     * @return string
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\SyntaxError
     */
    public function load(string $view, array $data = []): string
    {
        return $this->template->render("{$view}.{$this->ext}", $data);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function addData(string $name, $value): void
    {
        $this->template->addGlobal($name, $value);
    }
}
