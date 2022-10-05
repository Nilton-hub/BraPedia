<?php

namespace src\controllers;

use src\core\Model;
use src\models\Comment;
use src\models\CommentsReply;
use src\models\Post;

class Application
{
    /**
     * @param array $data
     * @return void
     */
    public function toHiddenArticle(array $data): void
    {
        if (isset($data['id'])) {
            $id = filter_var($data['id'], FILTER_VALIDATE_INT);
            $article = (new Post())->isId($id);
            $currentStatus = (new Model($article))->read(['id'], ['hidden'])->first()->hidden;
            $status = ($currentStatus == 1 ? '0' : '1');
            $modelUpdate = (new Model($article))->update('id = :id', ['id' => $id], ['hidden' => $status]);
            echo json_encode(['success' => $modelUpdate]);
            return;
        }
        echo json_encode(['success' => 0]);
    }

    /**
     * @return void
     */
    public function commentAction(): void
    {
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
        $id = filter_var($post['id'], FILTER_VALIDATE_INT);
        if ($post) {
            $comment = (new Comment())->isId($id);
            $comentModel = (new Model($comment))->read(['id'])->first();
            if (is_null($comentModel)) {
                echo json_encode(['error' => 'comentário inexistente.']);
                return;
            }
            if ($post['action'] === 'edit') {
                $comentModel = (new Model($comment))->update('id = :n', ['n' => $post['id']], ['text' => $post['text']]);
                echo json_encode(['success' => 'comentário atualizado.']);
                return;
            }
            if ($post['action'] === 'delete') {
                $comentModel = (new Model($comment))->delete('id = :n', ['n' => $post['id']]);
                echo json_encode(['success' => 'comentário deletado.']);
                return;
            }
        }
    }

    public function replyActions(): void
    {
        $id = filter_input(INPUT_POST, 'repply_id', FILTER_SANITIZE_NUMBER_INT);
        $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
        if (!$id) {
            echo json_encode(['error' => true, 'describe' => 'id inexistente']);
            return;
        }
        if ($action === 'delete') {
            $repply = (new CommentsReply())->isId($id);
            if ((new Model($repply))->delete('id = :i', ['i' => $id])) {
                echo json_encode(['success' => true]);
                return;
            }
        }
        if ($action === 'edit') {
            $text = filter_input(INPUT_POST, 'text', FILTER_SANITIZE_SPECIAL_CHARS);
            $repply = (new CommentsReply())->isId($id)->isText($text);
            if ((new Model($repply))->update('id = :n', ['n' => $id], ['text' => $text])) {
                echo json_encode(['success' => true]);
                return;
            }
        }
        echo json_encode(['success' => false]);
    }
}
