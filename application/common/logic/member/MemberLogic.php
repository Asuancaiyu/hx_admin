<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/11/23
 * Time: 16:07
 */

namespace app\common\logic\member;


use app\common\model\member\MemberBindGroupModel;
use app\common\model\member\MemberBindTagModel;
use app\common\model\member\MemberGroupModel;
use app\common\model\member\MemberModel;
use app\common\logic\BaseLogic;
use app\common\model\member\MemberTagModel;

class MemberLogic extends BaseLogic
{
    public $mm = null;

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->mm = new MemberModel();
    }

    /**
     * 获取用户列表
     * @param $request
     * @param bool $isDelete
     * @param int $page
     * @param int $limit
     * @return array|bool
     */
    public function getMemberList($request,$isDelete=false,$page=1,$limit=20)
    {
        $condition=[];
        $field='*';
        if (!empty($request) && is_array($request)) {
            foreach ($request as $k => $v) {
                $request[$k] = trim($v);
            }
            if (!empty($request['realname'])) {
                $condition[] = ['realname', 'like', '%' . $request['realname'] . '%'];
            }
            if (!empty($request['phone'])) {
                $condition[] = ['phone', '=', $request['phone']];
            }
            if (!empty($request['email'])) {
                $condition[] = ['email', '=', $request['email']];
            }
            if (!empty($request['ip'])) {
                $condition[] = ['register_ip', '=', $request['ip']];
            }
            if (!empty($request['nickname'])) {
                $condition[] = ['nickname', 'like', '%' . $request['nickname'] . '%'];
            }
            if (!empty($request['reg_ip'])) {
                $condition[] = ['register_ip', 'like', $request['reg_ip']];
            }
            if (!empty($request['start_time']) && !empty($request['end_time'])){
                $startTime=strtotime($request['start_time']);
                $dndTime=strtotime($request['end_time']);
                if ($startTime < $dndTime){
                    $condition[] = ['register_time', 'between', [$request['start_time'],$request['end_time']] ];
                }
            }
        }
        if ($isDelete){
            $condition[] = ['is_del', '=', 1];
        }else{
            $condition[] = ['is_del', '=', 0];
        }
        if($limit>999){
            $limit=20;
        }
        if(!$res=$this->mm->getMemberList($condition,$field,$limit)){
            $this->setError(5001,'获取数据列表失败');
            return false;
        }
        /*if(!$res=$this->mm->dataList($condition,$field,$limit)){
            $this->setError(5001,'获取数据列表失败');
            return false;
        }*/
        return $res;
    }

    /**
     * 设置用户启用状态
     * @param $memberId
     * @param $status
     * @return bool
     * @throws \think\exception\PDOException
     */
    public function setMemberStatus($memberId,$status)
    {
        if (is_array($memberId)){
            foreach ($memberId as $k=>$v){
                $memberId[$k]=(int)$v;
                if (!$memberId[$k]){
                    unset($memberId[$k]);
                }
            }
        }else{
            $memberId=(int)$memberId;
        }
        if (!$memberId){
            return false;
        }
        $condition=[
            'id' => $memberId
        ];
        $data=[
            'status' => $status
        ];
        $this->startTrans();
        try{
            $msg=$this->mm->saveData($condition,$data);
            if (!$msg){
                $this->setError(5002,'设置用户启用状态失败');
                $this->rollback();
                return false;
            }
        }catch (\Exception $e){
            $this->rollback();
            $this->setError(9999,$e->getMessage());
            return false;
        }
        $this->commit();
        return true;
    }


    /**
     * 新增用户
     * @param $phone 手机号
     * @param $password 密码
     * @param $email 邮箱
     * @param $status 启用状态
     * @param array $tag 标签
     * @param int $group 用户组
     * @return bool
     * @throws \think\exception\PDOException
     */
    public function addMember($phone,$password,$email,$status,$tag=[],$group=0)
    {
        if ($this->mm->dataCount(['phone'=>$phone])){
            $this->setError(5010,'该手机用户已存在');
            return false;
        }
        if ($email){
            if ($this->mm->dataCount(['email'=>$email])){
                $this->setError(5011,'该邮箱用户已存在');
                return false;
            }
        }
        $data=[
            'phone' => $phone,
            'password' => pwdEncryption($password),
            'email' => $email,
            'status' => $status,
            'register_ip' => request()->ip(),
            'register_time' => time(),
        ];
        $this->startTrans();
        try{
            if ($group){
                $mgm=new MemberGroupModel();
                if(!$mgm->dataCount(['id' => $group])){
                    $this->setError(5500,'无效的用户组');
                    return false;
                }
            }

            if(!$memberId=$this->mm->addData($data)){
                $this->rollback();
                $this->setError(5012,'新增用户失败');
                return false;
            }
            //绑定标签，如果标签不存在则新增标签
            if ($tag){
                if (is_array($tag)){
                    $mtl = new MemberTagLogic();
                    $mbtm = new MemberBindTagModel();
                    foreach ($tag as $k=>$v){
                        $tagInfo='';
                        $tagId='';
                        if(!$tagInfo=$mtl->getMemberTagInfo($v,'name')){
                            if ($tagInfo === false){
                                $this->rollback();
                                $this->setError($mtl->getErrorCode(),$mtl->getError());
                                return false;
                            }
                            if(false === $tagId = $mtl->addMemberTag($v,'')){
                                $this->rollback();
                                $this->setError(5018,'新增标签时失败');
                                return false;
                            }
                        }
                        $tagId = !empty($tagInfo['id']) ? $tagInfo['id'] : $tagId;
                        if(!$mbtm->addData(['tag_id'=>$tagId,'member_id'=>$memberId])){
                            $this->rollback();
                            $this->setError(5019,'给用户添加标签时失败');
                            return false;
                        }
                    }

                }
            }
            //绑定组
            if ($group){
                $mbgm=new MemberBindGroupModel();
                if(!$mbgm->addData(['group_id' => $group, 'member_id'=>$memberId])){
                    $this->rollback();
                    $this->setError(5020,'给用户添加组时失败');
                    return false;
                }
            }
            //添加sid
            $this->mm->saveData(['id'=>$memberId],['sid'=>pwdEncryption('hx' . $memberId . 'sid')]);
            $this->commit();
        }catch(\Exception $e){
            dump($e->getMessage());
            $this->rollback();
            $this->setError(9999,$e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * 更新用户基本信息
     * @param $memberId 用户id
     * @param $phone
     * @param $password
     * @param $email
     * @param $status
     * @return bool
     * @throws \think\exception\PDOException
     */
    public function updateMemberBaseInfo($memberId,$phone,$password='',$email,$status,$tag=[],$group=0)
    {
        if (!$userInfo=$this->mm->dataFind(['id'=>$memberId],'phone,email')){
            $this->setError(5000,'无效的用户');
            return false;
        }
        if ($this->mm->dataCount(['phone'=>$phone,'id'=>['<>',$userInfo['phone']]])){
            $this->setError(5010,'该手机用户已存在');
            return false;
        }
        if ($email){
            if ($this->mm->dataCount(['email'=>$email,'id'=>['<>',$userInfo['email']]])){
                $this->setError(5011,'该邮箱用户已存在');
                return false;
            }
        }
        $data=[
            'phone' => $phone,
            'email' => $email,
            'status' => $status,
            'modify_time' => time(),
        ];
        if ($password){
            $data['password'] = pwdEncryption($password);
        }
        $this->startTrans();
        try{

            if(!$this->mm->saveData(['id'=>$memberId],$data)){
                $this->rollback();
                $this->setError(5012,'更新用户信息失败');
                return false;
            }

            $mtl = new MemberTagLogic();
            $mbtm = new MemberBindTagModel();
            if(false===$mbtm->deleteData(['member_id'=>$memberId])){
                $this->rollback();
                $this->setError($mtl->getErrorCode(),$mtl->getError());
                return false;
            }
            //绑定标签，如果标签不存在则新增标签
            if ($tag){
                if (is_array($tag)){
                    if(false===$mbtm->deleteData(['member_id'=>$memberId])){
                        $this->rollback();
                        $this->setError($mtl->getErrorCode(),$mtl->getError());
                        return false;
                    }

                    foreach ($tag as $k=>$v){
                        $tagId='';
                        $tagInfo=[];
                        if(!$tagInfo=$mtl->getMemberTagInfo($v,'name')){
                            if ($tagInfo === false){
                                $this->rollback();
                                $this->setError($mtl->getErrorCode(),$mtl->getError());
                                return false;
                            }
                            if(false === $tagId = $mtl->addMemberTag($v,'')){
                                $this->rollback();
                                $this->setError(5018,'新增标签时失败');
                                return false;
                            }
                        }

                        $tagId = !empty($tagInfo['id']) ? $tagInfo['id'] : $tagId;

                        if(!$mbtm->addData(['tag_id'=>$tagId,'member_id'=>$memberId])){
                            $this->rollback();
                            $this->setError(5019,'给用户添加标签时失败');
                            return false;
                        }
                    }

                }
            }

            $mbgm=new MemberBindGroupModel();
            //绑定组
            if ($group){
                $mgm=new MemberGroupModel();
                if(!$mgm->dataCount(['id' => $group])){
                    $this->rollback();
                    $this->setError(5500,'无效的用户组');
                    return false;
                }
                //绑定的组不存在时
                if(!$mbgm->dataCount(['group_id' => $group,'member_id'=>$memberId])){
                    //从未绑定过分组
                    if (!$mbgm->dataCount(['member_id'=>$memberId])){
                        if(!$mbgm->addData(['member_id'=>$memberId,'group_id' => $group])){
                            $this->rollback();
                            $this->setError(5020,'给用户添加组时失败');
                            return false;
                        }
                    }else{
                        if(false===$mbgm->saveData(['member_id'=>$memberId],['group_id' => $group])){
                            $this->rollback();
                            $this->setError(5021,'给用户更换组时失败');
                            return false;
                        }
                    }
                }
            }else{
                if(false===$mbgm->deleteData(['member_id'=>$memberId])){
                    $this->rollback();
                    $this->setError(5021,'给用户更换组时失败');
                    return false;
                }
            }

            $this->commit();
        }catch(\Exception $e){
            $this->rollback();
            $this->setError(9999,$e->getMessage());
            return false;
        }
        return true;
    }


    /**
     * 获取用户基本信息
     * @param $memberId
     * @param $idType
     * @return array|bool|null|\PDOStatement|string|\think\Model
     */
    public function getMemberBaseInfo($memberId,$idType)
    {
        $condition=[];
        if ($idType=='id'){
            $condition=['id'=>$memberId];
        }elseif($idType=='sid'){
            $condition=['sid'=>$memberId];
        }else{
            $this->setError(5031,'无效的id类型');
            return false;
        }
        $mInfo=$this->mm->dataFind($condition);
        if (!$mInfo){
            $this->setError(5032,'用户不存在');
            return false;
        }
        return $mInfo;
    }

    /**
     * 删除用户
     * @param $memberId 用户id
     * @param bool $real true真删除 false软删除
     * @return bool
     * @throws \think\exception\PDOException
     */
    public function deleteMember($memberId,$real=false)
    {
        $condition=['id'=>$memberId];
        if (!$this->mm->dataCount($condition)){
            $this->setError(5000,'无效的用户');
            return false;
        }
        $this->startTrans();
        try{
            if ($real===true){
                if(false===$this->mm->deleteData($condition)){
                    $this->rollback();
                    $this->setError(9999,$this->mm->getError());
                    return false;
                }
                //删除用户已绑定的数据
                if(!$this->deleteMemberBindData($memberId)){
                    $this->rollback();
                    $this->setError(5700,'解绑用户数据失败');
                    return false;
                }
            }else{
                $data=[
                    'is_del'=> 1,
                    'delete_time' => time(),
                ];
                if(false===$this->mm->saveData($condition,$data)){
                    $this->rollback();
                    $this->setError(9999,$this->mm->getError());
                    return false;
                }
            }
            $this->commit();
        }catch(\Exception $e){
            $this->rollback();
            $this->setError(9999,$e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * 删除用户时解绑绑定的数据
     * @param $memberId
     * @return bool
     */
    public function deleteMemberBindData($memberId)
    {
        $mgl=new MemberGroupLogic();
        if(false === $mgl->setMemberUnbindGroup($memberId)){
            $this->setError($mgl->getErrorCode(),$mgl->getError());
            return false;
        }
        $mtl=new MemberTagLogic();
        if(false === $mtl->setMemberUnbindTag($memberId)){
            $this->setError($mtl->getErrorCode(),$mtl->getError());
            return false;
        }
        return true;
    }

    /**
     * 恢复软删除的用户
     * @param $memberId
     * @return bool
     * @throws \think\exception\PDOException
     */
    public function restoreDeleteMember($memberId)
    {
        $condition=['id'=>$memberId];
        if (!$this->mm->dataCount($condition)){
            $this->setError(5000,'无效的用户');
            return false;
        }
        $this->startTrans();
        try{
            $data=[
                'is_del' => 0,
            ];
            if(false===$this->mm->saveData($condition,$data)){
                $this->rollback();
                $this->setError(9999,$this->mm->getError());
                return false;
            }
            $this->commit();
        }catch(\Exception $e){
            $this->rollback();
            $this->setError(9999,$e->getMessage());
            return false;
        }
        return true;
    }



























    //----------------------------------------------------------------
    //------------------     用户组    --------------------------------
    //----------------------------------------------------------------

    /**
     * 用户绑定组
     * @param $memberId 用户id
     * @param $groupId 组id
     * @return bool
     * @throws \think\exception\PDOException
     */
    public function memberBindGroup($memberId,$groupId)
    {
        $mgm=new MemberGroupModel();
        if(!$mgm->dataCount(['id'=>$memberId])){
            $this->setError(5000,'用户不存在');
            return false;
        }
        $mbgm=new MemberBindGroupModel();
        if(!$mbgm->dataCount(['id'=>$groupId])){
            $this->setError(5500,'组不存在');
            return false;
        }
        $data=[
            'member_id' => $memberId,
            'group_id' => $groupId,
        ];
        $this->startTrans();
        try{
            $msg=$mbgm->addData($data);
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
            $mgm=new MemberGroupModel();
            $msg=$mgm->dataList($condition,'id,group_name,description', $limit);
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
            $mgm=new MemberGroupModel();
            $msg=$mgm->dataFind(['id'=>$memberId]);
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
        $mgm=new MemberGroupModel();
        if($mgm->dataCount(['group_name'=>$name])){
            $this->setError(5510,'该用户组已存在');
            return false;
        }
        $this->startTrans();
        try{
            $msg=$mgm->addData($data);
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

        $mgm=new MemberGroupModel();

        if($mgm->dataCount([['group_name','=',$name],['id','<>',$groupId]])){
            $this->setError(5510,'该用户组已存在');
            return false;
        }

        $this->startTrans();
        $condition=['id' => $groupId];
        try{
            if(!$mgm->dataCount($condition)){
                $this->rollback();
                $this->setError(5500,'组不存在');
                return false;
            }
            $msg=$mgm->saveData($condition,$data);
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
        $mgm=new MemberGroupModel();
        $condition=['id' => $groupId];
        try{
            if(!$mgm->dataCount($condition)){
                $this->rollback();
                $this->setError(5500,'组不存在');
                return false;
            }
            $msg=$mgm->deleteData($condition);
            if (false===$msg){
                $this->rollback();
                $this->setError(5503,'添加用户组失败');
                return false;
            }
            //用户解绑组
            $mbgm=new MemberBindGroupModel();
            $msg2=$mbgm->deleteData(['group_id'=>$groupId]);
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
        $mgm=new MemberGroupModel();
        $msg=$mgm->getMemberBindGroup($memberId,$merge);
        if (!$msg){
            $this->setError(5602,'获取用户组失败');
            return false;
        }
        return $msg;
    }
}