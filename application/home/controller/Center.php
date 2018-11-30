<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/9/23
 * Time: 19:41
 */

namespace app\home\controller;


use app\Base;

class Center extends Base
{
    public function index()
    {
        return $this->fetch();
    }
}