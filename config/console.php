<?php

$config = require_once __DIR__ . '/web.php';

$config['id'] .= '-console';
$config['controllerNamespace'] = 'app\commands';
$config['aliases']['@tests'] = '@app/tests';

unset($config['components']['request']);
unset($config['components']['user']);
unset($config['components']['session']);
unset($config['components']['errorHandler']);
unset($config['components']['log']['traceLevel']);
unset($config['components']['urlManager']);

/*
$config['controllerMap'] = [
    'fixture' => [ // Fixture generation command line.
        'class' => 'yii\faker\FixtureController',
    ],
];
*/

return $config;