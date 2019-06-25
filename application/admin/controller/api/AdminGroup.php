<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/11/15
 * Time: 18:27
 */

namespace app\admin\controller\api;


use app\admin\Base;
use app\common\logic\admin\GroupLogic;

class AdminGroup extends Base
{
    /**
     * 获取组列表
     */
    public function getGroup()
    {
        $msg=(new GroupLogic())->getGroupList();
        codeReturn(0,'ok',$msg['data'],$msg['count']);
    }


    /**
     * 添加组
     * @throws \think\exception\PDOException
     */
    public function addGroup()
    {
        $request=input('');
        $groupName=$request['name'] ?? null;
        $groupDescription=$request['description'] ?? null;
        $roleId=$request['roleId'] ?? null;
        $parentGroupId=$request['pid'] ?? 0;
        if (!$groupName){
            codeReturn(1001,'缺少组名称');
        }
        if (!$groupDescription){
            codeReturn(1002,'缺少组说明');
        }
        if ($roleId){
            if (!is_array($roleId)){
                $roleId=[$roleId];
            }
        }

        $gl=new GroupLogic();
        $msg=$gl->addGroup($groupName,$groupDescription,$roleId,$parentGroupId);
        if (!$msg){
            codeReturn($gl->getError(),'操作失败');
        }
        codeReturn(0,'ok',['id'=>$msg]);
    }

    /**
     * 保存组信息
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function saveGroupInfo()
    {
        $request=input('');
        $groupId=$request['id'] ?? 0;
        $groupName=$request['name'] ?? null;
        $groupDescription=$request['description'] ?? null;
        $roleId=$request['roleId'] ?? null;
        if (!$groupId){
            codeReturn(1001,'缺少组id');
        }
        if (!$groupName){
            codeReturn(1002,'缺少组名称');
        }
        if (!$groupDescription){
            codeReturn(1003,'缺少组说明');
        }
        if ($roleId){
            if (!is_array($roleId)){
                $roleId=[$roleId];
            }
        }
        unset($request);
        $gl=new GroupLogic();
        $msg=$gl->updateGroupInfo($groupId,$groupName,$groupDescription,$roleId);
        if (!$msg){
            codeReturn($gl->getError(),'操作失败');
        }
        codeReturn(0,'ok');
    }

    /**
     * 删除组
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function deleteGroup()
    {
        $request=input('');
        $gId = $request['id'] ?? null;
        if (!$gId){
            codeReturn(1000,'缺少组id');
        }
        $gl=new GroupLogic();
        $msg=$gl->deleteGroup($gId);
        if (!$msg){
            codeReturn($gl->getError(),'操作失败');
        }
        codeReturn();
    }

    /**
     * 获取组已绑定的角色
     */
    public function getGroupBindRole()
    {
        $request=input('');
        $groupId=$request['gId'] ?? null;
        if (!$groupId){
            codeReturn(1000,'缺少组标识');
        }
        $gl = new GroupLogic();
        if(false === $data = $gl->getGroupBindRole($groupId, true)){
            codeReturn(1,$gl->getError());
        }
        codeReturn(0,'ok',$data);
    }
}