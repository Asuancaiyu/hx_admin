<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/9/23
 * Time: 19:41
 */

namespace app\home\controller;

use app\home\Common;
class Login extends Common
{
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    /**
     * @param $username
     * @param $pwd
     * @param string $loginType
     */
    public function doLogin($username,$pwd,$loginType='username')
    {

    }

    public function index(){
        dump(1);
    }
}