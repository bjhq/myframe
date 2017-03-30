<?php
/**
 * Created by IntelliJ IDEA.
 * User: harry
 * Date: 2017/3/24
 * Time: 16:17
 */
use libraries\My_Excel as Excel;

class HomeController extends \core\MY_Controller
{
    public function __construct()
    {
        parent::__construct();

    }

    public function index()
    {
        $this->checkSchema();
        $array = Customer::get()->toArray();
        var_dump($array);
        die;
    }

    public function my()
    {
        $this->checkSchema();
        $excel = new Excel();
        $url = PUBLIC_PATH . "/2017-03-30.xlsx";
        $aaa = $excel->getBodyData($url, 1 ,PUBLIC_PATH);

        $array = Customer::get()->toArray();
        var_dump($array);
        die;
    }


    public function testExcel()
    {
        echo 111;die;

    }


}