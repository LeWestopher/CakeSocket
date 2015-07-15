<?php

namespace CakeSockets\Session;

use Cake\Core\Configure;
use Cake\Cache\Cache;
use SessionHandlerInterface;


class CakeWampSessionHandler implements SessionHandlerInterface
{

    public function __construct() {
        session_start();
    }

    public function open($save_path, $session_name) {
        return true;
    }

    public function close() {
        return true;
    }

    public function read($session_id) {
        $session_data = Cache::read($session_id, Configure::read('Session.handler.config'));
        session_decode($session_data);
        $restored_session_data = $_SESSION;
        foreach ($_SESSION as $key => $value) {
            unset($_SESSION[$key]);
        }
        return serialize($restored_session_data);
    }

    public function write($session_id, $data) {
        return true;
    }

    public function destroy($session_id) {
        return true;
    }

    public function gc($lifetime) {
        return true;
    }
}