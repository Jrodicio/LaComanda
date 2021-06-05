<?php
    require_once __DIR__ . '/../vendor/autoload.php';
    use Illuminate\Database\Capsule\Manager as Capsule;
    
    $capsule = new Capsule();

    $capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'remotemysql.com:3306',
    'database'  => 'Q2VBpO03Vq',
    'username'  => 'Q2VBpO03Vq',
    'password'  => '8wTUfrR8U4',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => ''
    ]);

    echo "QUE PASO";
    $capsule->bootEloquent();
?>