<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/11/15
 * Time: 16:06
 */

namespace app\common\model\admin;


use app\common\model\DataModel;
use think\Db;

class GroupBindRoleModel extends DataModel
{
    protected $table='admin_group_bind_role';
    protected $viewTable='view_admin_group_bind_role';

    public function getGroupBindRole($groupId)
    {
        $condition=[
            'group_id'=>$groupId
        ];
        $bindField=[
            'group_id' => 'group_id',

        ];
        $roleField=[
            'role_name' => 'role_name',
            'id' => 'role_id',
            'description' => 'description',
        ];
        try {
            $rm = new RoleModel();
            $data = Db::view($this->table . ' b', $bindField)
                ->view($rm->table . ' r', $roleField, 'b.role_id = r.id', 'LEFT')
                ->where($condition)
                ->select(false);
            return $data;
        }catch(\Exception $e){
            $this->error=$e->getMessage();
            return false;
        }
    }

    /**
     * @param $groupId
     * @param string $field
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function getGroupBindRoleView($groupId,$field='group_id,group_name,role_id,role_name,role_desc')
    {
        try {
            $msg = Db::name($this->viewTable)
                ->field($field)
                ->where(['group_id' => $groupId])
                ->select();
            return $msg;
        }catch(\Exception $e){
            $this->error=$e->getMessage();
            dump($e->getMessage());
            return false;
        }
    }
}