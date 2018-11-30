<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/11/8
 * Time: 11:48
 */

namespace app\common\logic\admin\auth;



use app\common\model\admin\auth\AuthOperationModel;
use app\common\model\admin\auth\RoleBindOperationModel;
use think\Model;

class AuthOperation extends Model
{
    protected $MO=null;

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->MO=new AuthOperationModel();
    }

    /**
     * 获取多条权限信息
     * @param int $id id
     * @return array
     * @throws \think\exception\DbException
     */
    public function getOperationAuth($id)
    {
        $condition=[];
        if($id){
            $condition[]=['path','like','%,'.$id.',%'];
        }
        $msg=$this->MO->dataList($condition);
        return $msg;
    }

    /**
     * 获取一条权限信息
     * @param $id
     * @return \app\common\model\PDOStatement|array|bool|null|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOperationAuthFind($id)
    {
        $id=(int)$id;
        if (!$id){
            return false;
        }
        $data=$this->MO->dataFind(['id'=>$id]);
        return $data;
    }

    /**
     * 添加一条权限
     * @param string $name 名称
     * @param string $url 链接
     * @param int $pid 父节点id
     * @param int $isParent 是否为父节点
     * @param int $open 是否打开
     * @param int $deleteDisable 是否可以删除
     * @param int $sort 排序
     * @return bool|int|string
     */
    public function addOperationAuth($name,$url,$pid=0,$isParent,$open,$deleteDisable=0,$sort=6000)
    {
        if (!$name){
            $this->error='权限名称不能为空';
            return false;
        }
        if (!$url){
            $this->error='URL不能为空';
            return false;
        }

        $data=[
            'pid'=>$pid,
            'name'=>$name,
            'url'=>$url,
            'delete_disable'=>(int)$deleteDisable,
            'sort'=>$sort,
            'is_parent' => (int)$isParent,
            'open' => (int)$open,
            'create_time' => time(),
        ];
        if ($pid!=0){
            $pData=$this->MO->dataFind(['id'=>$pid]);
            if (!$pData){
                $this->error='父节点不存在';
                return false;
            }
        }
        $id=$this->MO->addData($data);
        if (!$id){
            $this->error='添加节点失败';
            return false;
        }
        //添加完节点后需要给节点添加路径
        //添加路径字段以便于后期 “查询” 和 “删除” 节省更多时间
        $path=isset($pData) ? $pData['path'] : ',';
        $data=[
            'path'=> $path . $id . ',',
        ];
        $this->MO->saveData(['id'=>$id],$data);
        return $id;
    }

    /**
     * 删除权限
     * @param int $id 权限id
     * @return bool|int
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function deleteOperationAuth($id)
    {
        $id=(int)$id;
        if (!$id){
            return false;
        }
        $oInfo=$this->MO->dataFind(['id'=>$id]);
        if (!$oInfo){
            $this->error='该权限不存在';
            return false;
        }
        if (isset($oInfo['delete_disable']) && $oInfo['delete_disable']>0){
            $this->error='禁止删除';
            return false;
        }
        $this->startTrans();
        try{
            $condition=[
                ['path','like','%,'.$id.',%'],
            ];
            $roleData=$this->MO->dataList($condition,'id');
            $roleData=$roleData['data'] ?? null;
            if($roleData){
                $roleArr=[];
                foreach ($roleData as $v){
                    $roleArr[]=$v['id'];
                }
                if(false === (new RoleBindOperationModel())->deleteData(['auth_id'=>$roleArr])){
                    $this->rollback();
                    $this->error="在删除已绑定的权限时失败";
                    return false;
                }
                $this->MO->deleteData($condition);
            }
            $this->commit();
        }catch (\Exception $e){
            dump($e->getMessage());
            $this->error=9999;
            $this->rollback();
            return false;
        }
        //$msg=$this->MO->deleteData(['id'=>$id]);
        return true;
    }

    /**
     * 保存权限信息
     * @param $id
     * @param $name
     * @param $url
     * @param $pid
     * @return bool
     */
    public function saveOperationAuth($id,$name,$url,$pid)
    {
        $pData=$this->MO->dataFind(['id'=>$id]);
        if (!$pData){
            $this->error='节点不存在';
            return false;
        }
        if (!$name){
            $this->error='缺少名称';
            return false;
        }
        if (!$url){
            $this->error='缺少url';
            return false;
        }

        if ($pid!==0 && !$pid){
            $this->error='缺少父节点';
            return false;
        }

        $data=[
            'name' => $name,
            'url' => $url,
            'pid' => $pid,

        ];
        $msg=$this->MO->saveData(['id'=>$id],$data);
        if (!$msg){
            $this->error='修改失败！';
            return false;
        }
        return true;
    }
}