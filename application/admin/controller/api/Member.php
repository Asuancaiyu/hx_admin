<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/11/23
 * Time: 16:24
 */

namespace app\admin\controller\api;


use app\admin\Base;
use app\common\logic\member\MemberGroupLogic;
use app\common\logic\member\MemberLogic;
use app\common\logic\member\MemberTagLogic;
use think\facade\Request;


class Member extends Base
{

    //-------------------------------------------------------------
    //--------------        用户         --------------------------
    //-------------------------------------------------------------

    /**
     * 获取用户列表
     */
    public function getMemberList()
    {
        $request=Request::only([
            'page'  =>  1,
            'limit'  =>  40,
            'phone' =>  '',
            'email' =>  '',
            'is_delete' =>  0,
        ]);
        $ml=new MemberLogic();
        $res=$ml->getMemberList($request,$request['is_delete'],$request['page'],$request['limit']);
        $data=$res['data'] ?? null;
        $count=$res['count'] ?? null;
        if (!$res){
            codeReturn($ml->getErrorCode(),$ml->getError());
        }
        codeReturn(0,'ok',$data,$count);
    }

    /**
     * 设置用户启用状态
     * @throws \think\exception\PDOException
     */
    public function setMemberStatus()
    {
        $request=input('');
        $uid=$request['uid'] ?? null;
        $status=$request['status'] ?? null;
        if(null === $uid || null === $status || ($status != 0 && $status != 1)){
            ajaxReturn(msg(1,'错误的访问！'));
        }
        $ml=new MemberLogic();
        $data=$ml->setMemberStatus($uid,$status);
        unset($request);
        if (!$data){
            ajaxReturn(msg($ml->getErrorCode(),'操作失败！'));
        }
        ajaxReturn(msg(0,'ok'));
    }

    /**
     * 删除用户
     * @throws \think\exception\PDOException
     */
    public function deleteMember()
    {
        $request=Request::only([
            'uid' => 0,
            'real' => 0,
        ]);
        if(!$request['uid']){
            ajaxReturn(msg(1,'错误的访问！'));
        }
        if (!in_array($request['real'],[0,1])){
            ajaxReturn(msg(1,'无效的删除类型！'));
        }
        $ml=new MemberLogic();
        $data=$ml->deleteMember($request['uid'],(bool)$request['real']);
        unset($request);
        if (!$data){
            codeReturn($ml->getErrorCode(),'删除失败');
        }
        codeReturn(0,'ok');
    }

    /**
     * 添加用户
     * @throws \think\exception\PDOException
     */
    public function addMember()
    {
        $request=Request::only([
            'phone'  => '', //手机
            'pwd'  =>  '', //密码
            'email' =>  '', //邮箱
            'status' =>  1, //启用状态
            'tag' =>  [], //标签
            'group' =>  0, //用户组
        ]);
        if (empty($request['phone'])){
            codeReturn(1,'缺少手机号码');
        }
        if(!preg_match('/^1[3-9][0-9]{9}$/',$request['phone'])){
            codeReturn(1,'无效的手机号');
        }

        if (empty($request['pwd'])){
            codeReturn(1,'缺少密码');
        }
        if(strlen($request['pwd']) < 6){
            codeReturn(1,'密码不能小于6位');
        }
        if (empty($request['email'])){
            codeReturn(1,'缺少邮箱');
        }
        $request['status']=(int)$request['status'];
        $ml=new MemberLogic();
        $msg=$ml->addMember($request['phone'],$request['pwd'],$request['email'],$request['status'],$request['tag'],$request['group']);
        if (!$msg){
            codeReturn($ml->getErrorCode(),'新增用户失败');
        }
        codeReturn(0,'ok');
    }

    /**
     * 添加用户
     * @throws \think\exception\PDOException
     */
    public function saveMember()
    {
        $request=Request::only([
            'member_id' => 0,
            'phone'  => '', //手机
            'pwd'  =>  '', //密码
            'email' =>  '', //邮箱
            'status' =>  1, //启用状态
            'tag' =>  [], //标签
            'group' =>  0, //用户组
        ]);
        if (empty($request['member_id'])){
            codeReturn(1,'缺少用户id');
        }
        if (empty($request['phone'])){
            codeReturn(1,'缺少手机号码');
        }
        if(!preg_match('/^1[3-9][0-9]{9}$/',$request['phone'])){
            codeReturn(1,'无效的手机号');
        }

        if ($request['pwd'] != ''){
            if(strlen($request['pwd']) < 6){
                codeReturn(1,'密码不能小于6位');
            }
        }
        if (empty($request['email'])){
            codeReturn(1,'缺少邮箱');
        }
        if (!in_array($request['status'],[0,1])){
            codeReturn(1,'无效的状态');
        }

        $ml=new MemberLogic();
        $msg=$ml->updateMemberBaseInfo($request['member_id'],$request['phone'],$request['pwd'],$request['email'],$request['status'],$request['tag'],$request['group']);
        if (!$msg){
            codeReturn($ml->getErrorCode(),'新增用户失败');
        }
        codeReturn(0,'ok');
    }

    /**
     * 获取用户基本信息
     */
    public function getMemberBaseInfo()
    {
        $request=Request::only([
           'member_id' => 0,
        ]);
        if (empty($request['member_id'])){
            codeReturn(1001,'缺少用户id');
        }
        $ml = new MemberLogic();
        if(!$msg=$ml->getMemberBaseInfo($request['member_id'],'id')){
            codeReturn($ml->getErrorCode(),'获取用户信息失败');
        }
        codeReturn(0,'ok',$msg);
    }

    /**
     * 恢复删除用户
     * @throws \think\exception\PDOException
     */
    public function restoreDeleteMember()
    {
        $request=Request::only([
            'member_id' => 0,
        ]);
        if (empty($request['member_id'])){
            codeReturn(1001,'缺少用户id');
        }
        $ml = new MemberLogic();
        if(!$msg=$ml->restoreDeleteMember($request['member_id'])){
            codeReturn($ml->getErrorCode(),'获取用户信息失败');
        }
        codeReturn(0,'ok',$msg);
    }

    //---------------------------------------------------------------
    //----------------------     用户组    --------------------------
    //---------------------------------------------------------------

    /**
     * 获取用户组列表
     */
    public function getMemberGroupList()
    {
        $request=Request::only([
            'name'  => '',
            'page' =>  1,
            'limit' => 20,
        ]);
        $mgl = new MemberGroupLogic();
        $msg=$mgl->getMemberGroupList($request,$request['page'],$request['limit']);
        if (!$msg){
            codeReturn($mgl->getErrorCode(),'获取用户组列表失败');
        }
        codeReturn(0,'ok',$msg['data'],$msg['count']);
    }

    /**
     * 新增用户组
     * @throws \think\exception\PDOException
     */
    public function addMemberGroup()
    {
        $request=Request::only([
            'group_name'  => '',
            'description' =>  '',
        ]);
        if (!$request['group_name']){
            codeReturn(1001,'组名不能为空');
        }
        if (!$request['description']){
            codeReturn(1002,'组说明不能为空');
        }
        $mgl = new MemberGroupLogic();
        $msg=$mgl->addMemberGroup($request['group_name'],$request['description']);
        if (!$msg){
            codeReturn($mgl->getErrorCode(),$mgl->getError());
        }
        codeReturn(0,'ok');
    }

    /**
     * 保存组信息
     * @throws \think\exception\PDOException
     */
    public function saveMemberGroup()
    {
        $request=Request::only([
            'group_name'  => '',
            'description' =>  '',
            'group_id' =>  '',
        ]);
        if (!$request['group_id']){
            codeReturn(1000,'缺少组ID');
        }
        if (!$request['group_name']){
            codeReturn(1001,'组名不能为空');
        }
        if (!$request['description']){
            codeReturn(1002,'组说明不能为空');
        }
        $mgl = new MemberGroupLogic();
        $msg=$mgl->saveMemberGroupInfo($request['group_id'],$request['group_name'],$request['description']);
        if (!$msg){
            codeReturn($mgl->getErrorCode(),$mgl->getError());
        }
        codeReturn(0,'ok');
    }

    /**
     * 删除用户组
     * @throws \think\exception\PDOException
     */
    public function deleteMemberGroup()
    {
        $request=Request::only([
           'group_id' => 0,
        ]);
        if (empty($request['group_id'])){
            codeReturn(1000,'缺少组ID');
        }
        $mgl = new MemberGroupLogic();
        $msg=$mgl->deleteMemberGroup($request['group_id']);
        if (!$msg){
            codeReturn($mgl->getErrorCode(),'删除用户组失败');
        }
        codeReturn(0,'ok');
    }

    /**
     * 用户绑定组
     * @throws \think\exception\PDOException
     */
    public function setMemberBindGroup()
    {
        $request=Request::only([
            'member_id' => 0,
            'group_id' => 0,
        ]);
        if (empty($request['member_id'])){
            codeReturn(1000,'缺少用户ID');
        }
        if (empty($request['group_id'])){
            codeReturn(1001,'缺少组ID');
        }
        $mgl = new MemberGroupLogic();
        $msg=$mgl->memberBindGroup($request['member_id'],$request['group_id']);
        if (!$msg){
            codeReturn($mgl->getErrorCode(),'用户绑定组失败');
        }
        codeReturn(0,'ok');
    }

    public function getMemberBindGroupList()
    {
        $request=Request::only([
            'member_id'  => '',
            'merge'  => 0,//是否与未绑定的的组合并
        ]);
        if (empty($request['member_id'])){
            codeReturn(1000,'缺少用户ID');
        }
        $mgl = new MemberGroupLogic();
        $msg=$mgl->getMemberBindGroupList($request['member_id'],$request['merge']);
        if (!$msg){
            codeReturn($mgl->getErrorCode(),'获取用户组列表失败');
        }
        codeReturn(0,'ok',$msg);
    }


    //--------------------------------------------------------------------------------------
    //------------------------         用户标签         ------------------------------------
    //--------------------------------------------------------------------------------------

    /**
     * 获取 用户绑定的标签
     */
    public function getMemberBindTagList()
    {
        $request=Request::only([
            'member_id'  => '',
            'merge'  => 0,//是否与未绑定的的组合并
        ]);
        if (empty($request['member_id'])){
            codeReturn(1000,'缺少用户ID');
        }
        $ml = new MemberTagLogic();
        $msg=$ml->getMemberBindTagList($request['member_id'],$request['merge']);
        if (false===$msg){
            codeReturn($ml->getErrorCode(),'获取用户标签列表失败');
        }
        codeReturn(0,'ok',$msg);
    }

    /**
     * 获取标签列表
     */
    public function getMemberTagList()
    {
        $request=Request::only([
            'name'  => '',
            'page' =>  1,
            'limit' => 20,
        ]);
        $ml = new MemberTagLogic();
        $msg=$ml->getMemberTagList($request,$request['page'],$request['limit']);
        if (false===$msg){
            codeReturn($ml->getErrorCode(),'获取标签列表失败');
        }
        codeReturn(0,'ok',$msg['data'],$msg['count']);
    }

    /**
     * 新增标签
     * @throws \think\exception\PDOException
     */
    public function addMemberTag()
    {
        $request=Request::only([
            'tag_name'  => '',
            'description' =>  '',
        ]);
        if (!$request['tag_name']){
            codeReturn(1001,'标签名不能为空');
        }
        if (!$request['description']){
            codeReturn(1002,'标签说明不能为空');
        }
        $ml = new MemberTagLogic();
        $msg=$ml->addMemberTag($request['tag_name'],$request['description']);
        if (!$msg){
            codeReturn($ml->getErrorCode(),$ml->getError());
        }
        codeReturn(0,'ok');
    }

    /**
     * 保存标签信息
     * @throws \think\exception\PDOException
     */
    public function saveMemberTag()
    {
        $request=Request::only([
            'tag_name'  => '',
            'description' =>  '',
            'tag_id' =>  '',
        ]);
        if (!$request['tag_id']){
            codeReturn(1000,'缺少标签ID');
        }
        if (!$request['tag_name']){
            codeReturn(1001,'标签名不能为空');
        }
        if (!$request['description']){
            codeReturn(1002,'标签说明不能为空');
        }
        $ml = new MemberTagLogic();
        $msg=$ml->saveMemberTagInfo($request['tag_id'],$request['tag_name'],$request['description']);
        if (!$msg){
            codeReturn($ml->getErrorCode(),$ml->getError());
        }
        codeReturn(0,'ok');
    }

    /**
     * 删除标签
     * @throws \think\exception\PDOException
     */
    public function deleteMemberTag()
    {
        $request=Request::only([
            'tag_id' => 0,
        ]);
        if (empty($request['tag_id'])){
            codeReturn(1000,'缺少标签ID');
        }
        $ml = new MemberTagLogic();
        $msg=$ml->deleteMemberTag($request['tag_id']);
        if (!$msg){
            codeReturn($ml->getErrorCode(),'删除标签失败');
        }
        codeReturn(0,'ok');
    }

    /**
     * 获取用户标签
     * 用户id
     */
    public function getMemberBindTag()
    {
        $request=Request::only([
            'member_id' => 0,
        ]);
        if (empty($request['member_id'])){
            codeReturn(1000,'缺少用户Id');
        }
        $ml = new MemberTagLogic();
        $msg=$ml->getMemberBindTagList($request['member_id']);
        if (false===$msg){
            codeReturn($ml->getErrorCode(),'获取用户标签失败');
        }
        codeReturn(0,'ok',$msg['data'],$msg['count']);
    }
}