<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/9/30
 * Time: 19:41
 */
namespace app\admin\controller\api;

use app\admin\Common;
use app\common\logic\admin\AdminLogin;
use think\App;

class Login extends Common
{
    /**
     * 管理员登陆逻辑类
     * @var AdminLogin|string
     */
    private $lAdminLogin='';

    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->lAdminLogin=new AdminLogin();
    }

    /**
     * 用户名登陆
     */
    public function username()
    {
        $request=input('post.');
        $username=$request['username'] ?? false;
        $password=$request['password'] ?? false;
        $code=$request['code'] ?? false;
        if (!$username || !$password){
            ajaxReturn(msg(1,'用户名和密码不能为空！'));
        }
        if (!$code){
            ajaxReturn(msg(1,'验证码不能为空！'));
        }
        if (!captcha_check($code)) {
            ajaxReturn(msg(1,'验证码错误!',['v'=>1]));
        }
        $userInfo=$this->lAdminLogin->usernameLogin($username,$password)->exec();
        if (!$userInfo){
            if ($this->lAdminLogin->getError()==11004){
                ajaxReturn(msg(1,'该用户已被禁用！',['v'=>1]));
            }else{
                ajaxReturn(msg(1,'用户名或密码不匹配！',['v'=>1]));
            }
        }
        ajaxReturn(msg(0,'登陆成功！',[]));
    }

}