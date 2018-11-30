<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/11/2
 * Time: 13:34
 */

namespace app\common\model\admin;


use app\common\model\admin\auth\AuthOperationModel;
use app\common\model\admin\auth\RoleBindAuthModel;
use app\common\model\admin\auth\RoleBindOperationModel;
use app\common\model\DataModel;
use think\Db;
use think\Model;

class RoleModel extends DataModel
{
    /**
     * 表名
     * @var string
     */
    public $table='admin_role';

    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /*public function getRoleBindOperationAuth()
    {
        $ABPTable=(new RoleBindOperationModel())->table;
        $APTable=(new AuthOperationModel())->table;
        $msg=Db::name($this->table)
            ->alias('r')
            ->field('*')
            ->join($ABPTable.' b','r.id = b.role_id')
            ->join($APTable . ' a','b.auth_id = a.id')
            ->select();
        dump($msg);
    }*/


    /**
     * 添加角色
     * @param $name
     * @param $desc
     * @param $authArr
     * @return bool
     */
    public function addRole($name,$desc,$authArr=[])
    {
        $data=[
            'role_name' => $name,
            'description' => $desc,
        ];
        // 启动事务
        Db::startTrans();
        try {
            $rId=Db::name($this->table)->insertGetId($data);
            if (!$rId){
                Db::rollback();
                $this->error=3020;//添加角色时失败
                return false;
            }

            //添加操作权限
            if (!empty($authArr['operation'])){
                $msg = (new RoleBindOperationModel())->addRoleAuth($rId,$authArr['operation']);
                if (!$msg){
                    Db::rollback();
                    $this->error=3021;//绑定权限时失败
                    return false;
                }
            }
            //添加菜单权限
            if (!empty($authArr['menu'])){

            }
            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }
    }


    /**
     * 保存角色信息
     * @param $id
     * @param $name
     * @param $desc
     * @param array $authArr
     * @return bool
     */
    public function saveRoleInfo($id,$name,$desc,$authArr=[])
    {
        $data=[
            'role_name' => $name,
            'description' => $desc,
        ];
        // 启动事务
        Db::startTrans();
        try {
            $c=$data;
            $c['id']=$id;
            if (!$this->dataCount($c)){
                $msg=$this->saveData(['id'=>$id],$data);
                if (!$msg){
                    Db::rollback();
                    $this->error=3010;//更新角色信息失败！
                    return false;
                }
            }
            //更新操作权限
            if (!empty($authArr['operation'])){

                $RBOM=new RoleBindOperationModel();
                $msg = $RBOM->updateAuth($id,$authArr['operation']);
                if (!$msg){

                    Db::rollback();
                    $this->error=3011;//更新角色权限时失败！
                    return false;
                }
            }
            //更新菜单权限
            if (!empty($authArr['menu'])){

            }
            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {

            // 回滚事务
            Db::rollback();
            $this->error=9999;//异常
            return false;
        }
    }

    /**
     * 删除角色
     * @param $rId
     * @return bool
     */
    public function deleteRole($rId)
    {
        if (!$rId){
            $this->error=1000;
            return false;
        }
        // 启动事务
        Db::startTrans();
        try{
            if(false===$msg=(new RoleBindOperationModel())->deleteData(['role_id'=>$rId])){
                $this->rollback();
                $this->error=3012;//删除角色操作权限失败
                return false;
            }
            $msg2=$this->deleteData(['id'=>$rId]);
            if (!$msg2){
                $this->rollback();
                $this->error=3013;//删除角色信息失败
                return false;
            }
            // 提交事务
            Db::commit();
            return true;
        }catch (\Exception $e){
            // 回滚事务
            Db::rollback();
            $this->error=9999;//异常
            return false;
        }
    }



}