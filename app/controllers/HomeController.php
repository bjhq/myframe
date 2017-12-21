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

    public function getList()
    {
//        $this->checkSchema();
        $array = Customer::get()->toArray();
        echo 1;
        var_dump($array);
        die;
    }

    public function demoExcel()
    {
        $data = Measures::get()->toArray();
        $this->checkSchema();
        $excel = new Excel();
        /** @var 读取excel demo */
        echo "读取excel开始<br/>";
        $url = PUBLIC_PATH . "/test111.xlsx";
        $bodyData = $excel->getBodyData($url,2,true);
        $result = [];
        if (!empty($bodyData)&&!empty($bodyData['data'])){
            foreach ($bodyData['data'] as $key=>$value){
                $result = [
                    'source'=>$value[0],
                    'module'=>$value[1],
                    'type'=>substr($value[2],0,2),
                    'group'=>$value[3],
                    'weight'=>$value[4],
                    'web_name'=>$value[5],
                    'name'=>$value[6],
                    'formula'=>$value[7],
                    'data_way'=>$value[8],
                    'statement'=>$value[9]?$value[9]:'',
                    'data_comment'=>$value[10]?$value[10]:'',
                    'is_delete'=>0,
                    'web_statement'=>$value[11]?$value[11]:'',
                ];

                $a = Measures::insert($result);
                var_dump($a);
            }
        }
        echo "读取excel结束<br/>";



        die;

        /** @var  生成excel demo */
        echo "生成excel开始<br/>";
        $excel->setSheet(1, '标签2');
        $excel->setStyle();
        $this->header = $this->header();
        $url = PUBLIC_PATH . "/hq.xlsx";
        $excel->setheader($this->header(), 1);
        $body = [];
        $excel->writeBody($body);
        $excel->saveExcel($url);
        echo "生成excel结束<br/>";
        die;
    }

    public function body()
    {
        return [
            [1,2,3,4,5,6,7,8],
            [2,2,3,4,5,6,7,8],
            [3,2,3,4,5,6,7,8],
            [4,2,3,4,5,6,7,8],
            [5,2,3,4,5,6,7,8],
            [6,2,3,4,5,6,7,8],
        ];
    }

    public function header()
    {
        $header = [
            'row' => [
                'push' => ['展现量', '排期量'],
                'day' => ['2017-04-06', '2017-04-07', '2017-04-08'],
                'ccc' => ['a', 'b'],
                'asdasd' => ['11', 'aaa'],
                'test' => ['mytest', 'hello', 'word']
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