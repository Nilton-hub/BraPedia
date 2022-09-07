<?php

namespace src\websockets;

use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Ratchet\Wamp\WampServerInterface;

class Pusher implements WampServerInterface
{
    private array $subscribedTopics;

    public function __construct()
    {
        $this->subscribedTopics = [];
    }

    /**
     * @return array
     */
    public function getSubscribedTopics(): array
    {
        return $this->subscribedTopics;
    }

    /**
     * @param ConnectionInterface $conn
     * @return void
     */
    function onOpen(ConnectionInterface $conn)
    {
        echo "Conexão aberta: " . date("D, d/m/Y H:i:s") . PHP_EOL;
    }

    /**
     * @param ConnectionInterface $conn
     * @return void
     */
    function onClose(ConnectionInterface $conn): void
    {
        $conn->close();
    }

    /**
     * @param ConnectionInterface $conn
     * @param \Exception $e
     * @return void
     */
    function onError(ConnectionInterface $conn, \Exception $e): void
    {
        echo $e->getTraceAsString();
    }

    /**
     * @param ConnectionInterface $conn
     * @param string $id
     * @param string|Topic $topic
     * @param array $params
     * @return void
     */
    function onCall(ConnectionInterface $conn, $id, $topic, array $params): void
    {
        $conn->callError($id, $topic, 'Você não está permitindo fazer chamadas')->close();
    }

    /**
     * @param ConnectionInterface $conn
     * @param string|Topic $topic
     * @return void
     */
    function onSubscribe(ConnectionInterface $conn, $topic): void
    {
        $this->subscribedTopics[$topic->getId()] = $topic;
    }

    /**
     * @param ConnectionInterface $conn
     * @param string|Topic $topic
     * @return void
     */
    function onUnSubscribe(ConnectionInterface $conn, $topic): void
    {
        unset($this->subscribedTopics[$topic->getId()]);
    }

    /**
     * @param string JSON'ified string we'll receive from ZeroMQ
     */
    public function onBlogEntry($entry)
    {
        $entryData = json_decode($entry, true);
        // If the lookup topic object isn't set there is no one to publish to
        if (!array_key_exists($entryData['category'], $this->subscribedTopics)) {
            return;
        }
        $topic = $this->subscribedTopics[$entryData['category']];
        // re-send the data to all the clients subscribed to that category
        /** @var Topic $topic */
        $topic->broadcast($entryData);
    }

    /**
     * @param ConnectionInterface $conn
     * @param string|Topic $topic
     * @param string $event
     * @param array $exclude
     * @param array $eligible
     * @return void
     */
    function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible): void
    {
//        broadcast($msg, array $exclude = array(), array $eligible = array())
        $topic->broadcast($event, $exclude, $eligible);
    }
}
