<?php
/**
 * Created by IntelliJ IDEA.
 * User: harry
 * Date: 2017/3/27
 * Time: 14:53
 */

namespace libraries;

class My_Excel
{
    /**
     * @var \PHPExcel
     */
    protected $excelObj;

    /**
     * @var 下载时文件名
     */
    protected $fileName;


    /**
     * My_Excel constructor.
     */
    public function __construct()
    {
        $this->excelObj = new \PHPExcel();
    }

    /**
     * 设置下载时文件名
     *
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName . '.xlsx';
    }

    /**
     * 设置标题
     *
     * @param $title
     */
    public function setTitle($title)
    {
        $this->excelObj->getActiveSheet()->setTitle($title);
    }


    /**
     * 获取主体数据
     *
     * @param $excelUrl 读取的excel的位置
     * @param int|定位 $row 定位 从第几行开始读取
     *
     * @return mixed
     */
    public function getBodyData($excelUrl, $row = 1, $path)
    {
        $inputFileType = \PHPExcel_IOFactory::identify($excelUrl);
        $reader = \PHPExcel_IOFactory::createReader($inputFileType);
        $PHPExcel = $reader->load($excelUrl); // 载入excel文件
        $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        $highestColumm = $sheet->getHighestColumn(); // 取得总列数字母
        $cols = \PHPExcel_Cell::columnIndexFromString($highestColumm); //获取总列数数字

        /*****
         * 测试代码开始
         */
        $aaa = $PHPExcel->getActiveSheet()->getMergeCells();

        foreach ($aaa as $key => $value) {
            $PHPExcel->getActiveSheet()->unmergeCells($value);
        }


        //循环读取每个单元格的数据
        $data = [];
        for ($row; $row <= $highestRow; $row++) {
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumm . $row, NULL, TRUE, FALSE);
            $data[] = $rowData[0];
        }
        $result = $this->unsetNull($this->unsetNull($data));
        var_dump($result);
        die;
        if ($result) {
            $returnData = [
                'rowNum' => $cols,
                'data' => $result,
            ];
            return $returnData;
        }
        return $result;
    }

    public function test($excelUrl, $rowTitle, $columnTitle = 0)
    {
        $inputFileType = \PHPExcel_IOFactory::identify($excelUrl);
        $reader = \PHPExcel_IOFactory::createReader($inputFileType);
        $PHPExcel = $reader->load($excelUrl); // 载入excel文件
        $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        $highestColumm = $sheet->getHighestColumn(); // 取得总列数字母
        $cols = \PHPExcel_Cell::columnIndexFromString($highestColumm); //获取总列数数字

        $aaa = $PHPExcel->getActiveSheet()->getMergeCells();

        foreach ($aaa as $key => $value) {
            $PHPExcel->getActiveSheet()->unmergeCells($value);
        }

        //循环读取每个单元格的数据
        $data = [];
        for ($row = 1; $row <= $highestRow; $row++) {
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumm . $row, NULL, TRUE, FALSE);
            $data[] = $rowData[0];
        }
        echo 111;
        var_dump($data);
        die;
        return $data;
    }

    public function getValue()
    {
        for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
        for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
            if (trim($data->sheets[0]['cells'][$i][$j]) == "") {//是值为空的合并单元格，更具需要判断
                for ($k = $i - 1; $k >= 0; $k--) {
                    if (trim($data->sheets[0]['cells'][$k][$j]) != "") {
                        $data->sheets[0]['cells'][$i][$j] = $data->sheets[0]['cells'][$k][$j];
                        break;
                    }
                }
                //这里可以进一步处理！
            }
            echo "\"" . $data->sheets[0]['cells'][$i][$j] . "\",";
        }
        echo "<br/>";
    }
    }


    /**
     * 设置sheet和标题
     *
     * @param int $num
     * @param string $title
     *
     * @throws PHPExcel_Exception
     */
    public function setSheet($num = 0, $title = 'sheet')
    {
        if ($num > 0) {
            $objWorksheet1 = $this->excelObj->createSheet();
            $objWorksheet1->setTitle($title);
            $this->excelObj->setActiveSheetIndex($num);
        } else {
            $this->excelObj->getActiveSheet()->setTitle($title);
        }
        $this->sheet = $num;
    }

    /**
     * 多维数组null替换为 ''
     *
     * @param $result
     *
     * @return mixed
     */
    private function unsetNull($result)
    {
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $status = is_array($value) && !empty($value);
                $statusValue = is_null($value) || empty($value);
                if ($status) {
                    $result[$key] = $this->unsetNull($value);
                }
                if ($statusValue) {
                    unset($result[$key]);
                }
            }
            unset($status, $statusValue);
        }
        return $result;
    }


    /**
     * 找到数组中所有可能的指定长度的组合，要求没有重复。
     *
     * @param array $arr 要组合的数组
     * @param int $m 按m个元素取值
     * @param string $sign 分割符
     *
     * @return array
     */
    private function getCombinationToString($arr, $m, $sign = ',')
    {
        $result = array();
        //单独取数据
        if ($m == 1) {
            return $arr;
        }
        //当m等于数组count大小时直接取
        if ($m == count($arr)) {
            $result[] = implode($sign, $arr);
            return $result;
        }

        $firstelement = $arr[0];
        unset($arr[0]);
        $arr = array_values($arr);
        $list1 = $this->getCombinationToString($arr, ($m - 1));

        foreach ($list1 as $s) {
            $s = $firstelement . $sign . $s;
            $result[] = $s;
        }
        unset($list1);
        $list2 = $this->getCombinationToString($arr, $m);

        foreach ($list2 as $s) {
            $result[] = $s;
        }
        unset($list2);

        return $result;
    }
}