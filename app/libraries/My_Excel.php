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
     * @var 设置sheet
     */
    protected $sheet = 0;

    /**
     * @var 设置sheet
     */
    protected $headerNum = 0;

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

    /** ##############################---设置excel样式---开始---######################################### */
    public function setStyle()
    {
        $this->excelObj->getActiveSheet()->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
    }
    /** ##############################---设置excel样式---结束---######################################### */
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


    /** ##################################---获取excel数据----开始---##################################### */
    /**
     * 获取主体数据(注意 excel不能有null值 否则改区的值会被替换紧邻的值)
     *
     * @param $excelUrl 读取的excel的位置
     * @param int|定位 $row 定位 从第几行开始读取
     *
     * @return mixed
     */
    public function getBodyData($excelUrl, $row = 1, $isValue = false)
    {
        $inputFileType = \PHPExcel_IOFactory::identify($excelUrl);
        $reader = \PHPExcel_IOFactory::createReader($inputFileType);
        $PHPExcel = $reader->load($excelUrl); // 载入excel文件
        $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        $highestColumm = $sheet->getHighestColumn(); // 取得总列数字母
        $cols = \PHPExcel_Cell::columnIndexFromString($highestColumm); //获取总列数数字

        //循环读取每个单元格的数据
        $data = [];
        for ($row; $row <= $highestRow; $row++) {
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumm . $row, null, true, false);
            $data[] = $rowData[0];
        }

        $result = $this->getMergeValue($data,$isValue);
        if ($result) {
            $returnData = [
                'rowNum' => $cols,
                'data' => $result,
            ];
            return $returnData;
        }
        return $result;
    }

    /**
     * 取得合并取得值
     *
     * @param $data
     * @return array
     */
    private function getMergeValue($data,$isValue)
    {
        $result = [];
        foreach ($data as $dataKey => $dataValue) {
            foreach ($dataValue as $key => $value) {
                if (!$isValue){
                    $result[$dataKey][$key] = (is_null($dataValue[$key]) && $dataKey > 1) ? $result[$dataKey - 1][$key] : $dataValue[$key];
                }else{
                    $result[$dataKey][$key] = !empty($dataValue[$key])?$dataValue[$key]:'';
                }
            }
        }
        return $result;
    }

    /** ##################################---获取excel数据----结束---##################################### */


    /** ##################################---处理头补数据----开始---##################################### */
    /**
     * 设置头部
     *
     * @param $header 头部数据
     * demo:  ['row' =>['push' =>['展现量','排期量'],'day' =>['2017-04-06','2017-04-07','2017-04-08'],'ccc' =>['a','b'],],
     *          'col' =>['平台','媒体','对接方式','时长','市场',],]
     * @param $startRow 开始行
     */
    public function setheader($header, $startRow)
    {
        $row = array_values($header['row']);
        //头部占用的行
        $this->headerNum = count($row);
        $col = $header['col'];
        $colNum = count($col);
        $arrDika = $this->combineDika($row);
        $rowNum = count($arrDika);
        //处理col列
        $excelColChar = $this->getChar($colNum);
        $excelColCharResult = [];
        foreach ($excelColChar as $excelColCharKey => $excelColCharValue) {
            $excelColCharResult[$excelColCharKey] = $excelColCharValue . $startRow;
        }

        $colResult = array_combine($excelColCharResult, $col);
        //处理row列 从count(col)后开始
        $excelRowChar = $this->getChar($colNum + $rowNum);
        $result = $resultHeader = [];
        for ($i = 0; $i < $rowNum; $i++) {
            for ($j = 0; $j < $this->headerNum; $j++) {
                $result[$j][$excelRowChar[$i + $colNum] . ($j + $startRow)] = $arrDika[$i][$j];
                $resultHeader[$excelRowChar[$i + $colNum] . ($j + $startRow)] = $arrDika[$i][$j];
            }
        }
        //合并列
        foreach ($excelColChar as $key => $value) {
            $colMerge[] = $value . $startRow . ':' . $value . ($this->headerNum + $startRow - 1);
        }

        $resultColRow = array_merge($colResult, $resultHeader);
        //合并行
        $merge = $this->merge($result);
        $this->writeHeaderExcel($resultColRow);
        $this->writeMergeExcel($merge);
        $this->writeMergeExcel($colMerge);
    }

    /**
     * 拼接excel正文
     * @param $body 正文
     */
    public function writeBody($body){
        $body = array_values($body);
        $keys = $this->getChar($body[0]);
        foreach ($body as $i => $vo) {
            //$j 控制列
            $j = 0;
            foreach ($vo as $key => $item) {
                // 设置数据格式
                $this->excelObj->setActiveSheetIndex($this->sheet)->setCellValue($keys[$j] . $this->headerNum+$i, ' '.$item);
                $j++;
            }
        }

    }

    /**
     * @param $path 保存excel
     */
    public function saveExcel($path){
        $this->saveExcelFile($path);
    }

    /**
     * 将excel保存到本地
     *
     * @param $path  保存文件的路径
     *
     * @throws PHPExcel_Reader_Exception
     */
    public function saveExcelFile($path)
    {
        $objWriter = \PHPExcel_IOFactory::createWriter($this->excelObj, 'Excel2007');
        $objWriter->save($path);
    }

    public function writeHeaderExcel($resultHeader)
    {
        foreach ($resultHeader as $key => $value) {
            $this->excelObj->setActiveSheetIndex($this->sheet)->setCellValue($key, ' ' . $value);
        }
    }

    public function writeMergeExcel($result)
    {
        foreach ($result as $key => $value) {
            $this->excelObj->setActiveSheetIndex($this->sheet)->mergeCells('' . $value);
        }
    }

    public function merge($result)
    {
        //获取要合并的数组 按紧邻的值相同的取得该键组成数组
        $tempMerge = [];
        foreach ($result as $resultKey => $resultValue) {
            $oldvalue = '';
            $i = 0;
            foreach ($resultValue as $key => $value) {
                if (!empty($oldvalue)) {
                    if ($oldvalue !== $value) {
                        $i++;
                    }
                }
                $tempMerge[$value][$i][] = $key;
                $oldvalue = $value;
            }
        }
        //$tempMerge的例子 [['2017-04-06'=>[['G5','H5','I5'],['S5','T5','U5']]]
        $merge = [];
        foreach ($tempMerge as $mergeKey => $mergeValue) {
            foreach ($mergeValue as $mk => $mv) {
                if ($mv[0] !== array_slice($mv, -1)[0]) {
                    $merge[] = $mv[0] . ':' . array_slice($mv, -1)[0];
                }
            }
        }
        return $merge;
    }

    /**
     * 所有数组的笛卡尔积
     *
     * @param unknown_type $data
     */
    public function combineDika($data)
    {
        $cnt = count($data);
        $result = array();
        foreach ($data[0] as $item) {
            $result[] = array($item);
        }
        for ($i = 1; $i < $cnt; $i++) {
            $result = $this->combineArray($result, $data[$i]);
        }
        return $result;
    }

    /**
     * 两个数组的笛卡尔积
     *
     * @param unknown_type $arr1
     * @param unknown_type $arr2
     */
    public function combineArray($arr1, $arr2)
    {
        $result = array();
        foreach ($arr1 as $item1) {
            foreach ($arr2 as $item2) {
                $temp = $item1;
                $temp[] = $item2;
                $result[] = $temp;
            }
        }
        return $result;
    }


    /**
     * 更加数据获得字符
     *
     * @param $colNumber 列数
     *
     * @return array
     */
    private function getChar($colNumber)
    {
        $keys = [];
        $ch = [
            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR',
            'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ',
            'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ', 'CA', 'CB',
            'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT',
            'CU', 'CV', 'CW', 'CX', 'CY', 'CZ', 'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL',
            'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ', 'EA', 'EB', 'EC', 'ED',
            'EE', 'EF', 'EG', 'EH', 'EI', 'EJ', 'EK', 'EL', 'EM', 'EN', 'EO', 'EP', 'EQ', 'ER', 'ES', 'ET', 'EU', 'EV',
            'EW', 'EX', 'EY', 'EZ',
        ];
        for ($number = 1; $number <= $colNumber; $number++) {
            $divisor = intval($number / 26.01);
            $char = chr(64 + $number % 26);
            $charNum = ($char == '@') ? 'Z' : $char;
            if ($divisor < 27) {
                $charNumber = chr(64 + $divisor);
                $char = $divisor == 0 ? $charNum : $charNumber . $charNum;
            } else {
                $charNumber = $divisor - 27;
                $char = $ch[$charNumber] . $charNum;
            }
            $keys[] = $char;
        }

        return $keys;
    }


    /** ##################################---处理头补数据----结束---##################################### */


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