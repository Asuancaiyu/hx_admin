<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/11/2
 * Time: 16:48
 */
namespace app\common\model;


use think\Db;
use think\Model;

class DataModel extends BaseModel
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * 获取一条数据
     * @param array $condition
     * @param string $field
     * @return array|bool|null|\PDOStatement|string|Model
     */
    public function dataFind(array $condition,string $field='*'){
        try{
            $data=Db::name($this->table)
                ->field($field)
                ->where($condition)
                ->find();
            return $data;
        }catch (\Exception $e){
            $this->error=$e->getMessage();
            return false;
        }
    }

    /**
     * 获取多条数据
     * @param array $condition
     * @param string $field
     * @param int $limitLength
     * @return array|bool
     */
    public function dataList(array $condition=[],string $field='*',int $limitLength=20)
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

    /**
     * 获取查询总数
     * @param $condition
     * @return float|string
     */
    public function dataCount($condition)
    {
        try{
            $msg=Db::name($this->table)->where($condition)->count();
            return $msg;
        }catch (\Exception $e){
            $this->error=$e->getMessage();
            return false;
        }

    }

    /**
     * 添加数据
     * @param $data
     * @return int|string
     */
    public function addData($data)
    {
        try{
            $msg=Db::name($this->table)->insertGetId($data);
            return $msg;
        }catch (\Exception $e){
            $this->error=$e->getMessage();
            return false;
        }

    }

    /**
     * 插入多条数据
     * @param $data
     * @return bool|int|string
     */
    public function addDataAll($data)
    {
        try{
            $msg=Db::name($this->table)->insertAll($data);
            return $msg;
        }catch (\Exception $e){
            $this->error=$e->getMessage();
            return false;
        }
    }

    /**
     * 更新保存信息
     * @param $condition
     * @param $data
     * @return bool|int|string
     */
    public function saveData($condition,$data)
    {
        try{
            $msg=Db::name($this->table)
                ->where($condition)
                ->update($data);
            return $msg;
        }catch (\Exception $e){
            $this->error=$e->getMessage();
            return false;
        }
    }

    /**
     * 删除
     * @param $condition
     * @return bool|int
     */
    public function deleteData($condition)
    {
        try{
            $msg=Db::name($this->table)
                ->where($condition)
                ->delete();
            return $msg;
        }catch (\Exception $e){
            $this->error=$e->getMessage();
            return false;
        }
    }


}