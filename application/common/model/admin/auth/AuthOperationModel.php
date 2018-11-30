<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/11/8
 * Time: 11:38
 */
namespace app\common\model\admin\auth;

use app\common\model\DataModel;
use think\Db;

class AuthOperationModel extends DataModel
{
    public $table='admin_p_operate';

    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * @param array $condition
     * @param string $field
     * @param int $limitLength
     * @return array|bool
     */
    public function dataList(array $condition=[],string $field='*',int $limitLength=10000)
    {
        try{
            $list=Db::name($this->table)
                ->field($field)
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