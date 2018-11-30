<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/11/19
 * Time: 18:26
 */

namespace app\common\model\admin;


use app\common\model\DataModel;
use think\Db;

class AdminBindRoleModel extends DataModel
{
    protected $table='admin_bind_role';

    /**
     * @param $adminId
     * @param $roleId
     * @return bool
     */
    public function doAdminBindRole($adminId,$roleId)
    {
        $data=[];
        foreach ($roleId as $k=>$v){
            $data[]=[
                'admin_id' => $adminId,
                'role_id' => $v,
                'create_time' => time(),
            ];
        }
        try{
            $msg=Db::name($this->table)->insertAll($data);
            unset($data);
            if (!$msg){
                return false;
            }
        }catch (\Exception $e){
            return false;
        }
        return true;
    }
    

    /**
     * @param $adminId 管理员id
     * @param bool $merge 是否与角色列表合并
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function getAdminBindRole($adminId,$merge=false)
    {
        $roleTable=(new RoleModel())->table;
        try {
            $bindGroupData = Db::view($this->table . ' a')
                ->view($roleTable . ' b', 'id as role_id,role_name,description', 'a.role_id=b.id', 'LEFT')
                ->where(['a.admin_id' => $adminId])
                ->select();
            $data = $bindGroupData;

            if ($merge) {
                $groupData = Db::name($roleTable)->field('id as role_id,role_name,description')->select();
                if ($groupData) {
                    if ($bindGroupData){
                        foreach ($bindGroupData as $k1 => $v1) {
                            foreach ($groupData as $k2 => $v2) {
                                if ($v1['role_id'] == $v2['role_id']) {
                                    $v2['checked'] = 1;
                                    $groupData[$k2] = $v2;
                                }
                            }
                        }
                    }
                    $data = $groupData;
                }
            }
        }catch (\Exception $e){
            $this->error=9999;
            return false;
        }
        return $data;
    }

    /**
     * 解绑组
     * @param $adminId
     * @return bool
     */
    public function setAdminUntiedRole($adminId)
    {
        if (!$adminId){
            return false;
        }
        try{
            $msg=Db::name($this->table)->where(['admin_id'=>$adminId])->delete();
        }catch (\Exception $e){
            return false;
        }
        return true;
    }
}