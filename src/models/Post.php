<?php

namespace src\models;

use src\core\Model as CoreModel;
use stdClass;

class Post implements Model
{
    /** @var string database table */
    private static $entity = 'posts';

    /** @var string[] no update or create in this columns */
    private static array $safe = ['id', 'created_at', 'updated_at'];

    /** @var int|null primary key */
    private ?int $id;

    /** @var int|null foreign key from user */
    private ?int $userId;

    /** @var string|null title column */
    private ?string $title;

    /** @var string|null subtitle column */
    private ?string $subtitle;

    /** @var null|string $cover cover column */
    private ?string $cover;

    /** @var string|null content column */
    private ?string $content;

    /**
     * @return string
     */
    public static function entity(): string
    {
        return self::$entity;
    }

    /**
     * @return string[]
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
     * @return int|null
     */
    public function id(): ?int
    {
        return ($this->id ?? null);
    }

    /**
     * @param int $userId
     * @return $this
     */
    public function isUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function user_id(): ?int
    {
        return ($this->userId ?? null);
    }

    /**
     * @param string $title
     * @return $this
     */
    public function isTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string|null
     */
    public function title(): ?string
    {
        return ($this->title ?? null);
    }

    /**
     * @param string $subtitle
     * @return $this
     */
    public function isSubtitle(string $subtitle): self
    {
        $this->subtitle = $subtitle;
        return $this;
    }

    /**
     * @return string
     */
    public function subtitle(): ?string
    {
        return ($this->subtitle ?? null);
    }

    /**
     * @param string $cover
     * @return $this
     */
    public function isCover(string $cover): self
    {
        $this->cover = $cover;
        return $this;
    }

    /**
     * @return string|null
     */
    public function cover(): ?string
    {
        return ($this->cover ?? null);
    }

    /**
     * @param string $content
     * @return string|null
     */
    public function isContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string|null
     */
    public function content(): ?string
    {
        return ($this->content ?? null);
    }

    /**
     * @return stdClass|null
     */
    public function user(): ?stdClass
    {
        if (!empty($this->userId)) {
            $user = (new User())->isId($this->userId);
            $model = new CoreModel($user);
            return $model->read(['id'])->first();
        }
        return null;
    }
}
