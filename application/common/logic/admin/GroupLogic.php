<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/11/15
 * Time: 16:12
 */

namespace app\common\logic\admin;


use app\common\model\admin\GroupBindRoleModel;
use app\common\model\admin\GroupModel;
use app\common\model\admin\RoleModel;
use think\Model;

class GroupLogic extends Model
{
    protected $MG=null;
    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->MG=new GroupModel();
    }

    /**
     * 获取组
     * @param int $limitLength
     * @return array|bool
     */
    public function getGroupList($limitLength=1000)
    {
        $data=$this->MG->dataList([],'id,group_name as name,pid,open,is_parent,description',$limitLength);
        return $data;
    }

    /**
     * 添加组
     * @param $groupName
     * @param $groupDescription
     * @param array $roleId
     * @param int $parentGroupId
     * @return bool|int|string
     * @throws \think\exception\PDOException
     */
    public function addGroup($groupName,$groupDescription,$roleId=[],$parentGroupId=0)
    {
        if (!$groupName){
            $this->error=4001;//缺少组名称参数
            return false;
        }
        if (!$groupDescription){
            $this->error=4002;//缺少组说明参数
            return false;
        }
        if ($roleId){
            if (!is_array($roleId)){
                $this->error=4003;//缺少角色参数
                return false;
            }
        }

        if ($parentGroupId){
            if (!$PGInfo=$this->MG->dataFind(['id'=>$parentGroupId],'id,path')){
                $this->error=4004;//组不存在
                return false;
            }
        }
        $condition=[
            'group_name'=>$groupName,
            'pid'=>$parentGroupId,
        ];
        if($this->MG->dataCount($condition)){
            $this->error=4005;//该组已存在
            return false;
        }

        $data=[
            'group_name' => $groupName,
            'pid' => $parentGroupId,
            'description' => $groupDescription,
            'create_time' => time(),
        ];
        $this->startTrans();
        try {
            if (!$id = $this->MG->addData($data)) {
                $this->rollback();
                $this->error = 4007;//新增组失败
                return false;
            }
            $updateData = [];
            if ($parentGroupId) {
                $updateData['path'] = $PGInfo['path'] . $id . ',';
            } else {
                $updateData['path'] = ',' . $id . ',';
            }
            if (!$this->MG->saveData(['id' => $id], $updateData)) {
                $this->rollback();
                $this->error = 4008;//新增组失败
                return false;
            }
            if ($roleId) {
                if (!(new RoleModel())->dataCount(['id' => $roleId])) {
                    $this->rollback();
                    $this->error = 4006;//选择了无效的角色
                    return false;
                }
                $roleData = [];
                foreach ($roleId as $k => $v) {
                    $roleData[] = ['group_id' => $id, 'role_id' => $v];
                }
                if (!(new GroupBindRoleModel())->addDataAll($roleData)) {
                    $this->rollback();
                    $this->error = 4009;//添加角色失败
                    return false;
                }
            }
        }catch(\Exception $e){
            $this->rollback();
            $this->error=9999;
            return false;
        }
        $this->commit();
        return $id;
    }

    /**
     * 更新组信息
     * @param $groupId
     * @param $groupName
     * @param $groupDescription
     * @param $roleId
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function updateGroupInfo($groupId,$groupName,$groupDescription,$roleId)
    {
        if (!$groupId){
            $this->error=4010;//缺少组参数
            return false;
        }
        if (!$groupName){
            $this->error=4001;//缺少组名称参数
            return false;
        }
        if (!$groupDescription){
            $this->error=4002;//缺少组说明参数
            return false;
        }

        if ($roleId){
            if (!is_array($roleId)){
                $roleId=[$roleId];
            }
        }
        if (!$this->MG->dataCount(['id'=>$groupId])){
            $this->error=4004;//组不存在
            return false;
        }
        if ($roleId){
            if(!(new RoleModel())->dataCount(['id'=>$roleId])){
                $this->error=4006;//选择了无效的角色
                return false;
            }
        }
        $data=[
            'group_name' => $groupName,
            'description' => $groupDescription,
        ];
        $this->startTrans();
        try {
            $condition=$data;
            $condition['id']=$condition;
            if (!$this->MG->dataCount($condition)){
                if (false===$this->MG->saveData(['id' => $groupId], $data)) {
                    $this->rollback();
                    $this->error = 4011;//保存失败;
                    return false;
                }
            }

            $roleData = [];
            foreach ($roleId as $k => $v) {
                $roleData[] = ['group_id' => $groupId, 'role_id' => $v];
            }
            $GBRM = new GroupBindRoleModel();
            $GBRM->deleteData(['group_id' => $groupId]);
            if ($roleId) {
                if (!$GBRM->addDataAll($roleData)) {
                    $this->rollback();
                    $this->error = 4009;//添加组失败;
                    return false;
                }
            }
        }catch(\Exception $e){
            $this->rollback();
            $this->error=9999;
            return false;
        }
        $this->commit();
        return true;
    }

    /**
     * 删除组
     * @param $groupId
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function deleteGroup($groupId)
    {
        if (!$groupId){
            $this->error=4010;//缺少组id
            return false;
        }
        if(!$this->MG->dataCount(['id'=>$groupId])){
            $this->error=4004;//组不存在
            return false;
        }
        if ($this->MG->dataCount(['id'=>$groupId,'ban_delete'=>1])){
            $this->error=4016; //该组禁止删除
            return false;
        }
        $this->startTrans();
        $condition=[
            ['path','like','%,'.$groupId.',%'],
            ['ban_delete','=',0],
        ];
        try{
            if(!$this->MG->deleteData($condition)){
                $this->rollback();
                $this->error=4020; //删除组失败
                return false;
            }
            if(false===(new GroupBindRoleModel())->deleteData(['group_id'=>$groupId])){
                $this->rollback();
                $this->error=4021; //删除组失败
                return false;
            }
            $this->commit();
        }catch (\Exception $e){
            $this->rollback();
            $this->error=9999;
            return false;
        }

        return true;
    }

    /**
     * 获取组已绑定的角色信息
     * @param $groupId
     * @param $merge
     * @return array|bool|\PDOStatement|string|\think\Collection
     */
    public function getGroupBindRole($groupId,$merge=false)
    {
        if (!$groupId){
            $this->error=4010;//缺少组id
            return false;
        }
        $newData=[];
        $bindData=(new GroupBindRoleModel())->getGroupBindRoleView($groupId,'role_id,role_name');
        if(false===$bindData){
            $this->error=4030;//获取组失败
            return false;
        }

        $newData=$bindData;
        if ($merge){
            $roleData=(new RoleModel())->dataList([],'id as role_id,role_name',999);
            if (!empty($roleData['count'])){
                $roleList=$roleData['data'];
                foreach ($roleList as $k1=>$v1){
                    foreach ($bindData as $k2=>$v2) {
                        if ($v1['role_id'] == $v2['role_id']){//找到对应的组就进行替换
                            $v2['checked']=1;
                            $roleList[$k1]=$v2;
                            unset($bindData[$k2]);
                        }
                    }
                }
                $newData=$roleList;
            }else{
                $this->error=4031;//获取角色失败
                return false;
            }
        }
        return $newData;
    }

    /**
     * 获取组所有权限
     * @param $groupId
     * @return bool
     */
    public function getGroupAuthAll($groupId)
    {
        $role=$this->getGroupBindRole($groupId);
        if (!$role){
            return false;
        }
        $roleArr=[];
        foreach ($role as $k=>$v){
            $roleArr[]=$v['role_id'];
        }
        $RL=new RoleLogic();
        $auth=$RL->getRoleAuth($roleArr);
        if (!$auth){
            return false;
        }
        return $auth;
    }
}