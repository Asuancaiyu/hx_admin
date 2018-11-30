<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/11/8
 * Time: 13:08
 */

namespace app\admin\controller\api;


use app\admin\controller\Base;
use app\common\logic\admin\auth\AuthOperation;
use think\App;

class OperationAuth extends Base
{
    private $LA=null;
    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->LA=new AuthOperation();
    }

    /**
     * 获取权限
     * @throws \think\exception\DbException
     */
    public function getAuthList()
    {
        $request=input('');
        $id=$request['id'] ?? 0;
        $msg=$this->LA->getOperationAuth($id);
        unset($request);
        if (!$msg){
            ajaxReturn(msg(1,'操作失败！'));
        }
        ajaxReturn(dataMsg(0,'ok',$msg['count'],$msg['data']));
    }


    /**
     * 添加权限
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function addAuth()
    {
        $request=input('');
        $name=$request['name'] ?? '';
        $url=$request['url'] ?? '';
        $pid=$request['pid'] ?? 0;
        $DD=$request['deleteDisable'] ?? 0;
        $sort=$request['sort'] ?? 0;
        $isParent=$request['parent'] ?? 0;
        $open=$request['open'] ?? 0;

        $msg=$this->LA->addOperationAuth($name,$url,$pid,$isParent,$open,$DD,$sort);
        if (!$msg){
            ajaxReturn(msg(1,$this->LA->getError()));
        }
        ajaxReturn(msg(0,'ok',['id'=>$msg]));
    }

    /**
     * 删除权限
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function deleteAuth()
    {
        $request=input('');
        $id=$request['id'] ?? 0;
        if (!$id){
            ajaxReturn(msg(1,'错误的访问！'));
        }
        $msg=$this->LA->deleteOperationAuth($id);
        if (!$msg){
            ajaxReturn(msg(1,$this->LA->getError()));
        }
        ajaxReturn(msg(0,'ok',['id'=>$msg]));
    }

    /**
     */
    public function saveAuth()
    {
        $request=input('');
        $id=$request['id'] ?? 0;
        $name=$request['name'] ?? '';
        $url=$request['url'] ?? '';
        $pid=$request['pid'] ?: 0;

        if (!$id){
            ajaxReturn(msg(1,'错误的访问！'));
        }
        $msg=$this->LA->saveOperationAuth($id,$name,$url,$pid);
        if (!$msg){
            ajaxReturn(msg(1,$this->LA->getError()));
        }
        ajaxReturn(msg(0,'ok'));
    }
}