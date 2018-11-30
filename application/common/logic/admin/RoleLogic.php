<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/11/2
 * Time: 17:25
 */

namespace app\common\logic\admin;


use app\common\model\admin\AdminBindRoleModel;
use app\common\model\admin\auth\AuthOperationModel;
use app\common\model\admin\auth\RoleBindOperationModel;
use app\common\model\admin\GroupBindRoleModel;
use app\common\model\admin\RoleModel;
use think\Model;

class RoleLogic extends Model
{
    /**
     * 权限集合
     * @var array
     */
    protected $authArr=[
        'operation' => null,
        'menu' => null,
    ];
    /**
     * 角色数据模型
     * @var RoleModel|null
     */
    protected $MRole=null;

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->MRole=new RoleModel();
    }

    /**
     * 权限校验，是否为有效的权限
     * @return bool
     */
    public function authVerify()
    {
        if ($this->authArr['operation']) {
            $AOMCondition = [
                'id' => $this->authArr['operation'],
            ];
            //查询勾选权限是否合法
            $AOMCount = (new AuthOperationModel())->dataCount($AOMCondition);
            if ($AOMCount != count($this->authArr['operation'])) {
                $this->error = 3100;//勾选了无效的权限
                return false;
            }
        };
        if ($this->authArr['menu']) {

        }
        return true;
    }

    /**
     * 获取角色列表
     * @param array $request
     * @param int $page
     * @param int $limitLength
     * @return array
     */
    public function getRoleList($request=[],$page=1,$limitLength=20)
    {
        $condition=[];
        if(!empty($request['roleName'])){
            $condition[]=['role_name','like','%'.$request['roleName'].'%'];
        }
        $msg=$this->MRole->dataList($condition,'*');
        return $msg;
    }

    /**
     * 添加角色
     * @param $name
     * @param $desc
     * @param $AOperation //操作权限
     * @param $AMenu //菜单权限
     * @return bool
     */
    public function addRole($name,$desc,$AOperation,$AMenu)
    {
        if (!$name || !$desc){
            $this->error=1000;
            return false;
        }
        if (!is_array($AOperation)){
            $this->error='请勾选操作权限';
            return false;
        }
        $this->authArr['operation'] = $AOperation;
        $this->authArr['menu'] = $AOperation;

        //权限名称是否存在
        if ($this->MRole->dataCount(['role_name'=>$name])){
            $this->error=3101;//角色名已存在，禁止重复添加
            return false;
        };
        if (!$this->authVerify()){
            return false;
        }
        $msg=$this->MRole->addRole($name,$desc, $this->authArr);

        return $msg;
    }

    /**
     * 获取角色信息
     * @param $name
     * @return bool
     */
    public function queryAuthName($name)
    {
        $condition=[
            'role_name' => $name
        ];
        $msg=$this->MRole->dataCount($condition);
        if ($msg){
            $this->error = 3102;//角色名称已存在
            return false;
        }
        return true;
    }

    /**
     * 获取角色基本信息
     * @param $id
     * @return bool
     */
    public function getRoleBaseInfo($id)
    {
        $condition=[
            'id' => $id,
        ];
        $msg=$this->MRole->dataFind($condition);
        if (!$msg){
            $this->error = 3000;//无效的角色ID
            return false;
        }
        return $msg;
    }

    /**
     * 保存角色信息
     * @param $rId 角色id
     * @param $name 角色名称
     * @param $desc 角色说明
     * @param $AOperation 操作权限
     * @param $AMenu 菜单权限
     * @return bool
     */
    public function saveRole($rId,$name,$desc,$AOperation,$AMenu)
    {
        if (!$name || !$desc){
            $this->error=1000;
            return false;
        }
        if (!is_array($AOperation)){
            $this->error=3104;//请勾选操作权限
            return false;
        }
        $RM=new RoleModel();
        if(!$RM->dataFind(['id'=>$rId],'role_name')){
            $this->error = 3000;//角色不存在
            return false;
        }
        if ($RM->dataFind([['id','neq',$rId],['role_name','=',$name]])){
            $this->error = 3102;//角色已存在
            return false;
        }
        $this->authArr['operation'] = $AOperation;
        $this->authArr['menu'] = $AMenu;
        if (!$this->authVerify()){
            return false;
        }
        $msg=$this->MRole->saveRoleInfo($rId,$name,$desc,$this->authArr);
        if (!$msg){
            $this->error=$this->MRole->getError();
        }
        return $msg;
    }

    /**
     * 删除角色
     * @param $rId
     * @return bool
     * @throws \think\exception\PDOException
     */
    public function doRoleDelete($rId)
    {
        if (is_array($rId)) {
            foreach ($rId as $k => $v) {
                $uid[$k] = (int)$v;
                if (!$uid[$k]) {
                    unset($uid[$k]);
                }
            }
        } else {
            $rId = (int)$rId;
        }
        if (!$rId) {
            $this->error = 3000;//无效的角色ID
            return false;
        }
        $this->startTrans();
        try {
            $msg = $this->MRole->deleteRole($rId);
            if (!$msg) {
                $this->error = $this->MRole->getError();
                $this->rollback();
                return false;
            }
            if(false===(new RoleBindOperationModel())->deleteData(['role_id'=>$rId])){
                $this->rollback();
                $this->error=3400;//在删除已绑定权限时失败
                return false;
            }
            if(false===(new GroupBindRoleModel())->deleteData(['role_id'=>$rId])){
                $this->rollback();
                $this->error=3401;//在删除已绑定组时失败
                return false;
            }
            if(false===(new AdminBindRoleModel())->deleteData(['role_id'=>$rId])){
                $this->rollback();
                $this->error=3402;//在删除已绑定管理员时失败
                return false;
            }
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            $this->error = 9999;
            return false;
        }
        return true;
    }

    /**
     * 更新
     * @return int|string
     */
    public function updateRoleInfo()
    {
        $condition=[];
        $data=[];
        $msg=$this->MRole->saveData($condition,$data);
        return $msg;
    }


    //-------------------------------------------------------------------------
    //---------------------    角色权限   -------------------------------------
    //-------------------------------------------------------------------------


    /**
     * 获取角色绑定的功能权限
     * @param $roleId
     * @param bool $merge 是否和未绑定的权限进行合并
     * @return array|bool
     */
    public function getRoleBindOperation($roleId,$merge=false)
    {
        $ABM = new RoleBindOperationModel();
        $AM = new AuthOperationModel();
        $RM = new RoleModel();
        if (!$RM->dataFind(['id' => $roleId],'id')){
            $this->error=3000;//无效的角色ID
            return false;
        }
        //已绑定权限和未绑定权限进行合并
        if ($merge){
            $authData=$AM->dataList([],'id,pid,name,url,sort,open,is_parent');
            if (!$authData['count']){
                return [];
            }
            $authData=$authData['data'];
            $bindAuthData=$ABM->getRoleBindAuthView($roleId,'auth_id as id,pid,name,url,sort,open,is_parent');
            if ($bindAuthData){
                foreach ($authData as $k1=>$v1){
                    foreach ($bindAuthData as $k2=>$v2) {
                        if ($v1['id'] == $v2['id']){//找到对应的组就进行替换
                            $v2['checked']=1;
                            $authData[$k1]=$v2;
                            unset($bindAuthData[$k2]);
                        }
                    }
                }
            }
        }else{
            $authData=$ABM->getRoleBindAuthView($roleId,'auth_id as id,pid,name,url,sort,open,is_parent');
        }
        return $authData;
    }

    /**
     * 给角色绑定功能权限
     * @param $roleId 角色id
     * @param $authId 权限id
     * @return bool
     */
    public function setRoleBindOperation($roleId,$authId)
    {
        $RM = new RoleModel();
        if (!$RM->dataFind(['id' => $roleId],'id')){
            $this->error=3000;//无效的角色ID
            return false;
        }
        $AM = new AuthOperationModel();
        if (!$AM->dataFind(['id'=>$authId],'id')){
            $this->error=3001;//无效的权限ID
            return false;
        }
        $data=[
            'role_id' => $roleId,
            'auth_id' => $authId,
        ];
        $ABM = new RoleBindOperationModel();
        $msg=$ABM->addData($data);
        if (!$msg){
            $this->error=3002;//角色绑定权限失败
            return false;
        }
        return true;
    }

    /**
     * 解绑角色功能权限
     * @param $roleId 角色id
     * @param $authId 权限id
     * @return bool
     */
    public function setRoleUntiedOperation($roleId,$authId)
    {
        $RM = new RoleModel();
        if (!$RM->dataFind(['id' => $roleId],'id')){
            $this->error=3000;//无效的角色ID
            return false;
        }
        $AM = new AuthOperationModel();
        if (!$AM->dataFind(['id'=>$authId],'id')){
            $this->error=3001;//无效的权限ID
            return false;
        }
        $condition=[
            'role_id' => $roleId,
            'auth_id' => $authId,
        ];
        $ABM = new RoleBindOperationModel();
        $msg=$ABM->deleteData($condition);
        if(!$msg){
            $this->error=3003;//解绑失败，当前角色未绑定该权限
            return false;
        }
        return true;
    }

    /**
     * 获取角色权限
     * @param $roleId
     * @param string $authType
     * @return array|bool|null|\PDOStatement|string|\think\Collection
     */
    public function getRoleAuth($roleId,$authType='all')
    {
        //权限类型
        $authTypeData=[
            'all',
            'operation',
            'menu',
        ];
        if (!$roleId){
            $this->error=3000;//缺少角色id
            return false;
        }
        if(!in_array($authType,$authTypeData)){
            $this->error=3300;//无效的权限类型
            return false;
        }
        //均已数组形式传入
        if (is_array($roleId)){
            foreach ($roleId as $k=>$v){
                $roleId[$k]=(int)$v;
            }
        }else{
            $roleId=[(int)$roleId];
        }
        $data=null;
        if ($authType == 'all'){
            $data = $this->getRoleAuthAll($roleId);
        }else{
            $authModel=null;
            switch ($authType){
                case 'operation':
                    //获取功能权限
                    $authModel =new RoleBindOperationModel();
                    break;
                case 'menu':
                    //$authModel = new RoleBindMenuModel();
                    break;
                default:
                    $this->error=3300;//无效的权限类型
                    return false;
            }
            $data = $authModel->getRoleBindAuthView($roleId);
        }
        return $data;
    }

    /**
     * 获取角色所有权限
     * @param $roleId
     * @return array|bool
     */
    protected function getRoleAuthAll($roleId)
    {
        //获取功能权限
        $RBOM = new RoleBindOperationModel();
        if (false === $OAuthData = $RBOM->getRoleBindAuthView($roleId)){
            $this->error=$RBOM->getError();
            return false;
        }
        //菜单功能

        $data=[
            'operation' => $OAuthData ?: null,
        ];
        return $data;
    }
}