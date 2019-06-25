<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/9/23
 * Time: 13:05
 */

namespace app\home\controller;

use app\home\Common;

class Index extends Common
{
    public function index()
    {
        return $this->fetch();
    }

    public function db(){
        echo 2;
        exit;
    }
}