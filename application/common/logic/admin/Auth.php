<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/11/21
 * Time: 11:27
 */

namespace app\common\logic\admin;


use app\common\model\admin\RoleModel;
use think\Model;

class Auth extends Model
{
    public function getAdminAuthAll($adminId)
    {
        if (!$adminId){
            $this->error=6000;//缺少管理员id
            return false;
        }


    }

    /**
     * 获取管理员所有角色权限
     */
    public function getAdminRoleAllAuth($adminId)
    {
        $RM=new RoleModel();
        $RM->dataList(['admin_id'=>$adminId],'');
    }
}