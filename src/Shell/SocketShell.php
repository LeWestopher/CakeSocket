<?php

namespace CakeSockets\Shell;

use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Event\EventManager;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\Session\SessionProvider;
use Ratchet\Wamp\WampServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory as EventLoopFactory;
use React\Socket\Server as ReactServer;
use CakeSockets\Session\CakeWampSessionHandler;
use CakeSockets\Session\SessionSerializeHandler;
use CakeSockets\Server\WampAppServer;

class SocketShell extends Shell
{
    private $_event_loop = null;

    private $_io_server = null;

    public function start()
    {
        $this->_event_loop = EventLoopFactory::create();

        $websocket = new ReactServer($this->_event_loop);

        $websocket->listen(
            Configure::read('Ratchet.Websocket.connection.port'),
            Configure::read('Ratchet.Websocket.connection.address')
        );

        $this->_io_server = new IoServer(
            new HttpServer(
                new WsServer(
                    new SessionProvider(
                        new WampServer(
                            // Todo: Build WampAppServer
                            new WampAppServer(
                                $this,
                                $this->_event_loop,
                                EventManager::instance(),
                                $this->params['verbose']
                            )
                        ),
                        // Todo: Build CakeWampSession Handler
                        new CakeWampSessionHandler(),
                        [],
                        new SessionSerializeHandler()
                    )
                )
            ),
            $websocket,
            $this->_event_loop
        );

        $this->_event_loop->run();
    }

    // Todo: Build command to stop the websocket server
    public function stop()
    {

    }

    // Todo: build options parser for user friendly shell utility
    public function getOptionsParser()
    {

    }
}