<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/9/26
 * Time: 17:19
 */

namespace app\admin\controller;


use app\common\logic\admin\RoleLogic;

class Admin extends Base
{
    /**
     * 管理员列表页面
     * @return mixed
     */
    public function admin_list()
    {
        return $this->fetch();
    }

    /**
     * 添加管理员页面
     * @return mixed
     */
    public function admin_add(){
        return $this->fetch();
    }

    /**
     * 编辑管理员信息页面
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function admin_edit(){
        $request=input('');
        $uid=$request['uid'] ?? 0;
        if(!$uid){
            ajaxReturn(msg(1,'错误的访问！'));
        }
        $adminLogic=new \app\common\logic\admin\Admin();
        if (!$data=$adminLogic->adminInfo($uid)){
            ajaxReturn(msg(1,'错误的访问！'));
        }else{
            $this->assign('data',$data);
        }
        return $this->fetch();
    }

    /**
     * 管理员组列表
     */
    public function group_list()
    {
        return $this->fetch();
    }

    /**
     * 管理员角色列表
     */
    public function role_list()
    {
        return $this->fetch();
    }

    /**
     * 编辑角色信息
     * @return mixed
     */
    public function role_edit()
    {
        return $this->fetch();
    }

    /**
     * 管理员信息
     */
    public function admin_info()
    {
        return $this->fetch();
    }

    /**
     * 权限页面
     * @return mixed
     */
    public function auth()
    {
        return $this->fetch();
    }

    /**
     * 操作权限页面
     * @return mixed
     */
    public function operation_auth()
    {
        return $this->fetch();
    }

    /**
     * 菜单权限
     * @return mixed
     */
    public function menu_auth()
    {
        return $this->fetch();
    }

}