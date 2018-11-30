<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/10/9
 * Time: 12:36
 */

return [
    //验证码
    'admin/captcha' => 'admin/index/captcha',
    //管理员登陆页面
    'admin/login$' => 'admin/index/login',
    //管理员中心
    'admin/center$' => 'admin/center/index',


    //----------------------------------------------------------
    //-------------------------   管理员登陆   -----------------
    //----------------------------------------------------------

    'admin/api/login/username' => 'admin/api.Login/username',

    //----------------------------------------------------------
    //-------------------------   管理员   ---------------------
    //----------------------------------------------------------
    'admin/list'    =>  'admin/admin/admin_list',
    'admin/add'    =>  'admin/admin/admin_add',
    'admin/edit'    =>  'admin/admin/admin_edit',

    'admin/api/getadminlist'    =>  'admin/api.Admin/getAdminList',
    'admin/api/addadmin'    =>  'admin/api.Admin/addAdmin',
    'admin/api/deleteadmin'    =>  'admin/api.Admin/deleteAdmin',
    'admin/api/setadminstatus'    =>  'admin/api.Admin/setAdminStatus',
    'admin/api/saveadmin'    =>  'admin/api.Admin/saveAdmin',
    'admin/api/getadmininfo'    =>  'admin/api.Admin/getAdminInfo',
    'admin/api/getadmingroup'    =>  'admin/api.Admin/getAdminBindGroup',
    'admin/api/getadminrole'    =>  'admin/api.Admin/getAdminBindRole',

    //----------------------------------------------------------
    //-------------------------   管理员   ---------------------
    //----------------------------------------------------------

];

