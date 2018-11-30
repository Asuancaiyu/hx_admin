<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/11/29
 * Time: 13:27
 */

namespace app\common\logic\member;


use app\common\logic\BaseLogic;
use app\common\model\member\MemberBindGroupModel;
use app\common\model\member\MemberGroupModel;

class MemberGroupLogic extends BaseLogic
{
    public $mgm = null;
    public $mbgm = null;
    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->mgm = new MemberGroupModel();
        $this->mbgm = new MemberBindGroupModel();
    }
    
    /**
     * 获取用户组列表
     * @param array $request
     * @param int $page
     * @param int $limit
     * @return array|bool
     */
    public function getMemberGroupList($request=[],$page=1,$limit=20)
    {
        foreach ($request as $k=>$v){
            $request[$k] = trim($v);
        }
        $condition=[];
        if (!empty($request['name'])){
            $condition[]=['group_name','like','%'.$request['name'].'%'];
        }
        try{
            $msg=$this->mgm->dataList($condition,'id,group_name,description', $limit);
            if (false===$msg){
                $this->setError(5501,'获取用户组失败');
                return false;
            }
        }catch (\Exception $e){
            $this->setError(9999,$e->getMessage());
            return false;
        }
        return $msg;
    }

    /**
     * 获取组信息
     * @param $memberId
     * @return array|bool|null|\PDOStatement|string|\think\Model
     */
    public function getMemberGroupInfo($memberId)
    {
        try{
            $msg=$this->mgm->dataFind(['id'=>$memberId]);
            if (false===$msg){
                $this->setError(5502,'获取用户组信息失败');
                return false;
            }
        }catch (\Exception $e){
            $this->setError(9999,$e->getMessage());
            return false;
        }
        return $msg;
    }

    /**
     * 添加用户组
     * @param $name 组名
     * @param $description 组说明
     * @return bool
     * @throws \think\exception\PDOException
     */
    public function addMemberGroup($name,$description)
    {
        $data=[
            'group_name' => $name,
            'description' => $description,
            'create_time' => time()
        ];
        
        if($this->mgm->dataCount(['group_name'=>$name])){
            $this->setError(5510,'该用户组已存在');
            return false;
        }
        $this->startTrans();
        try{
            $msg=$this->mgm->addData($data);
            if (false===$msg){
                $this->rollback();
                $this->setError(5503,'添加用户组失败');
                return false;
            }
            $this->commit();
        }catch (\Exception $e){
            $this->rollback();
            $this->setError(9999,$e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * 保存用户组信息
     * @param $groupId 组信息
     * @param $name 组名
     * @param $description 组说明
     * @return bool
     * @throws \think\exception\PDOException
     */
    public function saveMemberGroupInfo($groupId,$name,$description)
    {
        $data=[
            'group_name' => $name,
            'description' => $description,
            'modify_time' => time(),
        ];
        
        if($this->mgm->dataCount([['group_name','=',$name],['id','<>',$groupId]])){
            $this->setError(5510,'该用户组已存在');
            return false;
        }

        $this->startTrans();
        $condition=['id' => $groupId];
        try{
            if(!$this->mgm->dataCount($condition)){
                $this->rollback();
                $this->setError(5500,'组不存在');
                return false;
            }
            $msg=$this->mgm->saveData($condition,$data);
            if (false===$msg){
                $this->rollback();
                $this->setError(5503,'添加用户组失败');
                return false;
            }
            $this->commit();
        }catch (\Exception $e){
            $this->rollback();
            $this->setError(9999,$e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * 删除组
     * @param $groupId 组id
     * @return bool
     * @throws \think\exception\PDOException
     */
    public function deleteMemberGroup($groupId)
    {
        $this->startTrans();
        $condition=['id' => $groupId];
        try{
            if(!$this->mgm->dataCount($condition)){
                $this->rollback();
                $this->setError(5500,'组不存在');
                return false;
            }
            $msg=$this->mgm->deleteData($condition);
            if (false===$msg){
                $this->rollback();
                $this->setError(5503,'添加用户组失败');
                return false;
            }
            //用户解绑组
            $msg2=$this->mbgm->deleteData(['group_id'=>$groupId]);
            if (false===$msg2){
                $this->rollback();
                $this->setError(5505,'用户解绑组失败');
                return false;
            }
            $this->commit();
        }catch (\Exception $e){
            $this->rollback();
            $this->setError(9999,$e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * 获取用户绑定的组
     * @param $memberId
     * @param $merge
     * @return array|bool|\PDOStatement|string|\think\Collection
     */
    public function getMemberBindGroupList($memberId,$merge)
    {
        if (!in_array($merge,[0,1])){
            $this->setError(5601,'不存在的合并参数');
            return false;
        }
        $msg=$this->mgm->getMemberBindGroup($memberId,$merge);
        if (!$msg){
            $this->setError(5602,'获取用户组失败');
            return false;
        }
        return $msg;
    }

    /**
     * 用户绑定组
     * @param $memberId 用户id
     * @param $groupId 组id
     * @return bool
     * @throws \think\exception\PDOException
     */
    public function memberBindGroup($memberId,$groupId)
    {
        if(!$this->mgm->dataCount(['id'=>$memberId])){
            $this->setError(5000,'用户不存在');
            return false;
        }

        if(!$this->mbgm->dataCount(['id'=>$groupId])){
            $this->setError(5500,'组不存在');
            return false;
        }
        $data=[
            'member_id' => $memberId,
            'group_id' => $groupId,
        ];
        $this->startTrans();
        try{
            $msg=$this->mbgm->addData($data);
            if (!$msg){
                $this->rollback();
                $this->setError(5600,'用户绑定组失败');
                return false;
            }
            $this->commit();
        }catch (\Exception $e){
            $this->rollback();
            $this->setError(9999,$e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * 用户解绑组
     * @param $memberId
     * @param null $groupId
     * @return bool
     */
    public function setMemberUnbindGroup($memberId,$groupId=null)
    {
        $mbgm = new MemberBindGroupModel();
        $condition=['member_id'=>$memberId];
        if ($groupId){
            $condition['group_id'] = $groupId;
        }
        if(false===$mbgm->deleteData($condition)){
            $this->setError(5610,'用户解绑组失败');
            return false;
        }
        return true;
    }
}