<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/9/27
 * Time: 11:55
 */
namespace app\common\model\admin;


use app\common\model\DataModel;
use think\Db;
use think\Model;

class AdminModel extends DataModel
{

    protected $table = 'admin';
    protected static $t='admin';

    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * @param $data
     * @return int|string
     */
    public static function adminAdd($data)
    {
        try{
            $msg=Db::name(self::$t)->insertGetId($data);
            return $msg;
        }catch (\Exception $e){

            return false;
        }
    }


    /**
     * 获取一条管理员信息
     * @param $condition
     * @param string $field
     * @return array|bool|null|\PDOStatement|string|Model
     */
    public static function adminFind($condition,$field='*')
    {
        try{
            $data=Db::name(self::$t)
                ->field($field)
                ->where($condition)
                ->find();
            return $data;
        }catch (\Exception $e){

            return false;
        }

    }

    /**
     * @param $condition
     * @param $field
     * @param $limitLength
     * @return array|bool
     */
    public static function adminList($condition,$field,$limitLength)
    {
        try{
            $list=Db::name(self::$t)
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

            return false;
        }

    }


    /**
     * 用户信息
     * @param $condition
     * @param string $field
     * @return array|bool|null|\PDOStatement|string|Model
     */
    public static function adminInfo($condition,$field='*')
    {
        try{
            $data=Db::name(self::$t)
                ->field($field)
                ->where($condition)
                ->find();
            return $data;
        }catch (\Exception $e){

            return false;
        }

    }

    /**
     * 更新用户信息
     * @param $condition
     * @param $data
     * @return bool|int|string
     */
    public static function adminUpdate($condition,$data)
    {
        try{
            $msg=Db::name(self::$t)
                ->where($condition)
                ->update($data);
            return $msg;
        }catch (\Exception $e){

            return false;
        }

    }

    /**
     * 删除用户
     * @param $adminId
     * @return bool|int
     */
    public function adminDelete($adminId)
    {
        try{
            $msg=Db::name($this->table)
                ->where(['id'=>$adminId])
                ->delete();
            return $msg;
        }catch (\Exception $e){
            return false;
        }

    }


}