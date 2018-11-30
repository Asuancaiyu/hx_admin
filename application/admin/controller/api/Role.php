<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/11/5
 * Time: 12:08
 */

namespace app\admin\controller\api;


use app\admin\controller\Base;
use app\common\logic\admin\RoleLogic;

class Role extends Base
{

    /**
     * @throws \think\exception\DbException
     */
    public function addRole()
    {
        $request=input('');
        $name=$request['name'] ?? null;
        $desc=$request['desc'] ?? null;
        $AO=$request['authOperation'] ?? null;
        $AM=$request['authMenu'] ?? null;
        unset($request);
        if(!$name){
            ajaxReturn(msg(1,'请输入角色名称'));
        }
        if(!$desc){
            ajaxReturn(msg(1,'请输入角色说明'));
        }
        if(!$AO){
            ajaxReturn(msg(1,'请选择操作权限'));
        }
        $RL=new RoleLogic();
        $msg=$RL->addRole($name,$desc,$AO,$AM);
        if (!$msg){
            if (is_int($RL->getError())) {
                ajaxReturn(msg($RL->getError(),'操作失败'));
            }else{
                ajaxReturn(msg(1,$RL->getError()));
            }

        }
        ajaxReturn(msg(0,'ok'));
    }

    /**
     * 保存角色信息
     */
    public function saveRoleInfo()
    {
        $request=input('');
        $rId=$request['id'] ?? null;
        $name=$request['name'] ?? null;
        $desc=$request['desc'] ?? null;
        $AO=$request['authOperation'] ?? null;
        $AM=$request['authMenu'] ?? null;
        unset($request);
        if(!$rId){
            ajaxReturn(msg(1000,'错误的访问'));
        }
        if(!$name){
            ajaxReturn(msg(1001,'请输入角色名称'));
        }
        if(!$desc){
            ajaxReturn(msg(1002,'请输入角色说明'));
        }
        if(!$AO){
            ajaxReturn(msg(1003,'请选择操作权限'));
        }
        $RL=new RoleLogic();
        $msg=$RL->saveRole($rId,$name,$desc,$AO,$AM);
        if (!$msg){
            if (is_int($RL->getError())) {
                ajaxReturn(msg($RL->getError(),'操作失败'));
            }else{
                ajaxReturn(msg(1,$RL->getError()));
            }

        }
        ajaxReturn(msg(0,'ok'));
    }

    /**
     * 角色名称验证
     */
    public function queryAuthName()
    {
        $request=input('');
        $name=$request['name'] ?? null;
        $RL=new RoleLogic();
        $msg=$RL->queryAuthName($name);
        if (!$msg){
            ajaxReturn(msg($RL->getError(),'角色名称已存在'));
        }
        ajaxReturn(msg(0,'ok'));
    }

    /**
     * 获取角色列表
     */
    public function getRoleList()
    {
        $request=input('');
        $page=$request['page'] ?? 1;
        $limit=$request['limit'] ?? 20;
        unset($request['page']);
        unset($request['limit']);
        if(!$page){
            ajaxReturn(msg(1,'错误的访问！'));
        }
        $msg=(new RoleLogic())->getRoleList($request,$page,$limit);
        unset($request);
        if (!$msg){
            ajaxReturn(msg(1,'操作失败！'));
        }
        ajaxReturn(dataMsg(0,'ok',$msg['count'],$msg['data']));
    }

    /**
     * 删除角色
     */
    public function deleteRole()
    {
        $request=input('');
        $rid=$request['id'] ?? 0;
        if(!$rid){
            ajaxReturn(msg(1,'错误的访问！'));
        }
        $RL=new RoleLogic();
        $msg=$RL->doRoleDelete($rid);
        unset($request);
        if (!$msg){
            ajaxReturn(msg($RL->getError(),'删除失败！'));
        }
        ajaxReturn(msg(0,'ok'));
    }

    /**
     * 获取角色权限
     */
    public function getRoleBindOperation()
    {
        $request=input('');
        $roleId=$request['id'] ?? null;
        $merge=!empty($request['merge']) ? true : false;//是否与未拥有的权限合并
        if (!$roleId){
            ajaxReturn(msg(1000,'错误的访问！'));
        }
        $msg=(new RoleLogic())->getRoleBindOperation($request,$merge);
        if (!$msg){
            ajaxReturn(msg(1,''));
        }
        ajaxReturn(msg(0,'ok',$msg));
    }

    /**
     * 获取角色基础信息
     */
    public function getRoleBaseInfo()
    {
        $request = input('');
        $id=$request['id'] ?? 0;
        unset($request);
        if (!$id){
            ajaxReturn(msg(1000,'错误的访问'));
        }
        $rl=new RoleLogic();
        $msg=$rl->getRoleBaseInfo($id);
        if (!$msg){
            ajaxReturn(msg($rl->getError(),'error'));
        }
        ajaxReturn(msg(0,'ok',$msg));
    }

}