<?php
/**
 * Created by IntelliJ IDEA.
 * User: harry
 * Date: 2017/3/24
 * Time: 15:53
 */

// 定义 PUBLIC_PATH

define('PUBLIC_PATH', __DIR__);
define('APPLICATION_ROOT', dirname(dirname(__FILE__)) .'/');//定义的项目根目录

// 启动器

require PUBLIC_PATH.'/../app/config/bootstrap.php';

// 路由配置、开始处理
require BASE_PATH.'/routes.php';
