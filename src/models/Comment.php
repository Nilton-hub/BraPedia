<?php

namespace src\models;

use stdClass;

class Comment implements Model
{
    /** @var string $entity database table */
    private static string $entity = 'comments';

    /** @var array|string[] no update or create in this columns */
    private static array $safe = ['id', 'created_at', 'updated_at'];

    /** @var int $id primary key */
    private int $id;

    /** @var int|null foreign key from users */
    private ?int $user_id;

    /** @var int|null foreign key form posts */
    private ?int $post_id;

    /** @var string|null $text column */
    private ?string $text;

    /**
     * @return string
     */
    public static function entity(): string
    {
        return self::$entity;
    }

    /**
     * @return array|string[]
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
        return ($this->id ?? null);
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
     * @return int|null
     */
    public function user_id(): ?int
    {
        return ($this->user_id ?? null);
    }

    /**
     * @param int $postId
     * @return $this
     */
    public function isPostId(int $postId): self
    {
        $this->post_id = $postId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function post_id(): ?int
    {
        return ($this->post_id ?? null);
    }

    /**
     * @param string $text
     * @return $this
     */
    public function isText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return string|null
     */
    public function text(): ?string
    {
        return ($this->text ?? null);
    }

    /**
     * @return stdClass|null
     */
    public function user(array $fields = ["*"]): ?stdClass
    {
        $user = (new User())->isId($this->user_id());
        return (new \src\core\Model($user))->read(["id"], $fields)->first();
    }

    /**
     * @return array|null
     */
    public function responses(): ?array
    {
        $responseModel = (new CommentsReply())->isCommentId($this->id);
        $responses = (new \src\core\Model($responseModel))->read(["comment_id"])->get();
        foreach ($responses as $response) {
            $userModel = new User();
            $userModel->isId($response->user_id);
            $response->user_name = (new \src\core\Model($userModel))->read(['id'], ['name'])->first()->name;
        }
        return $responses;
    }
}
