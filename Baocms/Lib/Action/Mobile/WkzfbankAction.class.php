<?php
/**
 * Created by PhpStorm.
 * User: hyk
 * Date: 2018/6/20
 * Time: 9:11
 */

class WkzfbankAction extends CommonAction {

    //todo  实名认证请求信息
    public function index(){
        //接受用户id
        $ID=$_GET['ID'];
//        $ID=811;
        $tb_sss=M("sss");
        $sss['uid']=$ID;
        $list=$tb_sss->where($sss)->select();
        if($list){
            $this->jsonout('success','有储蓄卡',$list);
        }else{
            $this->jsonout('faild','没有储蓄卡',$list);
        }

    }

    // todo 开通所有通道权限 储蓄卡管理
    function save_vip_bankinfo() {

        $uid=$_GET['ID'];
        // $uid=769;
        $bk_info_name = $_GET["bk_info_name"]; //姓名
        //  $bk_info_name="张飞";

        $bk_info_idCard = $_GET["bk_info_idCard"]; //身份证
        //$bk_info_idCard="410503199808035139";


        $bk_info_bankNum = $_GET["bk_info_bankNum"]; //银行卡号
        // $bk_info_bankNum="6217858000095727845";

        $bk_info_bankName = $_GET["bk_info_bankName"];//银行名字
        //$bk_info_bankName="中国银行";

        $bk_info_phoneNum = $_GET["bk_info_phoneNum"]; //手机号
        // $bk_info_phoneNum="18503723336";

        $cardBranchBankName = "安阳文峰大道";

        $agtorg = "yqp0001";
        //机构号
        $mercid = "484584045119461";
        //商户号


        //第一个通道
        $randStr_YSJFBH = str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
        $rand_YSJFBH = "yqp0001" . substr($randStr_YSJFBH, 0, 6);
        //上传YSJFBH资料
        $urll_YSJFBH = "http://mpay.gwpos.cn/709071.tran8";
        //业务参数
        $post_data = array("agtorg" => $agtorg, "mercid" => $mercid, "merchantid" => $rand_YSJFBH, "merchantname" => "青鹏无卡交易", "certno" => $bk_info_idCard, "name" => $bk_info_name, "district" => "330106", "address" => "安阳市北关区", "bankno" => $bk_info_bankNum, "mobile" => $bk_info_phoneNum, "email" => "kaishenlaile@163.com", "province" => "330000", "city" => "330100");
        //生成签名不进行MD5算法加密
        $post_data["sign"] = $this->get_md5($this->getSign($post_data));
        $request_YSJFBH = $this->simplest_xml_to_array($this->https_post($urll_YSJFBH, $this->getSign($post_data)));


        //判断资料YSJFBH是否上传成功
        if ($request_YSJFBH["RSPCOD"] == "000000") {
            $sc_YSJFBH_erro = "OK";
            //开通YSJFBH
            $urll_YSJFBH = "http://mpay.gwpos.cn/709073.tran8";
            //业务参数
            $post_data = array("agtorg" => $agtorg, "mercid" => $mercid, "merchantid" => $rand_YSJFBH, "busytyp" => "YSJFBH", "top" => "99999900", "fixed" => "200", "rate" => "0.0055", "bottom" => "100");
            //生成签名不进行MD5算法加密
            $post_data["sign"] = $this->get_md5($this->getSign($post_data));
            $requestt_YSJFBH = $this->simplest_xml_to_array($this->https_post($urll_YSJFBH, $this->getSign($post_data)));


            //判断是否开通YSJF成功
            if ($requestt_YSJFBH["RSPCOD"] == "000000") {

                // 第三个通道

                //创建空数组保存返回来的4个data数据
                $arrs = array();
                //调用接口
                for ($x = 1; $x < 5; $x++) {
                    if ($x != 5) {
                        $resstr = $this->uploadPictures($arrs);
                        if ($resstr != "-1-1-1-1") {
                            array_push($arrs, $resstr);
                        }
                    }
                }

                //上传MZJF资料
                $data1["certType"] = "1";
                $data1["fileDesc"] = "身份证正面照片";
                $data1["fileNo"] = $arrs[0];

                $data2["certType"] = "2";
                $data2["fileDesc"] = "身份证反面照片";
                $data2["fileNo"] = $arrs[1];

                $data3["certType"] = "4";
                $data3["fileDesc"] = "结算卡正面照片";
                $data3["fileNo"] = $arrs[2];

                $data4["certType"] = "5";
                $data4["fileDesc"] = "手持身份证照片";
                $data4["fileNo"] = $arrs[3];

                $data5 = array();
                array_push($data5, $data1, $data2, $data3, $data4);
                $cifCerts = json_encode($data5);

                $randStr_MZJF = str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
                $rand_MZJF = "yqp0001" . substr($randStr_MZJF, 0, 6);



                //上传MZJF资料
                $urll_MZJF = "http://mpay.gwpos.cn/709116.tran8";
                //业务参数
                $post_data = array("agtorg" => $agtorg, "mercid" => $mercid, "merchantid" => $rand_MZJF, "merchantname" => "青鹏交易", "certno" => $bk_info_idCard, "name" => $bk_info_name, "address" => "安阳市北关区", "cardBankName" => $bk_info_bankName, "cardBankNo" => "104496062824", "cardBranchBankName" => $cardBranchBankName, "bankno" => $bk_info_bankNum, "mobile" => $bk_info_phoneNum, "email" => "kaishenlaile@163.com", "cifCerts" => $cifCerts);
                //生成签名不进行MD5算法加密
                $post_data["sign"] = $this->get_md5($this->getSign($post_data));
                $request = $this->simplest_xml_to_array($this->https_post($urll_MZJF, $this->getSign($post_data)));

                //判断MZJF资料是否上传成功
                if ($request["RSPCOD"] == "000000") {

                    //MZJF资料上传成功
                    $sc_MZJF_erro = "OK";
                    //开通MZJF业务
                    $urll = "http://mpay.gwpos.cn/709118.tran8";
                    //业务参数
                    $post_data = array("agtorg" => "yqp0001", "mercid" => "484584045119461", "merchantid" => $rand_MZJF, "busytyp" => "MZJF", "top" => "99999900", "fixed" => "200", "rate" => "0.0055", "bottom" => "100");
                    //生成签名不进行MD5算法加密
                    $post_data["sign"] = $this->get_md5($this->getSign($post_data));
                    $requestt = $this->simplest_xml_to_array($this->https_post($urll, $this->getSign($post_data)));

                    //判断MZJF业务是否开通
                    if ($requestt["RSPCOD"] == "000000") {
                        //MZJF开通成功
                        $tb_sss=M("sss");
                        $ss['banknum']=$bk_info_bankNum;
                        $s=$tb_sss->where($ss)->select();
                        if ($s){
                            $data['RSPMSG']="已有此银行卡";
                            $this->jsonout('faild','已有此银行卡',$data);
                        }
                        $date = date("Y-m-d H:i:s");
                        //存储用户信息
                        $sss[]=array('uid'=>$uid,'name'=>$bk_info_name,'idcard'=>$bk_info_idCard,'bankname'=>$bk_info_bankName,'banknum'=>$bk_info_bankNum,'phonenum'=>$bk_info_phoneNum,'merchantid_YSJFBH'=>$rand_YSJFBH,'merchantid_MZJF'=>$rand_MZJF,'time'=>$date);
                        $tb_sss->addAll($sss);


                        $this->jsonout('success','',$requestt);

                    } else {
                        //MZJF开通失败
                        $kt_MZJF_erro = "NO";
                        $this->jsonout('faild','',$requestt);

                    }
                } else {
                    //MZJF资料上传失败
                    $sc_MZJF_erro = "NO";

                    $this->jsonout('faild','',$request);

                }
            } else {

                $kt_YSJF_erro = "NO";
                $this->jsonout('faild','',$requestt_YSJFBH);

            }


        } else {
            $sc_YSJFBH_erro = "NO";

            $this->jsonout('faild','',$request_YSJFBH);
        }

    }

    // todo 删除储蓄卡
    public function deletecxbank(){

        //获取用户信息
        $ID=$_GET['ID']; //用户id
        $cxid=$_GET['cxid']; // 储蓄卡id


        //调用登陆验证
        //   $this->login_verify($user_id,$randpwd);

        //删除银行卡
        $tb_bank=M('sss');
        $bank['uid']=$ID;
        $bank['id']=$cxid;
        $list=$tb_bank->where($bank)->delete();

        if ($list){
            $this->jsonout('success','删除成功！');
        }else{
            $this->jsonout('faild','删除失败！');
        }
    }

    // todo 信用卡列表
    public function xybank(){

        $ID=$_GET['ID'];
        $tb_xxx=M("xxx");
        $xxx['uid']=$ID;
        $list=$tb_xxx->where($xxx)->select();
        if ($list){
            $this->jsonout('success','',$list);
        }else{
            $this->jsonout('faile','没有信用卡','');
        }
    }

    // todo 添加信用卡
    public function addxybank(){

        $ID=$_GET['ID'];  //用户id

        $bk_info_name = $_GET["bk_info_name"];  // 用户名称
        // echo"name ==". $bk_info_name;
        $bk_info_idCard = $_GET["bk_info_idCard"]; //身份证
        //  echo "   idcard".$bk_info_idCard;
        $bk_info_bankNum = $_GET["bk_info_bankNum"]; //信用卡号
        // echo  " bankNum".$bk_info_bankNum;
        $bk_info_bankName = $_GET["bk_info_bankName"];//银行名称

        //  echo "bankName".$bk_info_bankName;
        $bk_info_phoneNum = $_GET["bk_info_phoneNum"]; // 手机号码
        //  echo$bk_info_phoneNum;
        $bk_info_yxTime = $_GET["bk_info_yxTime"];  // 有效期
        //  echo $bk_info_yxTime;
        $bk_info_cvnNum = $_GET["bk_info_cvnNum"];   // cvn
        // echo $bk_info_cvnNum;
        $bk_info_hkTime = $_GET["bk_info_hkTime"];   // 还款日期
        //  echo $bk_info_hkTime;
        $bk_info_zdInfo = $_GET["bk_info_zdInfo"];   //账单日期
//die();
//        $sql = "INSERT INTO xxx (uid, name,idcard,banknum,bankname,phonenum,youxiaoqi,cvn,huankuantime,zhangdanxx) VALUES ($ID,'$bk_info_name','$bk_info_idCard','$bk_info_bankNum','$bk_info_bankName','$bk_info_phoneNum','$bk_info_yxTime','$bk_info_cvnNum','$bk_info_hkTime','$bk_info_zdInfo')";
//
//        $flag = mysql_query($sql);
        //存入数据库
        $tb_xxx=M("xxx");
        $xx['banknum']=$bk_info_bankNum;
        $xx['uid']=$ID;
        $list=$tb_xxx->where($xx)->select();
        if ($list){
            $this->jsonout('faile','已有这张信用卡');
        }else{

            $xxx['uid']=$ID;
            $xxx['name']=$bk_info_name;
            $xxx['idcard']=$bk_info_idCard;
            $xxx['banknum']=$bk_info_bankNum;
            $xxx['bankname']=$bk_info_bankName;
            $xxx['phonenum']=$bk_info_phoneNum;
            $xxx['youxiaoqi']=$bk_info_yxTime;
            $xxx['cvn']=$bk_info_cvnNum;
            $xxx['huankuantime']=$bk_info_hkTime;
            $xxx['zhangdanxx']=$bk_info_zdInfo;
            $tb=$tb_xxx->add($xxx);

            if ($tb){
                $this->jsonout('success','保存成功','');
            }else{
                $this->jsonout('faile','保存失败','');
            }
        }

    }
    // todo 删除信用卡
    public function deletexybank(){
        //获取用户信息
        $ID=$_GET['ID']; //用户id
        $xyid=$_GET['xyid']; // 信用卡id


        //调用登陆验证
        //   $this->login_verify($user_id,$randpwd);

        //删除银行卡
        $tb_bank=M('xxx');
        $bank['uid']=$ID;
        $bank['id']=$xyid;
        $list=$tb_bank->where($bank)->delete();

        if ($list){
            $this->jsonout('success','删除成功！');
        }else{
            $this->jsonout('faild','删除失败！');
        }

    }

    //todo 无卡支付交易记录
    public function wk_record(){
        //查询刷卡记录
        $ID=$_GET['ID'];
        // $ID=811;
        $tb_yyy=M("yyy");
        $yyy['uid']=$ID;
        $yyy['deal_status']=2;
        $list=$tb_yyy->where($yyy)->select();

        $this->jsonout('success','',$list);
    }


    // todo 支付通道1  订单提交
    public function ysjfbh_pay1(){

        $ID=$_GET['ID']; //用户id
        //$ID=811;
        // echo "id == ".$ID;

        $xyid = $_GET["xyid"]; //信用卡id
        // $xyid=17;
        // echo"----------xyid ==".$xyid;
        $money = $_GET["money"];

        // echo "-------money ==".$money;
        // $money=500;
        $money = $money * 100;

        $kahao = $_GET["kahao"];
        //  echo "---------kahao ==".$kahao;




        //  $kahao="6212261706002565613";   //储蓄卡号
        //接口URL

        $urll = "http://mpay.gwpos.cn/709076.tran8";
        //随机流水号
        $randStr = str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');

        $rand = "yqp0001" . substr($randStr, 0, 6);

//业务参数
        // 查询储蓄卡
        $tb_sss=M('sss');
        $s['banknum']=$kahao;
        $s['uid']=$ID;
        $result_cx=$tb_sss->where($s)->find();


        //查询信用卡
        $tb_xxx=M('xxx');
        $x['id']=$xyid;
        $x['uid']=$ID;
        $result_xy=$tb_xxx->where($x)->find();




        $post_data = array("agtorg" => "yqp0001", "mercid" => "484584045119461",

            "ordernumber" => $rand, "merchantid" => $result_cx["merchantid_YSJFBH"],

            "nodbackurl" => "http://qp.webziti.com/mobile/wkzfbank/ysjfbh_pay2",

            "body" => "我是测试呢", "amount" => $money,

            "busytyp" => "YSJFBH", "bankAccount" => $result_xy["banknum"],

            "accountName" => $result_xy["name"], "idCard" => $result_xy["idcard"],

            "payMobile" => $result_xy["phonenum"],"frontUrl" =>"http://www.baidu.com");

//        $post_data = array("agtorg" => "yqp0001", "mercid" => "484584045119461",
//
//            "ordernumber" => $rand, "merchantid" => "yqp00017VYSWT",
//
//            "nodbackurl" => "http://www.163.com",
//
//            "body" => "我是测试呢", "amount" => $money,
//
//            "busytyp" => "YSJFBH", "bankAccount" => "6226890160452974",
//
//            "accountName" => "耿凯祥", "idCard" => "410522199110289339",
//
//            "payMobile" => "18625498727","frontUrl" =>"http://www.baidu.com");

//生成签名不进行MD5算法加密


        $post_data["sign"] = $this->get_md5($this->getSign($post_data));

//post请求

        $res = $this->https_post($urll, $this->getSign($post_data));

//返回结果转数组

        $arr_res = $this->xmlToArray($res);

        //  print_r($arr_res);

        //操作成功
        if ($arr_res['RSPCOD'] == '000000'){

            $tb_yyy=M('yyy');
            $yyy['uid']=$ID;   // 用户id
            $yyy['deal_user_name']=$result_cx['name'];  //用户姓名
            $yyy['deal_chuxv_num']=$result_cx['banknum']; //储蓄卡号
            $yyy['deal_chuxv_name']=$result_cx['bankname']; //储蓄银行
            $yyy['deal_money']=$money;  //交易金额
            $yyy['deal_time']=date("Y-m-d H:i:s");;  //时间
            $yyy['deal_xinyong_name']=$result_xy['bankname']; //信用卡银行
            $yyy['deal_xinyong_num']=$result_xy['banknum']; //信用卡号
            $yyy['deal_ordernumber']=$rand;  // 订单号
            $yyy['deal_status']=1;  // 交易状态
            $tb_yyy->add($yyy);
            $post_data['url']=$arr_res['DATA']['html']['body']['form']['@attributes']['action'];
            $this->jsonout('success','',$post_data);

        }else{
            //交易失败
            $this->jsonout('faild','',$arr_res);
        }

    }

    //todo 支付订单1  接受交易结果
    public function ysjfbh_pay2(){

        $rspcod=$_GET['rspcod']; //交易结果

        $orderno=$_GET['orderno']; //订单编号

        $timeStamp=$_GET['timeStamp'];//交易时间
        //echo   "交易结果 ==". $rspcod;

        // echo   " 订单编号 ==". $orderno;

        //echo " 交易时间".$timeStamp;

        // $timeStamp="20180718152927";
        //  Y-m-d H:i:s
        $Y= mb_substr($timeStamp,0,4,"UTF-8");   //年
        $m=mb_substr($timeStamp,4,2,"UTF-8");    //月
        $d=mb_substr($timeStamp,6,2,"UTF-8");    //日

        $H=mb_substr($timeStamp,8,2,"UTF-8");    //时
        $i=mb_substr($timeStamp,10,2,"UTF-8");   //分
        $s=mb_substr($timeStamp,12,2,"UTF-8");   //秒

        $timeStamp= $Y."-".$m."-".$d." ".$H.":".$i.":".$s;

        //判断回调的交易结果 2成功 其他则为失败
        if ($rspcod == 2){

            $tb_yyy=M('yyy');
            $yyy['deal_status']=2;
            $yyy['deal_time']=$timeStamp;
            $tb_yyy->where("deal_ordernumber='$orderno'")->save($yyy);
            $this->jsonout('success','交易成功',$rspcod);
        }
    }


//    public function ysjfbh_pay2(){
//
//
//        $agtorg = "yqp0001";
//
//        $mercid = "484584045119461";
//
//
//        /*$_rand_suiji = "yqp0001Y7HF4O";
//
//        $_yzm = "485484";*/
//
//        $_rand_suiji = $_GET['ordernumber'];  //订单号
//
//        $_yzm = $_GET["_yzzm"];   //验证码
//
//        $uid=$_GET['ID'];    // 用户 id
//
//        $name=$_GET['accountName']; //用户姓名
//
//        $money =$_GET['amount']; // 交易金额
//
//        $chuxunum=$_GET['cx_num'];  //储蓄卡号
//
//        $chuxuname=$_GET['merchantid_YSJFBH'];
//
//        $xinyongnum=$_GET['bankAccount'];  //信用卡号
//
//        $xinyongname=$_GET['xinyongname']; //信用卡银行
//
//        $time = date("Y-m-d H:i:s");    //时间
//
//        //接口URL
//
//        $urll = "http://mpay.gwpos.cn/709077.tran8";
//
//        //业务参数
//
//        $post_data = array("agtorg" => $agtorg, "mercid" => $mercid,
//
//            "ordernumber" => $_rand_suiji, "verifyCode" => $_yzm);
//
//
//
//        //生成签名不进行MD5算法加密
//
//        $post_data["sign"] = $this->get_md5($this->getSign($post_data));
//
//        //echo getSign($post_data);
//
//
//
//        $rese = $this->https_post($urll, $this->getSign($post_data));
//
//
//        $arr_res = $this->xmlToArray($rese);
//
//
//        if($arr_res["RSPCOD"] == "000000"){
//
//            $tb_yyy=M('yyy');
//            $yyy['uid']=$uid;   // 用户id
//            $yyy['deal_chuxv_name']=$name;  //用户姓名
//            $yyy['deal_chuxv_num']=$chuxunum; //储蓄卡号
//            $yyy['deal_chuxv_name']=$chuxuname; //储蓄银行
//            $yyy['deal_money']=$money;  //交易金额
//            $yyy['deal_time']=$time;  //时间
//            $yyy['deal_xinyong_name']=$xinyongname; //信用卡银行
//            $yyy['deal_xinyong_num']=$xinyongnum; //信用卡号
//            $yyy['deal_']=$_rand_suiji;  // 订单号
//            $tb_yyy->add($yyy);
//
//            $this->jsonout('success','',$arr_res);
//
//        }else{
//
//            $this->jsonout('faild','',$arr_res);
//
//        }
//
//
//    }


    //  todo 通道2 MZJF
    public function mzjf_pay1(){


        //接口URL
        $urll = "http://mpay.gwpos.cn/709120.tran8";

        //随机流水号
        $randStr = str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
        $rand = "yqp0001" . substr($randStr, 0, 6);
        //业务参数

        $ID=$_GET['ID'];  //用户id
        $xyid = $_GET["xyid"];   //信用卡id
        // $xyid=17;
        //  $ID="769";
        $money = $_GET["money"];   //交易金额
        //$money=500;
        $money = $money * 100;
        $kahao = $_GET["kahao"];   //储蓄卡id
        // $kahao="6212261706002565613";
        //查询储蓄卡信息
        $tb_sss=M("sss");
        $sss['banknum']=$kahao;
        $sss['uid']=$ID;
        $result_cx=$tb_sss->where($sss)->find();

        //查询信用卡信息

        $tb_xxx=M("xxx");
        $xxx['id']=$xyid;
        $xxx['uid']=$ID;
        $result_xy=$tb_xxx->where($xxx)->find();



        $post_data = array("agtorg" => "yqp0001", "mercid" => "484584045119461", "ordernumber" => $rand, "merchantid" => $result_cx["merchantid_MZJF"], "nodbackurl" => "http://jinfu.yiaigo.com/main.php", "body" => "青鹏交易", "amount" => $money, "busytyp" => "MZJF", "bankAccount" => $result_xy["banknum"], "accountName" => $result_xy["name"], "idCard" => $result_xy["idcard"], "payMobile" => $result_xy["phonenum"], "expired" => $result_xy["youxiaoqi"], "cvn2" => $result_xy["cvn"], "frontUrl" => "http://www.baidu.com");



        //生成签名不进行MD5算法加密
        $post_data["sign"] = $this->get_md5($this->getSign($post_data));

        $res = $this->simplest_xml_to_array($this->https_post($urll, $this->getSign($post_data)));



        if ($res["RSPCOD"] == "000000") {
            $this->jsonout('success','',$post_data);
        }else{
            $this->jsonout('faile','',$res);
        }
    }

    //  todo 通道2 MZJF
    public function mzjf_pay2(){

        $agtorg = "yqp0001";
        $mercid = "484584045119461";

        $_rand_suiji = $_GET['ordernumber'];  //订单号

        // echo "订单==".$_rand_suiji;
        $_yzm = $_GET["_yzzm"];   //验证码
        // echo "-----验证码 == ".$_yzm;
        $uid=$_GET['ID'];    // 用户 id
        // echo "-----ID == ".$uid;
        $name=$_GET['accountName']; //用户姓名
        // echo "----- 用户姓名== ".$name;
        $money =$_GET['amount']; // 交易金额
        // echo "-----交易金额== ".$money;
        $chuxunum=$_GET['deal_chuxv_num'];  //储蓄卡号
        //  echo "-----储蓄卡号== ".$chuxunum;
        $chuxuname=$_GET['deal_chuxv_name'];  //储蓄银行
        // echo "-----储蓄银行== ".$chuxuname;
        $xinyongnum=$_GET['bankAccount'];  //信用卡号
        // echo "-----信用卡号== ".$xinyongnum;
        $xinyongname=$_GET['deal_xinyong_name']; //信用卡银行
        //   echo "-----信用卡银行== ".$xinyongname;
        $time = date("Y-m-d H:i:s");    //时间
        ;
        //接口URL
        $urll = "http://mpay.gwpos.cn/709121.tran8";

        //业务参数
        $post_data = array("agtorg" => $agtorg, "mercid" => $mercid, "ordernumber" => $_rand_suiji, "verifyCode" => $_yzm);

        //生成签名不进行MD5算法加密
        $post_data["sign"] = $this->get_md5($this->getSign($post_data));
        $rese = $this->https_post($urll, $this->getSign($post_data));

        $arr_res = $this->simplest_xml_to_array($rese);

        if ($arr_res["RSPCOD"] == "000000") {

            //交易成功 存入数据库记录
            $tb_yyy=M('yyy');
            $yyy['uid']=$uid;   // 用户id
            $yyy['deal_user_name']=$name;  //用户姓名
            $yyy['deal_chuxv_num']=$chuxunum; //储蓄卡号
            $yyy['deal_chuxv_name']=$chuxuname; //储蓄卡银行
            $yyy['deal_money']=$money;  //交易金额
            $yyy['deal_time']=$time;  //时间
            $yyy['deal_xinyong_num']=$xinyongnum; //信用卡号
            $yyy['deal_xinyong_name']=$xinyongname; //信用卡银行
            $yyy['deal_ordernumber']=$_rand_suiji;  // 订单号
            $yyy['deal_status']=2;  // 交易状态
            $tb_yyy->add($yyy);

            $this->jsonout('success','',$arr_res);


        }else{
            $this->jsonout('faild','',$arr_res);
        }

    }

    //  todo MZJF图片上送 7-6
    function uploadPictures($arr) {
        //业务参数
        $fileType = "application/x-img";
        $base64FileContent = "data:image/png;base64,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";
        //接口URL
        $urll = "http://mpay.gwpos.cn/709115.tran8";
        //接口参数数组
        $post_data = array("fileType" => $fileType, "base64FileContent" => $base64FileContent);
        //生成签名不进行MD5算法加密
        $post_data["sign"] = $this->get_md5($this->getSign($post_data));
        //发送请求返回结果集
        $request_tu = $this->simplest_xml_to_array($this->https_post($urll, $this->getSign($post_data)));
        //判断结果集
        if ($request_tu["RSPCOD"] == "000000") {
            $DATA = $request_tu["DATA"];
            return $DATA;
        } else {
            echo "图片上传失败";
            return "-1-1-1-1";
        }
    }


}
?>