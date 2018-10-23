<?php


class GameAction extends CommonAction
{

    //强庄
    public function rob(){
        $roomid=$_POST['roomid'];
        $UserModel=D('Users');
        $usermoney=$UserModel->getusermoney($this->uid);
        if($usermoney<3000){
            $this->ajaxReturn('','余额不足3000，余额：'.$usermoney,'0');
        }

        $GameModel=D('Game');
        if(!$GameModel->is_free()){
            $this->ajaxReturn('','已经存在','0');
        }
        //生成一局游戏
        $frozenMoney=3000;
        $UserModel->frozen($this->uid,$frozenMoney);
        $res=$GameModel->creategame($this->uid);
        //通知来时下注

    }
    public function betting(){
        $roomid=(int)$_POST['roomid'];
        $betmoney=(int)$_POST['money'];
        $betType=(int)$_POST['bettype'];
        $GameModel=D('Game');
        $GameInfo=$GameModel->getNewInfo($roomid);
        $startTime=$GameInfo['creatime'];
        $endTime=$GameInfo['creatime']+20;
        if(time()>$endTime){
            $this->ajaxReturn('','已经封盘！','0');
        }
        if($betType>10){
            $multiple=3.5;
        }else{
            $multiple=2.5;
        }
        $UserModel=D('Users');
        $usermoney=$UserModel->getusermoney($this->uid);
        $pankou=3000;
        $allbetmoney=$GameModel->getAllbetmoney($GameInfo['id']);
        if($usermoney<$betmoney){
            $this->ajaxReturn('','余额不足,请充值','0');
        }
        if($allbetmoney>2500){
            $this->ajaxReturn('','超出盘口了','0');
        }
        $ablebet=2500-$allbetmoney;
        if($betmoney*$multiple>$ablebet){
            $this->ajaxReturn('','超出盘口了','0');
        }


    }
}