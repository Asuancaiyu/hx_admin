<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/11/23
 * Time: 18:44
 */
namespace app\common\logic;

use think\Model;

class BaseLogic extends Model
{
    /**
     * 错误信息
     * @var null
     */
    public $error=null;

    /**
     * 错误代码
     * @var int
     */
    public $errorCode = 1;

    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * 获取错误信息代码
     * @return int
     */
    public function getErrorCode(){
        return $this->errorCode;
    }

    /**
     * @return mixed|null
     */
    public function getError(){
        return $this->error;
    }


    /**
     * 设置错误信息
     * @param $code //错误代码
     * @param $msg  //错误说明
     */
    public function setError($code,$msg){
        $this->errorCode = $code;
        $this->error = $msg;
    }
}