<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/11/9
 * Time: 17:06
 */

namespace app\common\model\admin\auth;

use think\Db;

class RoleBindOperationModel extends RoleBindAuthModel
{
    public $table='role_bind_p_operate';
    public $viewTable='view_role_operate_auth';//视图

    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    public function sonsTree($arr,$id)
    {
        return sonsTree($arr,$id,0);
    }

    /**
     * 推荐使用
     * 获取角色已绑定的权限 - 视图表查询
     * @param $roleId
     * @param string $field
     * @return array|\PDOStatement|string|\think\Collection
     */
    /*public function getRoleBindAuthView($roleId,$field='')
    {
        try{
            $condition=[
                'role_id' => $roleId
            ];
            $data=Db::name($this->viewTable)
                ->field($field)
                ->where($condition)
                ->select();
            return $data;
        }catch(\Exception $e){
            $this->error=9999;
            return false;
        }
    }*/

    /**
     * 获取角色已绑定的权限 - 新建视图查询
     * @param $roleId
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    /*public function getRoleBindAuth($roleId)
    {
        $APTable=(new AuthOperationModel())->table;
        $condition=[
            'role_id' => $roleId
        ];
        $bField=[
            'id' => 'b_id',
            'auth_id' => 'auth_id',
            'role_id' => 'role_id',
        ];
        $aField=[
            'pid' => 'pid',
            'name' => 'name',
            'url' => 'url',
            'sort' => 'sort',
            'open' => 'open',
            'is_parent' => 'is_parent',
        ];
        $msg=Db::view($this->table . ' b',$bField)
            ->view($APTable . ' a',$aField ,'a.id = b.auth_id')
            ->where($condition)
            ->select();
        return $msg;
    }*/

    /**
     * 更新角色权限
     * @param $roleId
     * @param $authArr
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function updateAuth($roleId,$authArr)
    {
        if (!is_array($authArr)){
            $this->error = 3201;//不是操作权限id数组
            return false;
        }
        $addData=[];
        $deleteCondition=[
            'role_id' => $roleId,
        ];
        //查询角色已绑定的所有权限
        $aData=Db::name($this->table)
            ->where(['role_id' => $roleId])
            ->field('auth_id')
            ->select();
        if(false===$aData){
            $this->error = 3202;//没有“操作权限数据”，请先添加权限数据
            return false;
        }
        $authDataArr=[];
        if ($aData){
            foreach ($aData as $k => $v){
                $authDataArr[]=$v['auth_id'];
            }
        }
        if ($authArr){
            $addData=array_diff($authArr,$authDataArr);//新数据中有，数据库中没有的为新增
            $deleteCondition['auth_id']=array_diff($authDataArr,$authArr);//数据库中有，新数据中没有的为删除

            //如果新数据与数据库中数据相同，则不更新权限
            if (!$addData && !$deleteCondition['auth_id']){
                return true;
            }
            if (!$deleteCondition['auth_id']){
                $deleteCondition=[];//如果没有要删除的数据，则清空删除条件
            }
        }

        Db::startTrans();
        try{
            //如果删除条件不为空
            if ($deleteCondition){
                $msg=Db::name($this->table)
                    ->where($deleteCondition)
                    ->delete();
                if (!$msg){
                    Db::rollback();
                    $this->error=3203;//删除权限失败
                    return false;
                }
            }
            //新增权限
            if ($addData){
                $data=[];
                foreach ($addData as $k => $v){
                    $data[]=[
                        'auth_id'=>$v,
                        'role_id'=>$roleId
                    ];
                }
                $msg2=Db::name($this->table)
                    ->insertAll($data);
                if (!$msg2){
                    Db::rollback();
                    $this->error=3204;//角色添加权限失败
                    return false;
                }
            }
            Db::commit();
            return true;
        }catch(\Exception $e){
            Db::rollback();
            $this->error=$e->getMessage();
            return false;
        }
    }
}