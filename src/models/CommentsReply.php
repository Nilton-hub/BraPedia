<?php

namespace src\models;

class CommentsReply implements Model
{
    /** @var string $entity database table */
    private static string $entity = 'comments_reply';

    /** @var array|string[] $safe no update or create in this columns */
    private static array $safe = ['id', 'created_at', 'updated_at'];

    /** @var int|null $id primary key */
    private ?int $id;

    /** @var null|int $user_id foreign key from users */
    private ?int $user_id;

    /** @var null|int $comment_id foreign key for comments */
    private ?int $comment_id;

    /** @var null|string $text column */
    private ?string $text;

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
     * @param string $id
     * @return $this
     */
    public function isId(string $id): self
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
     * @return null|int
     */
    public function user_id(): ?int
    {
        return ($this->user_id ?? null);
    }

    /**
     * @param int $user_id
     * @return $this
     */
    public function isUserId(int $user_id): self
    {
        $this->user_id = $user_id;
        return $this;
    }

    /**
     * @return null|int
     */
    public function comment_id(): ?int
    {
        return ($this->comment_id ?? null);
    }

    /**
     * @param int $comment_id
     * @return $this
     */
    public function isCommentId(int $comment_id): self
    {
        $this->comment_id = $comment_id;
        return $this;
    }

    /**
     * @return null|string
     */
    public function text(): ?string
    {
        return ($this->text ?? null);
    }

    /**
     * @param null|string $text
     * @return $this
     */
    public function isText(string $text): self
    {
        $this->text = $text;
        return $this;
    }
}
