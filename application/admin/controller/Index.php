<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/9/23
 * Time: 13:05
 */

namespace app\admin\controller;


use app\admin\Common;
use app\common\logic\admin\AdminLogin;

class Index extends Common
{

    /**
     * 验证码
     * @return \think\Response
     */
    public function captcha()
    {
        return captcha();
    }

    /**
     * 管理员中心
     */
    public function index()
    {
        $this->redirect(url('admin/Center/index'));
    }

    /**
     * 登陆
     * @return mixed
     */
    public function login()
    {
        if (isset($_GET['url'])){
            $this->assign('url',$_GET['url']);
        }
        return $this->fetch();
    }

    /**
     * 注册
     * @return mixed
     */
    public function reg()
    {
        return $this->fetch();
    }

    /**
     * 404错误
     */
    public function error_404()
    {
        return $this->fetch();
    }

    /**
     * 500错误
     */
    public function error_500()
    {
        return $this->fetch();
    }

    /**
     * 维护
     * @return mixed
     */
    public function notice()
    {
        return $this->fetch();
    }

    public function logout()
    {
        session(AdminLogin::$sessionName,'null',AdminLogin::$sessionPrefix);
        $this->redirect(url('admin/index/login'));
    }

    /**
     * 普通错误页面
     * @return mixed
     */
    public function error_page()
    {
        return $this->fetch();
    }

    public function error_auth()
    {
        return $this->fetch();
    }

    /*public function reg_admin()
    {
        $a=new \app\common\logic\admin\Admin();
        $a->adminAdd(['username'=>'admin','password'=>'123456']);
    }*/
}