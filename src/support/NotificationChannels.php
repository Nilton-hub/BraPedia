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
    private array $comments;
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
     * Para cada registro buscado no banco de dados, traz um NOME, um ID, um USER_ID
     * define as propriedades titles, comments e commentsRepply
     *
     * @return $this
     */
    private function define(): self
    {
        if (!Auth::user()) {
            return $this;
        }
        $articleModel = (new Post())->isUserId($this->user->id());
        $this->titles = (new Model($articleModel))->read(['user_id'], ['title AS channel, id, user_id'])->get();

        $commentsModel = (new Comment())->isUserId($this->user->id());
        $this->comments = (new Model($commentsModel))->read(['user_id'], ['text AS channel, id, user_id'])->get();

        $commentsRepplyModel = (new CommentsReply())->isUserId($this->user->id());
        $this->commentsRepply = (new Model($commentsRepplyModel))->read(['user_id'], ['text AS channel, id, user_id'])->get();
        return $this;
    }

    /**
     * Modifica as propriedades titles, comments e commentsRepply
     *
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

        $this->comments = array_map(function ($e)  {
            $comment = new \StdClass();
            $comment->channel = "comment {$e->channel}";
            $comment->id = $e->id;
            $comment->user_id = $e->user_id;
            return $comment;
        }, $this->comments);

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
        $this->define()->hidrate();
        $channels = [];
        foreach (array_merge($this->titles, $this->comments, $this->commentsRepply) as $data) {
            $channel = implode('_', array_slice(explode(' ', $data->channel), 0, 3));
            $channel = str_replace(' ', '_', $channel);
            $channels[] = "{$channel}_{$data->id}_{$data->user_id}";
        }
        return $channels;
    }

    /**
     * @param string $type or article or comment
     * @param int $id
     * @return string|null
     */
    public function channel(string $type, int $id): ?string
    {
        $channel = '';
        switch ($type) {
            case 'article':
                $db = \Willry\QueryBuilder\DB::table('posts');
                $data = $db->select(['title', 'user_id'])->where("id = :i", ["i" => $id])->first();
                $title = implode('_', array_slice(explode(' ', $data->title), 0, 2));
                $channel = "article_{$title}_{$id}_{$data->user_id}";
                break;
            case 'comment':
                $db = \Willry\QueryBuilder\DB::table('comments');
                $data = $db->select(['text', 'user_id'])->where("id = :i", ["i" => $id])->first();
                $text = implode('_', array_slice(explode(' ', $data->text), 0, 2));
                $channel = "comment_{$text}_{$id}_{$data->user_id}";
                break;
            default:
                $channel = null;
        }
        return $channel;
    }
}
