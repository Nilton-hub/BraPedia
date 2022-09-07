<?php

namespace src\support;

use src\core\Model;
use src\models\Comment;
use src\models\CommentsReply;
use src\models\Post;
use src\models\User;

class NotificationChannels
{
    private array $titles;
    private array $coments;
    private array $commentsRepply;
    private User $user;

    public function __construct()
    {
        if (Auth::user()) {
            $this->user = new User();
            $this->user->isId(Auth::user()->id);
        }
    }

    /**
     * @return $this
     */
    private function create(): self
    {
        if (!Auth::user()) {
            return $this;
        }
        $articleModel = (new Post())->isUserId($this->user->id());
        $this->titles = (new Model($articleModel))->read(['user_id'], ['title AS channel, id, user_id'])->get();

        $commentsModel = (new Comment())->isUserId($this->user->id());
        $this->coments = (new Model($commentsModel))->read(['user_id'], ['text AS channel, id, user_id'])->get();

        $commentsRepplyModel = (new CommentsReply())->isUserId($this->user->id());
        $this->commentsRepply = (new Model($commentsRepplyModel))->read(['user_id'], ['text AS channel, id, user_id'])->get();
        return $this;
    }

    /**
     * @return $this
     */
    private function hidrate(): self
    {
        if (!Auth::user()) {
            return $this;
        }
        $this->titles = array_map(function ($e) {
            $title = new \StdClass();
            $title->channel = "article {$e->channel}";
            $title->id = $e->id;
            $title->user_id = $e->user_id;
            return $title;
        }, $this->titles);

        $this->coments = array_map(function ($e)  {
            $comment = new \StdClass();
            $comment->channel = "comment {$e->channel}";
            $comment->id = $e->id;
            $comment->user_id = $e->user_id;
            return $comment;
        }, $this->coments);

        $this->commentsRepply = array_map(function ($e) {
            $comment = new \StdClass();
            $comment->channel = "commentRepply {$e->channel}";
            $comment->id = $e->id;
            $comment->user_id = $e->user_id;
            return $comment;
        }, $this->commentsRepply);
        return $this;
    }

    /**
     * @return array
     */
    public function channels(): array
    {
        $this->create()->hidrate();
        $channels = [];
        foreach (array_merge($this->titles, $this->coments, $this->commentsRepply) as $data) {
            $channel = implode('_', array_slice(explode(' ', $data->channel), 0, 3));
            $channel = str_replace(' ', '_', $channel);
            $channels[] = "{$channel}_{$data->id}_{$data->user_id}";
        }
        return $channels;
    }
}
