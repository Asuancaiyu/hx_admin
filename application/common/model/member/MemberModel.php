<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/11/23
 * Time: 15:57
 */

namespace app\common\model\member;

use app\common\model\DataModel;
use think\Db;

class MemberModel extends DataModel
{
    public $table='member';

    /**
     * 获取用户列表
     * @param $condition 条件
     * @param $field
     * @param int $limitLength
     * @return array|bool
     */
    public function getMemberList($condition,$field='*',$limitLength=40)
    {
        try{
            $mbtm=new MemberBindTagModel();
            $mbgm=new MemberBindGroupModel();
            $tagCondition=[];
            if (!empty($condition['tag'])){
                $tagCondition['tag_name']=$condition['tag'];
                unset($condition['tag']);
            }
            $subTagQuery = Db::name($mbtm->vTable)
                ->alias('t')
                ->field('group_concat(tag_name)')
                ->where('t.member_id = m.id')
                ->buildSql();

            if (!empty($condition['group'])){
                $tagCondition['group_name']=$condition['group'];
                unset($condition['group']);
            }
            $subGroupQuery = Db::name($mbgm->vTable)
                ->alias('g')
                ->field('group_concat(group_name)')
                ->where('g.member_id = m.id')
                ->buildSql();
            $fieldData=[
                $field,
                $subTagQuery =>'tag_name',
                $subGroupQuery =>'group_name',
            ];
            $list=Db::name($this->table)
                ->alias('m')
                ->field($fieldData)
                ->where($condition)
                ->paginate($limitLength)
                ->each(function ($item){
                    return $item;
                });

            $data=[
                'data'=>$list->all(),//获取查询数据
                'count'=>$list->total(), // 获取总记录数
                'render'=>$list->render(), // 获取分页显示
            ];
            return $data;
        }catch (\Exception $e){
            $this->error=$e->getMessage();
            return false;
        }
    }
}