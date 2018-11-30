<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/9/23
 * Time: 13:05
 */

namespace app\home\controller;


use app\Base;

class Index extends Base
{
    public function index()
    {
        return $this->fetch();
    }
}