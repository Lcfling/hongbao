<?php
class HongbaoModel extends CommonModel
{
    protected $pk = 'id';
    protected $tableName = 'hongbao';


    public function getInfoById($id){

    }

    /**红包是否领取完毕
     * @param $id 红包id
     * @return bool 领取完毕 true 有待领取 false
     */
    public function isfinish($id){
        $value=Cac()->lget('hongbao_queue_'.$id,0);
        if($value>0){
            return false;
        }else{
            return true;
        }
    }

    /**是否领取过此红包
     *
     * @param $hongbao_id 红包id
     *
     * @param $uid        用户id
     *
     * @return bool       领取过 true  未领取 false
     */
    public function is_recived($hongbao_id,$uid){
        Cac()->rPush('recive_queue_'.$hongbao_id.'_'.$uid,1);
        if(Cac()->lLen('recive_queue_'.$hongbao_id.'_'.$uid)==1){
            $list=Cac()->lRange('kickback_user_'.$hongbao_id, 0, -1);

            if(!empty($list)){
                foreach ($list as $v){
                    if($v==$uid){
                        return true;
                    }
                }
                return false;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }

    /**从队列中取出一个红包id   出队
     *
     * @param $hongbao_id
     *
     * @return $kickbackid 0 或  大于0
     *
     * 缓存队列键 kickback_queue_187
     */
    public function getOnekickid($hongbao_id){
        $kickbackid=Cac()->lPop('kickback_queue_'.$hongbao_id);
        return $kickbackid;
    }




    /**领取完毕后 入队已经领取
     * @param $hongbao_id
     * @param $uid
     *
     * 缓存键 kickback_userin_198   198用户id
     */
    public function UserQueue($hongbao_id,$uid){
        Cac()->rPush('kickback_user_'.$hongbao_id,$uid);
    }

    /**设置小红包为已经领取
     * @param $kickbackid
     * @param $uid  领取人id
     *
     * 先改数据库 再更新缓存
     */
    public function setkickbackOver($kickbackid,$uid){

    }

    /**从已经领取队列中 判断自己是否是最后一位
     *
     * @param $hongbao_id
     *
     * @param $uid
     *
     * @return bool
     */
    public function is_self_last($hongbao_id,$uid){
        $value=Cac()->lGet('hongbao_queue_'.$hongbao_id,5);
        if($value==$uid){
            return true;
        }else{
            return false;
        }
    }

    /**获取中雷红包个数 大于3全部设置为3
     * @param $hongbao_id
     * @return $count 中雷个数
     */
    public function getBomNums($hongbao_id){

        return $count;
    }

    /**设置红包状态为领取完毕
     * @param $hongbao_id
     */

    public function sethongbaoOver($hongbao_id){

    }

    /**获取小红包的信息
     * @param $kickback_id
     * @return mixed
     */
    public function getkickInfo($kickback_id){
        $kickInfo=unserialize(Cac()->get('kickback_id_'.$kickback_id));
        if(empty($kickInfo)){
            $kickInfo=D('kickback')->where(array('id'=>$kickback_id))->find();
            if(!empty($kickInfo)){
                Cac()->set('kickback_id_'.$kickback_id,serialize($kickInfo));
            }else{
                return false;
            }
        }else{
            return $kickInfo;
        }

    }

    public function creathongbao($money,$bom_num,$num,$roomid,$uid){
        $token=md5(genRandomString(6).time().$uid);
        $data=array();
        $data['token']=$token;
        $data['roomid']=$roomid;
        $data['user_id']=$uid;
        $data['money']=$money;
        $data['num']=$num;
        $data['bom_num']=$bom_num;
        $data['is_over']=0;
        $data['overtime']=0;
        $data['creatime']=time();
        $this->add($data);//大红包添加完毕
        //取出红包加入缓存
        $hongbao_info=$this->where(array('token'=>$token))->find();
        if(empty($hongbao_info)){
            return false;
        }
        Cac()->set('hongbao_info_'.$hongbao_info['id'],serialize($hongbao_info));
        //根据金额 生成7个红包
        $kickarr=$this->getkicklist($money,$num);

        //小红包入库
        foreach($kickarr as $k=>$value){
            if($k==0){
                $data['user_id']=0;
                $data["hb_id"]=$hongbao_info['id'];
                $data["is_robot"]=1;
                $data["is_receive"]=1;
                $data["money"]=$value;
                $data['recivetime']=time();
                $data["creatime"]=time();
                D('kickback')->add($data);
            }else{
                $data['user_id']=0;
                $data["hb_id"]=$hongbao_info['id'];
                $data["is_robot"]=0;
                $data["is_receive"]=0;
                $data["money"]=$value;
                $data['recivetime']=0;
                $data["creatime"]=time();
                D('kickback')->add($data);
            }
        }
        //获取小红包
        $new_kicklist=D('kickback')->where(array('hb_id'=>$hongbao_info['id']))->select();
        foreach ($new_kicklist as $k=>$v){
            if($v['is_receive']==0){
                Cac()->rPush('kickback_queue_'.$hongbao_info['id'],$v['id']);
                Cac()->set('kickback_id_'.$v['id'],serialize($v));
            }
        }
        $len=Cac()->lLen('kickback_queue_'.$hongbao_info['id']);
        if($len==6){
            return true;

        }else{
            return false;
        }
        //$arr=Cac()->lrange('kickback_queue_'.$hongbao_info['id'],0,$len);
        //$len=Cac()->lLen('kickback_queue_'.$hongbao_info['id']);

        //
    }

    private function getkicklist($money,$num){
        $totle=$money;
        if($num>1){
            $nums_arr=array();

            while (count($nums_arr)<$num-1){
                $point=rand(1,$totle-1);
                while(in_array($point,$nums_arr)){
                    $point=rand(1,$totle-1);
                }
                $nums_arr[]=$point;
            }
            arsort($nums_arr);
        }else{
            $nums_arr[]=0;
        }
        $maxkey=$totle;
        $money_arr=array();
        foreach($nums_arr as $k=>$value){
            $money_arr[]=$maxkey-$value;
            $maxkey=$value;
        }
        if($num>1){
            $money_arr[]=$maxkey;
        }
        return $money_arr;
    }


}