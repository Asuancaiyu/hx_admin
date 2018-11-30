<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/9/21
 * Time: 12:43
 */

namespace addons\test\controller;

use think\addons\Controller;

class Action extends Controller
{
    public function link()
    {
        return '111';
        //return $this->fetch();
    }
}
