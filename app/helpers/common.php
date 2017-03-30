<?php
/**
 * Created by IntelliJ IDEA.
 * User: harry
 * Date: 2017/3/29
 * Time: 16:44
 */







/**
 * 找到数组中所有可能的指定长度的组合，要求没有重复。
 *
 * @param array $arr 要组合的数组
 * @param int $m 按m个元素取值
 * @param string $sign 分割符
 *
 * @return array
 */
function getCombinationToString($arr, $m, $sign = ',')
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
    $arr   = array_values($arr);
    $list1 = $this->getCombinationToString($arr, ($m - 1));

    foreach ($list1 as $s) {
        $s        = $firstelement . $sign . $s;
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