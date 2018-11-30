<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/11/29
 * Time: 13:27
 */

namespace app\common\logic\member;


use app\common\logic\BaseLogic;
use app\common\model\member\MemberBindTagModel;
use app\common\model\member\MemberTagModel;

class MemberTagLogic extends BaseLogic
{
    public $mtm = null;
    public $mbtm = null;

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->mtm = new MemberTagModel();
        $this->mbtm = new MemberBindTagModel();
    }
    
    /**
     * 获取标签列表
     * @param array $request
     * @param int $page
     * @param int $limit
     * @return array|bool
     */
    public function getMemberTagList($request=[],$page=1,$limit=20)
    {
        foreach ($request as $k=>$v){
            $request[$k] = trim($v);
        }
        $condition=[];
        if (!empty($request['name'])){
            $condition[]=['tag_name','like','%'.$request['name'].'%'];
        }
        try{
            $mgm=new MemberTagModel();
            $msg=$mgm->dataList($condition,'id,tag_name,description', $limit);
            if (false===$msg){
                $this->setError(5501,'获取标签失败');
                return false;
            }
        }catch (\Exception $e){
            $this->setError(9999,$e->getMessage());
            return false;
        }
        return $msg;
    }


    /**
     * 获取标签信息
     * @param $tag
     * @param $type
     * @return array|bool|null|\PDOStatement|string|\think\Model
     */
    public function getMemberTagInfo($tag,$type)
    {
        if ($type=='id'){
            $condition=['id'=>$tag];
        }else{
            $condition=['tag_name'=>$tag];
        }
        try{

            $mgm=new MemberTagModel();
            $msg=$mgm->dataFind($condition);
            if (false===$msg){
                $this->setError(5502,'获取标签信息失败');
                return false;
            }
        }catch (\Exception $e){
            $this->setError(9999,$e->getMessage());
            return false;
        }
        return $msg;
    }

    /**
     * 添加标签
     * @param $name 标签名
     * @param $description 标签说明
     * @return bool
     * @throws \think\exception\PDOException
     */
    public function addMemberTag($name,$description)
    {
        $data=[
            'tag_name' => $name,
            'description' => $description,
            'create_time' => time()
        ];
        $mgm=new MemberTagModel();
        if($mgm->dataCount(['tag_name'=>$name])){
            $this->setError(5510,'该标签已存在');
            return false;
        }
        $this->startTrans();
        try{
            $msg=$mgm->addData($data);
            if (false===$msg){
                $this->rollback();
                $this->setError(5503,'添加标签失败');
                return false;
            }
            $this->commit();
        }catch (\Exception $e){
            $this->rollback();
            $this->setError(9999,$e->getMessage());
            return false;
        }
        return $msg;
    }

    /**
     * 保存标签信息
     * @param $tagId 标签信息
     * @param $name 标签名
     * @param $description 标签说明
     * @return bool
     * @throws \think\exception\PDOException
     */
    public function saveMemberTagInfo($tagId,$name,$description)
    {
        $data=[
            'tag_name' => $name,
            'description' => $description,
            'modify_time' => time(),
        ];
        $mgm=new MemberTagModel();
        if($mgm->dataCount([['tag_name','=',$name],['id','<>',$tagId]])){
            $this->setError(5510,'该标签已存在');
            return false;
        }

        $this->startTrans();
        $condition=['id' => $tagId];
        try{
            if(!$mgm->dataCount($condition)){
                $this->rollback();
                $this->setError(5500,'标签不存在');
                return false;
            }
            $msg=$mgm->saveData($condition,$data);
            if (false===$msg){
                $this->rollback();
                $this->setError(5503,'添加标签失败');
                return false;
            }
            $this->commit();
        }catch (\Exception $e){
            $this->rollback();
            $this->setError(9999,$e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * 删除标签
     * @param $tagId 标签id
     * @return bool
     * @throws \think\exception\PDOException
     */
    public function deleteMemberTag($tagId)
    {
        $this->startTrans();
        $mgm=new MemberTagModel();
        $condition=['id' => $tagId];
        try{
            if(!$mgm->dataCount($condition)){
                $this->rollback();
                $this->setError(5500,'标签不存在');
                return false;
            }
            $msg=$mgm->deleteData($condition);
            if (false===$msg){
                $this->rollback();
                $this->setError(5503,'添加标签失败');
                return false;
            }
            //用户解绑标签
            $msg2=$this->mbtm->deleteData(['tag_id'=>$tagId]);
            if (false===$msg2){
                $this->rollback();
                $this->setError(5505,'用户解绑标签失败');
                return false;
            }
            $this->commit();
        }catch (\Exception $e){
            $this->rollback();
            $this->setError(9999,$e->getMessage());
            return false;
        }
        return true;
    }


    public function setMemberBindTag($memberId,$tagId)
    {

    }

    /**
     * 获取用户绑定的标签
     * @param $memberId
     * @param $merge
     * @return bool
     */
    public function getMemberBindTagList($memberId,$merge=0)
    {
        if (!in_array($merge,[0,1])){
            $this->setError(5601,'不存在的合并参数');
            return false;
        }
        $mtm=new MemberTagModel();
        $msg=$mtm->getMemberBindTag($memberId,$merge);
        if (false===$msg){
            $this->setError(5602,'获取用户标签失败');
            return false;
        }
        return $msg;
    }

    /**
     * 用户解绑标签
     * @param $memberId
     * @param null $tagId
     * @return bool
     */
    public function setMemberUnbindTag($memberId,$tagId=null)
    {
        $mbtm = new MemberBindTagModel();
        $condition=['member_id'=>$memberId];
        if ($tagId){
            $condition['tag_id'] = $tagId;
        }
        if(false===$mbtm->deleteData($condition)){
            $this->setError(5610,'用户解绑组失败');
            return false;
        }
        return true;
    }
}