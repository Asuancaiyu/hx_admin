<?php
/**
 * 用户管理 面页集合
 * User: KF
 * Date: 2018/11/23
 * Time: 13:28
 */

namespace app\admin\controller;


class Member extends Base
{
    /**
     * 用户列表
     * @return mixed
     */
    public function member_list()
    {
        return $this->fetch();
    }

    /**
     * 已删除用户列表
     * @return mixed
     */
    public function member_delete_list()
    {
        return $this->fetch();
    }

    /**
     * 编辑用户信息
     * @return mixed
     */
    public function member_edit()
    {
        return $this->fetch();
    }

    /**
     * 添加用户
     * @return mixed
     */
    public function member_add()
    {
        return $this->fetch();
    }

    /**
     * 用户组列表
     * @return mixed
     */
    public function group_list()
    {
        return $this->fetch();
    }
    /**
     * 标签列表
     * @return mixed
     */
    public function tag_list()
    {
        return $this->fetch();
    }
}