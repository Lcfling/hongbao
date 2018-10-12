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
        $hongbao_info['type']=1;
        $hongbao_info['remark']='可以领取';
        $this->ajaxReturn($hongbao_info,'可以领取',1);

    }
    //开包
    public function openkickback(){

    }

    /**
     *
     * 红包详情
     *
     */
    public function getrecivelist(){

    }
}