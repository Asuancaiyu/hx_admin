<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/11/19
 * Time: 17:07
 */

namespace app\admin\controller\api;


use app\admin\controller\Base;

class Admin extends Base
{
    /**
     * 获取管理员列表
     */
    public function getAdminList()
    {
        $request=input('');
        $page=$request['page'] ?? 1;
        $limit=$request['limit'] ?? 20;
        unset($request['page']);
        unset($request['limit']);
        $CAdmin=new \app\common\logic\admin\Admin();
        $msg=$CAdmin->adminList($request,false,$page,$limit);
        unset($request);
        if (!$msg){
            ajaxReturn(msg(1,'获取数据失败！'));
        }
        ajaxReturn(dataMsg(0,'ok',$msg['count'],$msg['data']));
    }

    /**
     * 设置管理员启用状态
     */
    public function setAdminStatus()
    {
        $request=input('');
        $uid=$request['uid'] ?? null;
        $status=$request['status'] ?? null;
        if(null === $uid || null === $status || ($status != 0 && $status != 1)){
            ajaxReturn(msg(1,'错误的访问！'));
        }
        $CAdmin=new \app\common\logic\admin\Admin();
        $data=$CAdmin->setAdminStatus($uid,$status);
        unset($request);
        if (!$data){
            ajaxReturn(msg(1,'操作失败！'));
        }
        ajaxReturn(msg(0,'ok'));
    }

    /**
     *  添加管理员
     * @throws \think\exception\PDOException
     */
    public function addAdmin()
    {
        $request = input('');
        $username = $request['username'] ?? codeReturn(1,'缺少用户名');
        $pwd = $request['pwd'] ??  codeReturn(1,'缺少密码');
        $realname = $request['realname'] ?? codeReturn(1,'缺少姓名');
        $phone = $request['phone'] ?? codeReturn(1,'缺手机号码');
        $group = $request['group'] ?? codeReturn(1,'缺少组');
        $role = $request['role'] ?? codeReturn(1,'缺少角色');
        $status = $request['status'] ?? codeReturn(1,'缺少启用状态');

        if(!preg_match('/^[a-zA-Z0-9_-]{4,20}$/',$username)){
            codeReturn(1,'用户名为4~20个字符');
        }
        if(strlen($pwd) < 6){
            codeReturn(1,'密码不能小于6位');
        }
        if(!preg_match('/^1[3-9][0-9]{9}$/',$phone)){
            codeReturn(1,'无效的手机号');
        }
        if (!is_array($group)){
            codeReturn(1,'无效的分组');
        }
        if (count($group)>1){
            codeReturn(1,'最多只能选择一个分组');
        }
        if (!is_array($role)){
            codeReturn(1,'无效的角色');
        }
        $status=(int)$status;
        if ($status != 1 && $status !=0){
            codeReturn(1,'无效的启用状态');
        }

        $al=new \app\common\logic\admin\Admin();
        $msg=$al->adminAdd($username,$pwd,$realname,$phone,$group,$role,$status);
        if (!$msg){
            codeReturn($al->getError(),'error');
        }
        codeReturn(0,'ok');
    }

    /**
     * 更新管理员信息
     * @throws \think\exception\PDOException
     */
    public function saveAdmin()
    {
        $request = input('');
        $adminId = $request['id'] ?? codeReturn(1,'缺少识别码');
        $username = $request['username'] ?? codeReturn(1,'缺少用户名');
        $pwd = $request['pwd'] ??  null;
        $realname = $request['realname'] ?? codeReturn(1,'缺少姓名');
        $phone = $request['phone'] ?? codeReturn(1,'缺手机号码');
        $group = $request['group'] ?? codeReturn(1,'缺少组');
        $role = $request['role'] ?? codeReturn(1,'缺少角色');
        $status = $request['status'] ?? codeReturn(1,'缺少启用状态');

        if(!preg_match('/^[a-zA-Z0-9_-]{4,20}$/',$username)){
            codeReturn(1,'用户名为4~20个字符');
        }
        if ($pwd){
            if(strlen($pwd) < 6){
                codeReturn(1,'密码不能小于6位');
            }
        }

        if(!preg_match('/^1[3-9][0-9]{9}$/',$phone)){
            codeReturn(1,'无效的手机号');
        }
        if (!is_array($group)){
            codeReturn(1,'无效的分组');
        }
        if (count($group)>1){
            codeReturn(1,'最多只能选择一个分组');
        }
        if (!is_array($role)){
            codeReturn(1,'无效的角色');
        }
        $status=(int)$status;
        if ($status != 1 && $status !=0){
            codeReturn(1,'无效的启用状态');
        }

        $al=new \app\common\logic\admin\Admin();
        $msg=$al->adminInfoUpdate($adminId,$username,$pwd,$realname,$phone,$group,$role,$status);
        if (!$msg){
            codeReturn($al->getError(),'error');
        }
        codeReturn(0,'ok');
    }

    /**
     * 获取管理员基本信息
     */
    public function getAdminInfo()
    {
        $request=input('');
        $id=$request['id'] ?? null;
        if (!$id){
            codeReturn(1,'缺少id');
        }
        $msg=(new \app\common\logic\admin\Admin())->adminInfo($id);
        if (!$msg){
            codeReturn(1,'管理员信息不存在');
        }
        codeReturn(0,'ok',$msg);
    }

    /**
     * 获取管理员绑定的组
     */
    public function getAdminBindGroup()
    {
        $request=input('');
        $adminId=$request['adminId'] ?? codeReturn(1,'缺少管理员id');
        $AL=new \app\common\logic\admin\Admin();
        $msg=$AL->getAdminBindGroup($adminId);
        if (!$msg){
            codeReturn($AL->getError(),'管理员信息不存在');
        }
        codeReturn(0,'ok',$msg);
    }

    /**
     * 获取管理员已绑定的角色
     */
    public function getAdminBindRole()
    {
        $request=input('');
        $adminId=$request['adminId'] ?? codeReturn(1,'缺少管理员id');
        $AL=new \app\common\logic\admin\Admin();
        $msg=$AL->getAdminBindRole($adminId);
        if (!$msg){
            codeReturn($AL->getError(),'管理员信息不存在');
        }
        codeReturn(0,'ok',$msg);
    }

    /**
     * 删除管理员
     */
    public function deleteAdmin()
    {
        $request=input('');
        $uid=$request['uid'] ?? 0;
        if(!$uid){
            ajaxReturn(msg(1,'错误的访问！'));
        }
        $CAdmin=new \app\common\logic\admin\Admin();
        $data=$CAdmin->adminDelete($uid,true);
        unset($request);
        if (!$data){
            ajaxReturn(msg($CAdmin->getError(),'删除失败！'));
        }
        ajaxReturn(msg(0,'ok'));
    }

    /**
     * 获取被删除的管理员
     */
    public function getDeleteAdminList()
    {
        $request=input('');
        $page=$request['page'] ?? 1;
        $limit=$request['limit'] ?? 20;
        unset($request['page']);
        unset($request['limit']);
        $CAdmin = new \app\common\logic\admin\Admin();
        $msg=$CAdmin->adminList($request,true,$page,$limit);
        unset($request);
        if (!$msg){
            ajaxReturn(msg(1,'获取数据失败！'));
        }
        ajaxReturn(dataMsg(0,'ok',$msg['count'],$msg['data']));
    }
}