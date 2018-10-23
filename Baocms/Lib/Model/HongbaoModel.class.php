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
//不大于0 等于领取过
        return true;
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
        //单个用户是否领取过红包

        return true;
    }

    /**从队列中取出一个红包id
     *
     * @param $hongbao_id
     *
     * @return $kickbackid 0 或  大于0
     */
    public function getkickid($hongbao_id){

        return $kickbackid;
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
                Cac()->set('kickback_id_'.$v['id'],$v);
            }
        }
        $len=Cac()->lLen('kickback_queue_'.$hongbao_info['id']);

        //$arr=Cac()->lrange('kickback_queue_'.$hongbao_info['id'],0,$len);
        //$len=Cac()->lLen('kickback_queue_'.$hongbao_info['id']);

        //
    }
    private function insertkicklist($hongbao_id){

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