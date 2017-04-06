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

    protected $header;

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

    public function demoExcel()
    {
        $this->checkSchema();
        $excel = new Excel();

        /** @var 读取excel demo */
//        echo "读取excel开始<br/>";
//        $url = PUBLIC_PATH . "/New Card2017-03-30.xlsx";
//        $aaa = $excel->getBodyData($url, 3 ,PUBLIC_PATH);
//        var_dump($aaa);
//        echo "读取excel结束<br/>";

        /** @var  生成excel demo */
        echo "生成excel开始<br/>";
        $this->header = $this->header();
        $url = PUBLIC_PATH . "/hqqgggqq444.xlsx";
        $excel->setheader($this->header(), 1,$url);
        echo "生成excel结束<br/>";
        die;
    }

    public function header()
    {
        $header = [
            'row' => [
                'push' => ['展现量', '排期量'],
                'day' => ['2017-04-06', '2017-04-07', '2017-04-08'],
                'ccc' => ['a', 'b'],
                'asdasd'=>['11','aaa'],
                'test'=>['mytest','hello','word']
            ],
            'col' => [
                '平台',
                '媒体',
                '对接方式',
                '时长',
                '市场',
                '频道'
            ],
        ];
        return $header;
    }


    /**
     * @name getTimeDay
     * 通过开始和结束时间计算一个包含所有时间的数据 非四舍五入舍弃取整
     * @param $startTime
     * @param $endTime
     * @return array
     */
    function getTimeDayByFloor($startTime, $endTime)
    {
        $day = floor(($endTime - $startTime) / 86400);
        $timeZone = [];
        for ($i = 0; $i <= $day; $i++) {
            $timeZone[$i] = date('Y-m-d', strtotime("+$i day", $startTime));
        }
        return $timeZone;
    }

}