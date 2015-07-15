<?php
use Cake\Routing\Router;

Router::plugin('CakeSockets', function ($routes) {
    $routes->fallbacks('InflectedRoute');
});
