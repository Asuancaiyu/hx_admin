<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/9/29
 * Time: 19:34
 */

namespace app\common\logic\admin;


use app\common\model\admin\AdminModel;
use think\facade\Session;
use think\Model;

class AdminLogin extends Model
{
    /**
     * 表查询条件
     * @var array
     */
    private $condition=[];

    /**
     * 存储用户信息的session名称
     * @var string
     */
    public static $sessionName='admin_info';

    /**
     * Session作用域（前缀）
     * @var string
     */
    public static $sessionPrefix='admin';

    /**
     * 存储的用户信息
     * @var array
     */
    public $adminInfo=[];

    /**
     * 登陆初始化
     * @param $userInfo
     * @return bool
     */
    public function loginIni($userInfo)
    {
        if (!$userInfo || !is_array($userInfo)){
            $this->error=10001;//用户登陆初始化失败！[缺少有效用户信息]
            return false;
        }
        $this->adminInfo=$userInfo;
        if(!self::setAdminSession($userInfo)){
            $this->error=10002;//设置用户session 失败
            return false;
        }
        return true;
    }

    /**
     * 设置管理员Session
     * @param $sessionData
     * @return bool
     */
    public static function setAdminSession($sessionData)
    {
        if(!$sessionData){
            return false;
        }
        Session::set(self::$sessionName,$sessionData,self::$sessionPrefix);
        return true;
    }


    /**
     * 用户名方式登陆
     * @param $username
     * @param $pwd
     * @return $this
     */
    public function usernameLogin($username,$pwd)
    {
        $e='/^[a-zA-Z0-9_-]{4,30}$/';
        if (!preg_match($e,$username)){
            $this->error=10002;//用户名称格式不正确
        }else{
            $this->condition=[
                'username'  =>  $username,
                'password'   =>  pwdEncryption($pwd),
            ];
        }
        return $this;
    }

    /**
     * 手机号形式登陆
     * @param $phone
     * @param $pwd
     * @return $this
     */
    public function phoneLogin($phone,$pwd)
    {
        $e='/^1[1-9]{9}$/';
        if (!preg_match($e,$phone)){
            $this->error=10003;//手机格式不正确
        }else{
            $this->condition=[
                'phone'  =>  $phone,
                'password'   =>  pwdEncryption($pwd),
            ];
        }
        return $this;
    }

    /**
     * email形式登陆
     * @param $email
     * @param $pwd
     * @return $this
     */
    public function emailLogin($email,$pwd)
    {
        $e='/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/';
        if (!preg_match($e,$email)){
            $this->error=10004;//邮箱格式不正确
        }else {
            $this->condition = [
                'email' => $email,
                'password' => pwdEncryption($pwd),
            ];
        }
        return $this;
    }

    /**
     * 手机验证码登陆 （注：功能未完成）
     * @param $phone
     * @param $code
     * @return $this
     */
    public function phoneCodeLogin($phone,$code)
    {
        if ($code!=='my code'){
            $this->error=10005;//验证码不正确
        }else{
            $this->condition=[
                'phone'  =>  $phone,
            ];
        }
        return $this;
    }

    /**
     * 执行
     * @return bool
     */
    public function exec()
    {
        //如果有错误直接返回
        if ($this->error){
            return false;
        }
        if (!$this->condition){
            $this->error=11001;//缺少查询条件
            return false;
        }
        $adminInfo=AdminModel::adminInfo($this->condition,'*');
        if (!$adminInfo){
            $this->error=11002;//获取数据失败
            return false;
        }
        if ($adminInfo['status']==0){
            $this->error=11004;//该账号已被禁用！
            return false;
        }
        if(!$this->loginIni($adminInfo)){
            return false;
        }
        return true;
    }
}