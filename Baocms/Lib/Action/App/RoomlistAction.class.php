<?php
require_once LIB_PATH.'/GatewayClient/Gateway.php';
use GatewayClient\Gateway;
class RoomlistAction extends CommonAction
{
    public function index()
    {
        $gametype=$_POST['gametype'];
        $roomlist=D('Room')->getroomlist($gametype);
        if(!empty($roomlist)){
            $this->ajaxReturn($roomlist);
        }else{
            $this->ajaxReturn('','未知错误！',1);
        }
    }
}