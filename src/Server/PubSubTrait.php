<?php
/**
 * Created by PhpStorm.
 * User: Westopher
 * Date: 7/15/2015
 * Time: 7:07 AM
 */

namespace CakeSockets\Server;

use Ratchet\ConnectionInterface as Conn;


trait PubSubTrait
{
    abstract function getTopicName($topic);

    abstract function dispatchEvent($event_name, $subject, $payload);

    protected $_topics = [];

    protected $_subscribers = [];

    public function broadcast($topic, array $payload)
    {
        $topic_name = $this->getTopicName($topic);

        if (isset($this->_topics[$topic_name]['topic'])) {
            $this->_topics[$topic_name]['topic']->broadcast($payload);

            $this->dispatchEvent(
                'Ratchet.WampServer.broadcast',
                $this,
                [
                    'topic_name' => $topic_name,
                    'payload' => $payload
                ]
            );
        }
    }

    public function onPublish(Connection $conn, $topic, $event, array $exclude = [], array $eligible = [])
    {
        if ($topic instanceof \Ratchet\Wamp\Topic) {
            $topic->broadcast($event, $exclude, $eligible);
        }

        $topic_name = self::getTopicName($topic);

        $event_payload = [
            'topic_name' => $topic_name,
            'connection' => $conn,
            'event' => $event,
            'exclude' => $exclude,
            'eligible' => $eligible,
            'wamp_server' => $this,
            'connection_data' => $this->connections[$conn->WAMP->sessionId]
        ];

        $this->dispatchEvent('Ratchet.WampServer.onPublish', $this, $event_payload);
        $this->dispatchEvent('Ratchet.WampServer.onPublish.' . $topic_name, $this, $event_payload);
    }

    public function onSubscribe(Connection $conn, $topic)
    {

    }

    public function onUnSubscribe(Connection $conn, $topic)
    {

    }
}