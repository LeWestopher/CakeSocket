<?php
/**
 * Created by PhpStorm.
 * User: Westopher
 * Date: 7/15/2015
 * Time: 6:40 AM
 */

namespace CakeSockets\Server;

use Cake\Event\EventManager;
use Cake\Event\Event;
use Ratchet\Wamp\WampServerInterface;
use Ratchet\ConnectionInterface as Connection;
use React\EventLoop\LoopInterface;
use CakeSockets\Shell\SocketShell;


class WampAppServer implements WampServerInterface
{
    use PubSubTrait;

    protected $_shell;

    protected $_event_loop;

    protected $_eventManager;

    protected $_topicManager;

    protected $_verbose;

    public function __construct(
        SocketShell $shell,
        LoopInterface $loop,
        EventManager $event_manager,
        $verbose = false
    )
    {
        $this->_shell = $shell;
        $this->_event_loop = $loop;
        $this->_eventManager = $event_manager;
        $this->_verbose = $verbose;

        $this->dispatchEvent(
            'Ratchet.WampServer.construct',
            $this,
            [
                'loop' => $this->_event_loop
            ]
        );
    }

    public function getShell()
    {
        return $this->_shell;
    }

    public function getEventLoop()
    {
        return $this->_event_loop;
    }

    public function getVerbose()
    {
        return $this->_verbose;
    }

    public function onError(Connection $conn, \Exception $e)
    {
        $this->outVerbose(get_class($e) . ' for connection <info>' . $conn->WAMP->sessionId . '</info>: <error>' . $e->getMessage() . '</error>');
        //CakeLog::write('ratchetWampServer', 'Something did not work');
    }

    public function dispatchEvent($event_name, $scope, $params)
    {
        $event = new Event($event_name, $scope, $params);
        $this->outVerbose('Event begin: ' . $event_name);
        $this->eventManager->dispatch($event);
        $this->outVerbose('Event end: ' . $event_name);

        return $event;
    }

    public function outVerbose($message)
    {
        if ($this->_verbose) {
            $time = microtime(true);
            $time = explode('.', $time);
            if (!isset($time[1])) {
                $time[1] = 0;
            }
            $time[1] = str_pad($time[1], 4, 0);
            $time = implode('.', $time);
            $this->_shell->out('[<info>' . $time . '</info>] ' . $message);
        }
    }

    static public function getTopicName($topic)
    {
        if ($topic instanceof \Ratchet\Wamp\Topic) {
            return $topic->getId();
        } else {
            return $topic;
        }
    }
}