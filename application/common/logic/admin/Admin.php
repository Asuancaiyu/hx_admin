<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/9/29
 * Time: 16:44
 */
namespace app\common\logic\admin;


use app\common\model\admin\AdminBindGroupModel;
use app\common\model\admin\AdminModel;
use app\common\model\admin\AdminBindRoleModel;
use app\common\model\admin\GroupModel;
use app\common\model\admin\RoleModel;
use think\Model;

class Admin extends Model
{
    /**
     * @var RoleModel|null
     */
    protected $MA=null;

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->MA=new AdminModel();
    }

    /**
     * 添加管理员
     * @param $username
     * @param $pwd
     * @param $realname
     * @param $phone
     * @param $group
     * @param $role
     * @param $status
     * @return bool
     * @throws \think\exception\PDOException
     */
    public function adminAdd($username,$pwd,$realname,$phone,$group,$role,$status)
    {
        $data=[
            'username'  =>  $username,
            'password'  =>  $pwd,
            'phone'     =>  $phone,
            'realname'  =>  $realname,//实名
            'status'    =>  $status,//账号启用状态 默认0 为禁用
            'register_time' =>  time(),//注册日期
            'modify_time'   =>  time(),//修改日期
            'register_ip'   =>  request()->ip(),//注册ip
        ];
        if (verify('username',$data['username'])!==true){
            $this->error=2001;//用户名称格式不正确
            return false;
        }
        if (verify('password',$data['password'])!==true){
            $this->error=2002;//密码格式不正确
            return false;
        }
        $data['password']=pwdEncryption($data['password']);
        if ($data['phone'] && verify('phone',$data['phone'])!==true){
            $this->error=2003;//无效的手机号
            return false;
        }
        /*if ($data['email'] && verify('email',$data['email'])!==true){
            $this->error='无效的邮箱';
            return false;
        }
        if (!in_array($data['fex'],['man','woman','other','secret'])){
            $this->error='未选择fex';
            return false;
        }*/
        if (!in_array($data['status'],[0,1])){
            $data['status']=0;
        }

        $AM=new AdminModel();
        if($AM->dataCount(['username'=>$username])){
            $this->error=2004;//用户名已存在
            return false;
        }
        $GM=new GroupModel();
        if(count($group) != $GM->dataCount(['id'=>$group])){
            $this->error=2006;//无效的分组
            return false;
        }
        $RM=new RoleModel();
        if(count($role) != $RM->dataCount(['id'=>$role])){
            $this->error=2007;//无效的角色
            return false;
        }

        $this->startTrans();
        try{
            $id=$AM->addData($data);
            if (!$id){
                $this->error=2005;//新增用户失败
                $this->rollback();
                return false;
            }
            if(!(new AdminBindGroupModel())->doAdminBindGroup($id,$group)){
                $this->error=2008;//管理员绑定分组失败
                $this->rollback();
                return false;
            }
            if(!(new AdminBindRoleModel())->doAdminBindRole($id,$role)){
                $this->error=2009;//管理员绑定角色失败
                $this->rollback();
                return false;
            }
            $this->commit();
        }catch(\Exception $e){
            $this->rollback();
            $this->error=9999;
            return false;
        }
        return true;
    }

    /**
     * 更新用户信息
     * @param $adminId
     * @param $username
     * @param $pwd
     * @param $realname
     * @param $phone
     * @param $group
     * @param $role
     * @param $status
     * @return bool
     * @throws \think\exception\PDOException
     */
    public function adminInfoUpdate($adminId,$username,$pwd,$realname,$phone,$group,$role,$status){
        $adminInfo=$this->MA->dataFind(['id'=>$adminId]);
        if (!$adminInfo){
            $this->error = 2100;//无效的管理员
            return false;
        }
        if (verify('username',$username)!==true){
            $this->error=2001;//用户名称格式不正确
            return false;
        }
        if ($pwd){
            if (verify('password',$pwd)!==true){
                $this->error=2002;//密码格式不正确
                return false;
            }
            $pwd=pwdEncryption($pwd);
        }
        if ($phone && verify('phone',$phone)!==true){
            $this->error=2003;//无效的手机号
            return false;
        }
        if (!in_array($status,[0,1])){
            $status=0;
        }

        if ($adminInfo['username']!=$username){
            if($this->MA->dataCount([['username','=',$username],['id','<>',$adminId]])){
                $this->error = 2004;//该用户名已存在
                return false;
            }
        }

        $GM=new GroupModel();
        if(count($group) != $GM->dataCount(['id'=>$group])){
            $this->error=2006;//无效的分组
            return false;
        }
        $RM=new RoleModel();
        if(count($role) != $RM->dataCount(['id'=>$role])){
            $this->error=2007;//无效的角色
            return false;
        }
        $data=[
            'username' => $username,
            'phone' => $phone,
            'realname' => $realname,
            'status' => $status,
        ];
        if ($pwd){
            $data['password']=$pwd;
        }
        $this->startTrans();
        try{
            $msg=$this->MA->saveData(['id'=>$adminId],$data);
            /*if (!$msg){
                $this->error=2103;//更新管理员基本信息失败
                $this->rollback();
                return false;
            }*/
            $ABGM=new AdminBindGroupModel();
            $ABGM->deleteData(['admin_id'=>$adminId]);
            if(!$ABGM->doAdminBindGroup($adminId,$group)){
                $this->error=2008;//管理员绑定分组失败
                $this->rollback();
                return false;
            }
            //删除现绑定角色，再绑定新角色
            $ABRM=new AdminBindRoleModel();
            $ABRM->deleteData(['admin_id'=>$adminId]);
            if(!$ABRM->doAdminBindRole($adminId,$role)){
                $this->error=2009;//管理员绑定角色失败
                $this->rollback();
                return false;
            }
            $this->commit();
        }catch(\Exception $e){
            $this->rollback();
            $this->error=9999;
            return false;
        }
        return true;

    }


    /**
     * 获取管理员基本信息
     * @param $uid
     * @return array|null|\PDOStatement|string|Model
     */
    public function adminInfo($uid)
    {
        $condition['id']=$uid;
        $info=AdminModel::adminInfo($condition);
        return $info;
    }

    /**
     * 管理员列表
     * @param $request 筛选参数
     * @param bool $isDel 是否删除
     * @param int $page 分页
     * @param int $limit 每页查询长度
     * @return array
     */
    public function adminList($request,$isDel=false,$page=1,$limit=10)
    {
        $condition=[];
        $field='*';
        if (!empty($request) && is_array($request)) {
            foreach ($request as $k => $v) {
                $request[$k] = trim($v);
            }
            if (!empty($request['username'])) {
                $condition[] = ['username', 'like', '%' . $request['username'] . '%'];
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
            if (!empty($request['nickname'])) {
                $condition[] = ['nickname', 'like', '%' . $request['nickname'] . '%'];
            }
            if (!empty($request['reg_ip'])) {
                $condition[] = ['register_ip', 'like', $request['reg_ip']];
            }
        }
        if ($isDel){
            $condition[] = ['is_del', '=', 1];
        }else{
            $condition[] = ['is_del', '=', 0];
        }
        if($limit>999){
            $limit=10;
        }
        $info=AdminModel::adminList($condition,$field,$limit);
        return $info;
    }

    /**
     * 删除用户
     * @param $uid
     * @param bool $real
     * @return bool
     */
    public function adminDelete($uid,$real=false)
    {
        if (is_array($uid)){
            foreach ($uid as $k=>$v){
                $uid[$k]=(int)$v;
                if (!$uid[$k]){
                    unset($uid[$k]);
                }
            }
        }else{
            $uid=[(int)$uid];
        }
        if (!$uid){
            $this->error=2004;//无效的管理员id
            return false;
        }
        $condition=[
            'id'=>  $uid,
        ];

        if(count($uid) != $this->MA->dataCount(['id'=>$uid])){
            $this->error=2004;//无效的管理员id
            return false;
        }
        $this->startTrans();
        try {
            if (true === $real) {
                $ABGM = new AdminBindGroupModel();
                if(false===$ABGM->setAdminUntiedGroup($uid)){
                    $this->rollback();
                    $this->error=2401;//解绑组
                    return false;
                }
                $ABGM = new AdminBindRoleModel();
                if(false===$ABGM->setAdminUntiedRole($uid)){
                    $this->rollback();
                    $this->error=2401;//解绑角色
                    return false;
                }
                if(!$this->MA->adminDelete($uid)){
                    $this->rollback();
                    $this->error=2201;//删除管理员失败
                    return false;
                }
            } else {
                $data = [
                    'is_del' => 1
                ];
                if(!$this->MA->saveData($condition, $data)){
                    $this->rollback();
                    $this->error=2202;//删除管理员失败
                    return false;
                }
            }
            $this->commit();
        }catch(\Exception $e){
            $this->error=9999;
            return false;
        }
        return true;
    }

    /**
     * 设置用户启用状态
     * @param $uid
     * @param $status
     * @return int|string
     */
    public function setAdminStatus($uid,$status)
    {
        if (is_array($uid)){
            foreach ($uid as $k=>$v){
                $uid[$k]=(int)$v;
                if (!$uid[$k]){
                    unset($uid[$k]);
                }
            }
        }else{
            $uid=(int)$uid;
        }
        if (!$uid){
            return false;
        }
        $condition=[
            'id' => $uid
        ];
        $data=[
            'status' => $status
        ];
        $msg=AdminModel::adminUpdate($condition,$data);
        if ($msg){
            return true;
        }
        return false;
    }

    /**
     * 获取管理员绑定的组
     * @param $adminId
     * @param $marge //是否和未绑定的数组合并
     * @return array|bool|\PDOStatement|string|\think\Collection
     */
    public function getAdminBindGroup($adminId,$marge=true)
    {
        if(!$this->MA->dataCount(['id'=>$adminId])){
            $this->error=2004;//无效的管理员
            return false;
        }
        $ABGM=new AdminBindGroupModel();
        $msg=$ABGM->getAdminBindGroup($adminId,$marge);
        if (!$msg){
            $this->error=2201;//获取绑定组失败
            return false;
        }
        return $msg;
    }

    /**
     * 获取管理员已绑定的角色
     * @param $adminId
     * @param $merge //是否和为绑定的角色合并
     * @return array|bool|\PDOStatement|string|\think\Collection
     */
    public function getAdminBindRole($adminId,$merge=true)
    {
        if(!$this->MA->dataCount(['id'=>$adminId])){
            return 0;
        }
        $ABRM=new AdminBindRoleModel();
        $msg=$ABRM->getAdminBindRole($adminId,$merge);
        if (!$msg){
            $this->error=2301;//获取绑定角色失败
            return false;
        }
        return $msg;
    }

    /**
     * 获取管理员所有权限
     * @param $adminId
     * @return array|bool|null|\PDOStatement|string|\think\Collection
     */
    public function getAdminAuthAll($adminId)
    {
        //获取管理员角色
        $roleArr=[];
        $role=$this->getAdminBindRole($adminId, false);
        if (false===$role){
            return false;
        }elseif($role){
            foreach ($role as $k=>$v){
                $roleArr[]=$v['role_id'];
            }
        }
        //获取组的角色
        $group=$this->getAdminBindGroup($adminId,false);
        if ($group) {
            $groupId=[];
            foreach ($group as $k=>$v){
                $groupId[]=$v['group_id'];
            }
            $GL = new GroupLogic();
            $groupRole=$GL->getGroupBindRole($groupId);
            if (false===$groupRole){
                $this->error=$GL->getError();
                return false;
            }elseif($groupRole){
                $groupRoleArr=[];
                foreach ($groupRole as $k=>$v){
                    $groupRoleArr[]=$v['role_id'];
                }
                if (!$roleArr = array_merge($roleArr, $groupRoleArr)) {
                    $this->error = 2600;//合并角色时失败
                    return false;
                }
                //角色去重复
                $roleArr=array_unique($roleArr);
            }
        }
        $adminRoleAuth=[];
        //如果有角色 就获取角色权限
        if ($roleArr) {
            //获取角色权限
            $RL = new RoleLogic();
            $adminRoleAuth = $RL->getRoleAuth($roleArr);
            if (false === $adminRoleAuth) {
                $this->error = $RL->getError();
                return false;
            }

            //权限去重复
            foreach ($adminRoleAuth as $k => $authInfo) {
                if ($authInfo){
                    $arr = [];
                    foreach ($authInfo as $k2 => $v) {
                        $arr[$k2] = $v['auth_id'];
                    }
                    $arr2 = [];
                    $arr = array_unique($arr);
                    foreach ($arr as $k3 => $v3) {
                        $arr2[] = $authInfo[$k3];
                    }
                    $adminRoleAuth[$k] = $arr2;
                }
            }
        }
        return $adminRoleAuth;
    }

    /**
     * 获取管理员拥有角色的所有权限
     * @param $adminId
     * @return array|bool|null|\PDOStatement|string|\think\Collection
     */
    public function getAdminRoleAuthAll($adminId)
    {

        $role=$this->getAdminBindRole($adminId, false);
        if (!$role){
            return false;
        }
        $roleArr=[];
        foreach ($role as $k=>$v){
            $roleArr[]=$v['role_id'];
        }
        $RL=new RoleLogic();
        $auth=$RL->getRoleAuth($roleArr);
        if (false === $auth){
            $this->error=$RL->getError();
            return false;
        }
        return $auth;
    }
}