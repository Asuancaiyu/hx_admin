<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/11/19
 * Time: 18:25
 */

namespace app\common\model\admin;


use app\common\model\DataModel;
use think\Db;

class AdminBindGroupModel extends DataModel
{
    protected $table = 'admin_bind_group';

    /**
     * @param $adminId
     * @param $groupId
     * @return bool
     */
    public function doAdminBindGroup($adminId,$groupId)
    {

        $condition=[
            'admin_id' => $adminId,
            'group_id' => $groupId,
        ];
        try{
            if(Db::name($this->table)->where($condition)->count()){
                return false;
            }
            unset($condition);
            $data=[];
            foreach ($groupId as $k=>$v){
                $data[]=[
                    'admin_id' => $adminId,
                    'group_id' => $v,
                    'create_time' => time(),
                ];
            }
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
     * @param bool $merge 是否与组列表合并
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function getAdminBindGroup($adminId,$merge=false)
    {
        $groupTable=(new GroupModel())->table;
        try {
            $bindGroupData = Db::view($this->table . ' a')
                ->view($groupTable . ' b', 'id as group_id,group_name,pid,is_parent,open,ban_delete', 'a.group_id=b.id', 'LEFT')
                ->where(['a.admin_id' => $adminId])
                ->select();
            $data = $bindGroupData;

            if ($merge) {
                $groupData = Db::name($groupTable)->field('id as group_id,group_name,pid,is_parent,open,ban_delete')->select();
                if ($groupData) {
                    if ($bindGroupData){
                        foreach ($bindGroupData as $k1 => $v1) {
                            foreach ($groupData as $k2 => $v2) {
                                if ($v1['group_id'] == $v2['group_id']) {
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
     * 解绑角色
     * @param $adminId
     * @return bool
     */
    public function setAdminUntiedGroup($adminId)
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