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

class MemberTagModel extends DataModel
{
    public $table='member_tag';

    /**
     * 获取用户绑定的标签
     * @param $memberId
     * @param $merge
     * @return bool
     */
    public function getMemberBindTag($memberId,$merge=0)
    {
        $mbtm = new  MemberBindTagModel();
        try {
            $bindTagData = Db::name($mbtm->vTable)
                ->where(['member_id'=>$memberId])
                ->field('tag_id,member_id,tag_name,description')
                ->select();
            $data = $bindTagData;

            if ($merge) {
                $groupData = Db::name($this->table)
                    ->field('id as tag_id,tag_name,description')
                    ->select();
                if ($groupData) {
                    if ($bindTagData){
                        foreach ($bindTagData as $k1 => $v1) {
                            foreach ($groupData as $k2 => $v2) {
                                if ($v1['tag_id'] == $v2['tag_id']) {
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