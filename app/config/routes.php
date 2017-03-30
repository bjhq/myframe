<?php
/**
 * Created by IntelliJ IDEA.
 * User: harry
 * Date: 2017/3/24
 * Time: 15:54
 */


use libraries\My_Macaw as Macaw;

Macaw::get('', 'HomeController@index');

Macaw::get('(:all)', function($fu) {
    echo '未匹配到路由<br>'.$fu;
});

Macaw::post('', 'HomeController@my');

Macaw::post('/excel', 'HomeController@testExcel');

Macaw::dispatch();