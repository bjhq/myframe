<?php
/**
 * Created by IntelliJ IDEA.
 * User: harry
 * Date: 2017/3/27
 * Time: 10:51
 */

use Illuminate\Database\Capsule\Manager as Capsule;

// 定义 BASE_PATH

define('BASE_PATH', __DIR__);

// Autoload 自动载入
require PUBLIC_PATH.'/../vendor/autoload.php';

// Eloquent ORM

$capsule = new Capsule;

$capsule->addConnection(require BASE_PATH.'/database.php');

$capsule->bootEloquent();