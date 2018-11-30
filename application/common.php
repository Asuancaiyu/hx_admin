<?php
// 应用公共文件

/**
 * @param int $code 返回代码
 * @param string $msg 返回消息
 * @param null $data 返回数据
 * @param null $count 返回数据条数
 * @param null $other 返回其他数据
 */
function codeReturn($code=0,$msg='',$data=null,$count=null,$other=null)
{
    $arr=[
        'code'  =>  $code,
        'msg'   =>  $msg,
    ];
    if ($data !== null){
        $arr['data']=$data;
    }
    if ($count !== null){
        $arr['count']=$count;
    }
    if ($other !== null){
        $arr['other']=$other;
    }
    ajaxReturn($arr);
}

/**
 * ajax请求返回json数据
 * @param array $data
 */
function ajaxReturn($data=[])
{
    die(json_encode($data));
}

/**
 * 消息数组
 * @param int $code
 * @param string $info
 * @param array $data
 * @return array
 */
function msg($code=0,$info='',$data=null)
{
    $arr=[
        'code'  =>  $code,
        'msg'   =>  $info,
    ];
    if ($data!==null){
        $arr['data'] = $data;
    }
    return $arr;
}

/**
 * 返回数据类型的消息数组
 * @param int $code
 * @param string $info
 * @param int $count
 * @param array $data
 * @param null $other
 * @return array
 */
function dataMsg($code=0,$info='',$count=0,$data=[],$other=null)
{
    $arr=[
        'code'  =>  $code,
        'msg'   =>  $info,
        'count' =>  $count,
        'data'  =>  $data,
    ];
    if ($other != null){
        $arr['other']=$other;
    }
    return $arr;
}

/**
 * 遍历树（根据父找儿孙）
 * @param $arr 数组
 * @param $id id
 * @param $lev 层级
 * @return array
 */
function sonsTree($arr, $id,$lev){
    $temp = [];
    foreach ($arr as $k=>$v){
        $item = $v;
        if($item['pid'] == $id){
            $item['lev']=$lev;
            unset($arr[$k]);

            $item['child']=sonsTree($arr,$item['id'],$lev+1);
            $temp[]=$item;
        }
    }
    return $temp;
};


/**
 * 密码加密
 * @param $pwd
 * @return string
 */
function pwdEncryption($pwd)
{
    return md5('hx'.$pwd.'hx');
}

/**
 * 获取客户端IP地址
 * @access public
 * @param  integer   $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param  boolean   $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function ip($type = 0, $adv = true)
{
    $type      = $type ? 1 : 0;
    static $ip = null;

    if (null !== $ip) {
        return $ip[$type];
    }

    $httpAgentIp = $this->config['http_agent_ip'];

    if ($httpAgentIp && $this->server($httpAgentIp)) {
        $ip = $this->server($httpAgentIp);
    } elseif ($adv) {
        if ($this->server('HTTP_X_FORWARDED_FOR')) {
            $arr = explode(',', $this->server('HTTP_X_FORWARDED_FOR'));
            $pos = array_search('unknown', $arr);
            if (false !== $pos) {
                unset($arr[$pos]);
            }
            $ip = trim(current($arr));
        } elseif ($this->server('HTTP_CLIENT_IP')) {
            $ip = $this->server('HTTP_CLIENT_IP');
        } elseif ($this->server('REMOTE_ADDR')) {
            $ip = $this->server('REMOTE_ADDR');
        }
    } elseif ($this->server('REMOTE_ADDR')) {
        $ip = $this->server('REMOTE_ADDR');
    }

    // IP地址类型
    $ip_mode = (strpos($ip, ':') === false) ? 'ipv4' : 'ipv6';

    // IP地址合法验证
    if (filter_var($ip, FILTER_VALIDATE_IP) !== $ip) {
        $ip = ('ipv4' === $ip_mode) ? '0.0.0.0' : '::';
    }

    // 如果是ipv4地址，则直接使用ip2long返回int类型ip；如果是ipv6地址，暂时不支持，直接返回0
    $long_ip = ('ipv4' === $ip_mode) ? sprintf("%u", ip2long($ip)) : 0;

    $ip = [$ip, $long_ip];

    return $ip[$type];
}


//---------------------------------------------------------------------------------------
//------------------------------        验证函数           ------------------------------
//---------------------------------------------------------------------------------------
/**
 * 验证
 * @param $type
 * @param $str
 * @return bool|int|null|string
 */
function verify($type,$str)
{
    $err=null;
    switch ($type){
        case 'username'://用户名
            $e='/^[a-zA-Z0-9_-]{4,30}$/';
            if (!preg_match($e,$str)){
                $err='用户名称格式不正确';
            }
            break;
        case 'password'://密码验证
           /* $e='/^[a-zA-Z0-9_-]{6,30}$/';
            if (!preg_match($e,$type)){
                $err='密码格式不正确';
            }*/
            break;
        case 'phone'://手机号验证
            $e='/^[0-9]{11}$/';
            if (!preg_match($e,$str)){
                $err='手机格式不正确';
            }
            break;
        case 'email'://邮箱验证
            $e='/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/';
            if (!preg_match($e,$str)){
                $err='邮箱格式不正确';
            }
            break;
        default://不存在的验证类型
            return false;
    }
    if ($err){
        return $err;
    }
    return true;
}