<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/10/8
 * Time: 15:53
 */

namespace app\admin;


use app\common\logic\admin\AdminLogin;
use think\App;
use think\facade\Request;
use think\facade\Session;

class Base extends Common
{
    protected $adminInfo=null;

    public function __construct(App $app = null)
    {
        parent::__construct($app);
    }

    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $adminInfo=Session::get(AdminLogin::$sessionName,AdminLogin::$sessionPrefix);
        //如果不存在session则跳转至登陆页
        if ($adminInfo=='null' || empty($adminInfo['id'])){
            $isAjax=Request::isAjax();
            $loginUrl=url('admin/Index/login').'?url='.urlencode(CURRENT_URL);
            if (!$isAjax){
                $this->redirect($loginUrl);
            }else{
                ajaxReturn(msg(-1,'请先登录！',['url'=>$loginUrl]));
            }
        }else{
            $this->adminInfo=$adminInfo;
            //用户已登录的逻辑
            //.............
            $this->authVerify();
        }
    }

    /**
     * 验证访问权限
     * @return bool
     */
    public function authVerify()
    {
        //Session::delete(AdminLogin::$sessionName,AdminLogin::$sessionPrefix);
        //unset($this->adminInfo['auth']);
        $auth = $this->adminInfo['auth'] ?? null;
        $adminId = $this->adminInfo['id'] ?? null;
        if (!$auth) {
            $AL = new \app\common\logic\admin\Admin();
            $msg = $AL->getAdminAuthAll($adminId);
            if (!$msg) {
                $this->errorPage('您没有权限访问！[0002]',1);
            }
            $authO = $msg['operation'] ?? null;
            if (!$authO) {
                $this->errorPage('您没有权限访问！[0003]',1);
            }
            $AOArr = [];
            foreach ($authO as $k => $v) {
                $AOArr[] = $v['url'];
            }
            //权限写入session
            $this->adminInfo['auth']['operation'] = $AOArr;
            $this->adminInfo['auth']['menu'] = [];
            AdminLogin::setAdminSession($this->adminInfo);
            $auth= $this->adminInfo['auth'];
        }

        $currentUrl=Request::url();
        if (empty($auth['operation'])){
            $msg="您没有权限访问！[0004]";
            if (Request::isAjax()){
                codeReturn(1,$msg);
            }
            $this->errorPage($msg,1);
        }

        $pass=false;//通行权限
        foreach ($auth['operation'] as $v){
            if (strpos($currentUrl,$v)===0){
                $pass=true;
                break;
            }
        }
        if (!$pass){
            $msg="您没有权限访问！[0005]";
            if (Request::isAjax()){
                codeReturn(1,$msg);
            }
            $this->errorPage($msg,1);
        }
        //dump($_SERVER);
        return true;
    }


    /**
     * 返回错误页面
     * @param $msg
     * @param null $errorType
     */
    public function errorPage($msg,$errorType=null)
    {
        switch ($errorType){
            case 1://无权限访问;
                $url=url("admin/index/error_auth");
                break;
            default://普通页面
                $url=url("admin/index/error_page");
        }
        header('location:'.$url.'?msg='.urlencode($msg));
        exit;
    }

}