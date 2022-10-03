<?php

namespace src\models;

/**
 * @author <tvirapegubeco@gmail.com> Nilton duarte
 * @package src\models\Notification
 */
class Notification implements Model
{
    /** @var string $entity database table */
    private static string $entity = 'notification';

    /** @var array|string[] no update or create in this columns */
    private static array $safe = ['id', 'created_at', 'updated_at'];

    /** @var ?int $id primary key */
    private ?int $id;
    private ?int $user_id;
    private ?string $url;
    private ?string $photo;
    private ?string $username;
    private ?string $msg;
    private ?string $content;
    private ?bool $view;

    /**
     * @return string
     */
    public static function entity(): string
    {
        return self::$entity;
    }

    /**
     * @return array
     */
    public static function safe(): array
    {
        return self::$safe;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function isId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function id(): ?int
    {
        return $this->id ?? null;
    }

    /**
     * @return int|null
     */
    public function user_id(): ?int
    {
        return $this->user_id ?? null;
    }

    /**
     * @param int|null $user_id
     * @return Notification
     */
    public function isUserId(?int $user_id): Notification
    {
        $this->user_id = $user_id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function url(): ?string
    {
        return $this->url ?? null;
    }

    /**
     * @param string|null $url
     * @return Notification
     */
    public function isUrl(?string $url): self
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string|null
     */
    public function photo(): ?string
    {
        return $this->photo ?? null;
    }

    /**
     * @param string|null $photo
     * @return Notification
     */
    public function isPhoto(?string $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    /**
     * @return string|null
     */
    public function username(): ?string
    {
        return $this->username ?? null;
    }

    /**
     * @param string|null $username
     * @return Notification
     */
    public function isUsername(?string $username): self
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string|null
     */
    public function msg(): ?string
    {
        return $this->msg ?? null;
    }

    /**
     * @param string|null $msg
     * @return Notification
     */
    public function isMsg(?string $msg): self
    {
        $this->msg = $msg;
        return $this;
    }

    /**
     * @return string|null
     */
    public function content(): ?string
    {
        return $this->content ?? null;
    }

    /**
     * @param string|null $content
     * @return Notification
     */
    public function isContent(?string $content): Notification
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function view(): ?bool
    {
        return $this->view ?? null;
    }

    /**
     * @param bool|null $view
     * @return Notification
     */
    public function isView(?bool $view): Notification
    {
        $this->view = $view;
        return $this;
    }
}
