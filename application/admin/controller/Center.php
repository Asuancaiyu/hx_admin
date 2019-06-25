<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/9/23
 * Time: 19:41
 */

namespace app\admin\controller;

use app\admin\Base;
class Center extends Base
{
    /**
     * 后台中心
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    //欢迎页
    public function welcome()
    {
        return $this->fetch();
    }
}