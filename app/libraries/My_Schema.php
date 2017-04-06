<?php
/**
 * Created by IntelliJ IDEA.
 * User: harry
 * Date: 2017/3/27
 * Time: 14:53
 */

namespace libraries;
use JsonSchema\Validator;
use JsonSchema\Constraints\Constraint;

class My_Schema
{
    private $validator;
    private $path;
    private $schema = null;
    private $errCode = null;
    private $errMsg;
    const ERROR_NO_EXISTS    =   -1;

    public function __construct($config)
    {
        $this->init($config);
        $this->loadSchema();
    }

    /**
     * 规定自定义的路径
     */
    private function init(array $config)
    {
        //规定引入文件路径
        $basicPath = APPLICATION_ROOT . 'app/third_party/Json_Schema/';
        //具体文件
        $method = $config['method'];
        $class = $config['class'];
        //$this->path = $basicPath .  $this->directory . $this->class .'/'. $this->method.'.json';
        $this->path = $basicPath  . $class .'/'. $method  . '.json';
    }
    /**
     * 加载schema
     */
    private function loadSchema()
    {
        $this->validator = new Validator;
        if(file_exists($this->path)) {
            $this->schema = file_get_contents($this->path);
        } else {
            $this->errCode = self::ERROR_NO_EXISTS;
        }
    }

    /**
     * 进行字段验证
     *
     * @param  StdClass $jsonData
     * @return bool
     */
    public function check(\StdClass $jsonData)
    {
        if($this->errCode == self::ERROR_NO_EXISTS)  {
            $this->errMsg = 'Can Not Found Json Schema File';
        } else {
            $schemaData = json_decode($this->schema);
            if(!$schemaData) {
                $this->errCode = self::ERROR_SCHEMA_ERROR;
                $this->errMsg  = 'Invalid Json Schema Data, Please Check Schema JsonData';
                return FALSE;
            }

            $this->validator->check($jsonData, $schemaData);
            if(!$this->validator->isValid()) {
                foreach ($this->validator->getErrors() as $error) {
                    $this->errMsg .=  sprintf("%s %s\n", $error['property'], $error['message']) . "\r\n";
                }
            }
        }
    }

    public function error()
    {
        return $this->errMsg;
    }


    /**
     * 判断当前字段是否可用
     */
    public function isValid()
    {
        return $this->errMsg ? FALSE : TRUE;
    }

}