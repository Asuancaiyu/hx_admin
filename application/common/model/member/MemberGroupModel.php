<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/11/28
 * Time: 17:39
 */

namespace app\common\model\member;


use app\common\model\DataModel;
use think\Db;

class MemberGroupModel extends DataModel
{
    public $table='member_group';

    /**
     * 获取用户绑定的组
     * @param $memberId
     * @param $merge 是否和未绑定的合并
     * @return array|bool|\PDOStatement|string|\think\Collection
     */
    public function getMemberBindGroup($memberId,$merge)
    {
        $mbgm = new  MemberBindGroupModel();
        try {
            $bindGroupData = Db::name($mbgm->vTable)
                ->where(['member_id'=>$memberId])
                ->field('group_id,member_id,group_name,description')
                ->select();
            $data = $bindGroupData;

            if ($merge) {
                $groupData = Db::name($this->table)
                    ->field('id as group_id,group_name,description')
                    ->select();
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
            $this->setError(9999,$e->getMessage());
            return false;
        }
        return $data;
    }
}