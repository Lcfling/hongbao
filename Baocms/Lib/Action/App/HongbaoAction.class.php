<?php
require_once LIB_PATH.'/GatewayClient/Gateway.php';
use GatewayClient\Gateway;
class HongbaoAction extends CommonAction
{
    public function index()
    {
        $gametype=$_POST['gametype'];
        $roomlist=D('Room')->getroomlist($gametype);
        if(!empty($roomlist)){
            $this->ajaxReturn($roomlist);
        }else{
            $this->ajaxReturn('','未知错误！',0);
        }
    }
    //点击检测
    public function clickkickback(){
        $hongbao_id=(int)$_POST['hongbao_id'];
        $hongbaoModel=D('hongbao');
        $hongbao_info=$hongbaoModel->getInfoById($hongbao_id);
        if($hongbao_info['over']<time()){
            $info['type']=2;
            $info['remark']='红包过期';
            $this->ajaxReturn($info,'红包过期!',0);
        }
        if($hongbaoModel->isfinish()){
            $info['type']=3;
            $info['remark']='红包过期';
            $this->ajaxReturn($info,'红包已经领取完毕!',0);
        }
        //此处加强判断 已经领取  不允许重复领取
        if($hongbaoModel->is_recived($hongbao_id,$this->uid)){
            $info['type']=4;
            $info['remark']='已经领取过次红包!';
            $this->ajaxReturn('','已经领取过次红包!',0);
        }
        //余额判断   余额小于红包总金额的1.66倍 不允许强
        $userMoney=D('Users')->getUserMoney();
        if($userMoney<$hongbao_info['money']*1.66){
            $info['type']=5;
            $info['remark']='余额不足!';
            $this->ajaxReturn('','余额不足!',0);
        }
        $hongbao_info['type']=1;
        $hongbao_info['remark']='可以领取';
        $this->ajaxReturn($hongbao_info,'可以领取',1);

    }
    //开包
    public function openkickback(){
        $hongbao_id=(int)$_POST['hongbao_id'];//红包id
        $hongbaoModel=D('hongbao');
        $hongbao_info=$hongbaoModel->getInfoById($hongbao_id);

        //此处加强判断 已经领取  不允许重复领取
        if($hongbaoModel->is_recived($hongbao_id,$this->uid)){
            $this->ajaxReturn('','已经领取过次红包!',0);
        }
        $kickback_id=$hongbaoModel->getkickid($hongbao_id);
        if($kickback_id>0){

        }else{
            $this->ajaxReturn('','手慢了，领取完了!',0);
        }
    }

    /**
     *
     * 红包详情
     *
     */
    public function getrecivelist(){

    }

    /**发红包
     *
     */
    public function send(){
        $roomid=(int)$_POST['roomid'];
        $roomData=D('Room')->getRoomData($roomid);
        if(empty($roomData)){
            $this->ajaxReturn('','房间不存在!',0);
        }
        $this->ajaxReturn($roomData,'请求成功',1);
    }
    //执行发红包
    public function dosend(){
        $money=(int)($_POST['money']*100);
        $bom_num=(int)$_POST['bom_num'];
        $roomid=(int)$_POST['roomid'];
        $roomData=D('Room')->getRoomData($roomid);
        if(empty($roomData)){
            $this->ajaxReturn('','房间不存在!',0);
        }
        //金额判断
        if($money>$roomData['max']||$money<$roomData['min']){
            $this->ajaxReturn('','请选择正确的金额 '.$roomData['min'].'-'.$roomData['max'],0);
        }
        //雷点判断
        if(!($bom_num<10||$bom_num>=0)){
            $this->ajaxReturn('','请选择正确的雷数字 0-9',0);
        }
        //余额判断判断
        $userMoney=D('Users')->getUserMoney();
        if($userMoney<$money){
            $this->ajaxReturn('','余额不足，请充值!',0);
        }
        //生成红包
        D('Hongbao')->createhongbao();
    }
}