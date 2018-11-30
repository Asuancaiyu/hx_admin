<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/11/21
 * Time: 17:01
 */

namespace app\admin\controller;



class Test extends Base
{
    public function auth()
    {
        $AL=new \app\common\logic\admin\Admin();
        $msg=$AL->getAdminAuthAll(26);
        if (!$msg){
            dump( $AL->getError());
            exit;
        }
        dump($msg);
    }
}