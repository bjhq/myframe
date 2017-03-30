<?php
/**
 * Created by IntelliJ IDEA.
 * User: harry
 * Date: 2017/3/24
 * Time: 16:12
 */

namespace core;

use libraries\My_Schema;
use libraries\My_Macaw as Macaw;

class MY_Controller
{

    protected $Jschema;
    protected $methods;
    protected $class;
    protected $methods_class;
    #错误码信息
    protected $errCode = [];
    const BAD_PARAM = 4;
    
    public function __construct()
    {
        //引入json_schema
        $this->initError();
        $this->getMC();
        $this->Jschema = new My_Schema($this->methods_class);

    }

    /**
     * 检查 API 请求参数
     */
    public function checkSchema()
    {
        //##########等待封装的地方--开始########
        $content      = file_get_contents('php://input');
//        $content = '{"auto_update":1,"campaign_id":1,"campaign_info":[{"campaign_name":"111"}],"chart":[12121212],"description":"card描述","name":"card名称修改版","config":[333],"status":1,"send_time":12,"cycle":1}';
        $socket = json_decode($content, TRUE);
        $json   = json_decode($content);
        $json1   = $json ? $json : new \STDclass();

        //##########等待封装的地方--结束########
        $this->Jschema->check($json1);

        if (!$this->Jschema->isValid()) {
            $error = $this->Jschema->error();

            $this->callBackWithParamError($error);
        }
        return TRUE;
    }

    /**
     * 参数异常返回
     *
     * @msg 异常信息, 可以不传,默认按照错误码信息返回
     */
    public function callBackWithParamError($msg = '')
    {
        $this->callBack(self::BAD_PARAM, $msg);
    }


    /**
     * @name callBack
     *
     * 系统API返回函数
     *
     * @param Int $errCode 错误码,具体参照application/config/error.php中的配置
     * @param Array $data 接口返回的数据
     *
     * @return JsonString
     *      code=0 Success
     *      code>0 Failed
     */
    public function callBack($errCode = 0, $data = [], $errMsg = '', $format = 'json')
    {
        $msg = isset($this->errCode[$errCode]) ? $this->errCode[$errCode] : '';
        $data = [
            'code' => $errCode,
            'msg' => $errMsg ? $errMsg : $msg,
            'data' => $data,
        ];
        if ($format == 'json') {
            die(json_encode($data, JSON_UNESCAPED_UNICODE));
        } else {
            return $data;
        }
    }

    /*
     * @name _initError
     *
     * 初始化错误码
     */
    private function initError()
    {
        $tempErrCode  = require BASE_PATH.'/error.php';
        $this->errCode = $tempErrCode['error'];
    }

    public function initParam()
    {
        $this->get  = $this->input->get();
        $this->post = $this->input->post();

        $content      = file_get_contents('php://input');
        $this->socket = json_decode($content, TRUE);
        $this->json   = json_decode($content);
        $this->json   = $this->json ? $this->json : new STDclass();

        $this->request = array_merge($this->post, $this->get);

        $this->get    = new Request($this->get, ArrayObject::STD_PROP_LIST);
        $this->post   = new Request($this->post, ArrayObject::STD_PROP_LIST);
        $this->socket = new Request($this->socket ? $this->socket : [], ArrayObject::STD_PROP_LIST);
    }

    /**
     * 获取
     */
    private function getMC()
    {
        $arrMC = Macaw::$mysegments;
        $this->class = $arrMC[0];
        $this->methods = $arrMC[1];
        $this->methods_class = [
            'class' => $arrMC[0],
            'method' => $arrMC[1]
        ];
    }

}