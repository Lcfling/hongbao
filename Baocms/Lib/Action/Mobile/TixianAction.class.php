<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/20
 * Time: 15:06
 */

//$userOpenID=$_SESSION['openid'];

class TixianAction extends CommonAction {

    //todo 提现       Lee_zhj
    public function tixian() {
        date_default_timezone_set('PRC');
        $time=date("Y-m-d H:i:s");

        $qqqq = I('post.aaa');
        $bankinfo = I('post.ubankinfo');
        $name = I('post.username');
        $banknum = I('post.bank_num');
        $yue = I('post.yue');


        //todo 查询银行卡种。。11
        $tb_bank=M(tb_bank);
        $bank['openID']=$userOpenID;
        $sqlbank=$tb_bank->where($bank)->field('bank_class')->select();


        //todo 用户提现时的信息保存到数据库
        $record = M("tb_record"); // 实例化User对象
        $recordinfo['openID']=$userOpenID;
        $recordinfo['txmoney']=$qqqq;
        $recordinfo['date']=$time;
        $recordinfo['status']=0;
        $recordinfo['username']=$name;
        $recordinfo['bank_info']=$bankinfo;
        $recordinfo['bank_num']=$banknum;
        $recordinfo['yue']=$yue;
        $recordinfo['bank_class']=$sqlbank;

        $record->fetchSql(true)->add($recordinfo);

    }

}