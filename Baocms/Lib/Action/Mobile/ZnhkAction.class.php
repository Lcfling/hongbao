<?php



class ZnhkAction extends CommonAction {

    //todo 参数配置

   public function config()
    {
        // $this->db   = db::getInstance();
        return array(
            'yeepayUrl'=>'https://skb.yeepay.com/skb-app/', #易宝服务端站点
            'md5Key'=>'2w7tox74rAxrK153594gr43C5jjL7hMnho56wjuCx3gI0ar53pl60XVJn4Z0', #Md5密钥
            'mainCustomerNumber'=>'10015386628', #易宝主商户号
        );
    }
    /*
       * 判断是否注册
       * */
    public function findUser() {
        $id = $_GET['ID'];
        $tb_yibao_wuka = M('znhk_register');
        // 实例化User对象
        $Modeldata = $tb_yibao_wuka -> where('user_id=' . $id) ->field('user_customerNumber')-> find();
    if ($Modeldata){
       $list= $tb_yibao_wuka->where('user_id='.$id)->find();
    $this->jsonout('success','已实名认证',$list);
}else{
    $this->jsonout('faild','未实名认证');
}

    }


  //todo 子商户注册 1
    public function SaveData() {
        $ID = $_GET['ID'];
        //$randpwd = $_GET['randpwd'];
        $bk_info_name = $_GET['bk_info_name'];
        $bk_info_idCard = $_GET['bk_info_idCard'];
        $bk_info_bankNum = $_GET['bk_info_bankNum'];
        $bk_info_bankName = $_GET['bk_info_bankName'];
        $bk_info_phoneNum = $_GET['bk_info_phoneNum'];
        //数据插入表
        $tb_yibao_wuka = M('znhk_register');
        $Modeldata = $tb_yibao_wuka -> where('user_id=' . $ID) -> find();

        if(empty($Modeldata)){
            $yibao_wuka['user_id'] = $ID;
            $yibao_wuka['user_name'] = $bk_info_name;
            $yibao_wuka['user_idcard'] = $bk_info_idCard;
            $yibao_wuka['user_phone'] = $bk_info_phoneNum;
            $yibao_wuka['user_banknum'] = $bk_info_bankNum;
            $yibao_wuka['user_bankname'] = $bk_info_bankName;
            $yibao_wuka['user_State'] = 1;
            $flgnum = $tb_yibao_wuka -> add($yibao_wuka);
            if ($flgnum > 0) {
                $this -> jsonout('success', '成功!', null);
            } else {
                $this -> jsonout('faild', '失败!', null);
            }
        }else{
            $yibao_wuka['user_id'] = $ID;
            $yibao_wuka['user_name'] = $bk_info_name;
            $yibao_wuka['user_idcard'] = $bk_info_idCard;
            $yibao_wuka['user_phone'] = $bk_info_phoneNum;
            $yibao_wuka['user_banknum'] = $bk_info_bankNum;
            $yibao_wuka['user_bankname'] = $bk_info_bankName;
            $flgnum = $tb_yibao_wuka -> where('user_id=' . $ID) -> save($yibao_wuka);
            if ($flgnum >= 0) {
                $this -> jsonout('success', '成功!', null);
            } else {
                $this -> jsonout('faild', '失败!', null);
            }
        }
    }

    //todo 子商户注册 2
    public function reg() {

        $ID = $_GET['ID'];
        $tb_yibao_wuka = M('znhk_register');
        // 实例化User对象
        $Modeldata = $tb_yibao_wuka -> where('user_id=' . $ID) -> find();

        $bk_info_idCard = $Modeldata['user_idcard'];
        $bk_info_phoneNum = $Modeldata['user_phone'];
        $bk_info_bankNum = $Modeldata['user_banknum'];
        $bk_info_bankName = $Modeldata['user_bankname'];
        $bk_info_name = $Modeldata['user_name'];

        //sss
        $sss['idcard'] = $bk_info_idCard;
        $sss['bankname'] = $bk_info_bankName;
        $sss['banknum'] = $bk_info_bankNum;
        $sss['phonenum'] = $bk_info_phoneNum;
        $sss['name'] = $bk_info_name;

        $user_IDcardFront = $Modeldata['user_IDcardFront'];
        $arr1 = pathinfo($user_IDcardFront);
        $user_IDcardBack = $Modeldata['user_IDcardBack'];
        $arr2 = pathinfo($user_IDcardBack);
        $user_bankImg = $Modeldata['user_bankImg'];
        $arr3 = pathinfo($user_bankImg);
        $user_AllImg = $Modeldata['user_AllImg'];
        $arr4 = pathinfo($user_AllImg);

        //照片地址
        $d = BASE_PATH;
        //注册流水号
        $randNum = rand_string(20);
        $url = "register.action";
        $data = array(
            'mainCustomerNumber' => $this -> config()['mainCustomerNumber'],
            'requestId' => $randNum, #注册流水号
            'customerType' => 'PERSON', #个人|PERSON , ENTERPRISE|企业 , INDIVIDUAL|个体工商户
            'bindMobile' => $bk_info_phoneNum, #绑定的手机号
            'signedName' => $bk_info_name, #商户签约名： 在注册商户类型为个人用户时,保证 签约名和开户名一样; 在注册商户类型为企业、个体工商户 时,签约名传企业名称全称
            'linkMan' => $bk_info_name, #推荐人姓名
            'idCard' => $bk_info_idCard, #商户法人身份证号,同一个身份证号，只能在一个大商户下注册一个账号
            'legalPerson' => $bk_info_name, #商户的法人姓名
            'minSettleAmount' => '1', #起结金额
            'riskReserveDay' => '0', #0|T0出款,N|TN出款
            'bankAccountNumber' => $bk_info_bankNum,
            'bankName' => $bk_info_bankName, #工商银行、农业银行、招商银行、建设银行、交通银行、中信银行、光大银行、北京银行、深圳发展银行 、中国银行、兴业银行、民生银行
            'accountName' => $bk_info_name, #开户名
            'areaCode' => '1000', #商户所在地区,请根据【银联 32 域码 表 0317.xls-来自易宝】,填写编码
            'certFee' => '0', #认证费用
            'manualSettle' => 'N', # N否是自助结算 N - 隔天自动打 款;Y - 不会自动打款
            'BankCardPhoto' => new CURLFile($d.'/'.$user_bankImg, 'image/jpeg', $arr3['basename']), #银行卡正面照
            'idCardPhoto' => new CURLFile($d.'/'.$user_IDcardFront, 'image/jpeg', $arr1['basename']), #身份证正面照
            'idCardBackPhoto' => new CURLFile($d.'/'.$user_IDcardBack, 'image/jpeg', $arr2['basename']), #身份证背面照
            'PersonPhoto' => new CURLFile($d.'/'.$user_AllImg, 'image/jpeg', $arr4['basename']), #银行卡与身份证及本人上半身合照
        );

        $res = $this -> yeepayData1($url, $data);
        //判断易宝的返回值状态
        if ($res['code'] != '0000') {
            $this -> jsonout('faild', $res['message'], $res);
        } else {
            $customerNumber = $res['customerNumber'];
            $user_State['user_customerNumber'] =$customerNumber;
            $user_State['user_State'] = 6;
            $tb_yibao_wuka -> where('user_id=' . $ID) -> save($user_State);
            $this->fee($customerNumber);
            $this -> jsonout('success', $res['message'], $res);
        }
    }


    /*
     * 银行卡照片上传
     * */
    public function UploadPhotosBank() {
        $ID = $_POST['ID'];
        $randpwd = $_POST['randpwd'];
        $type = $_POST['type'];
        $d = BASE_PATH;
        if(!empty($ID)){
            $tb_yibao_wuka = M('znhk_register');
            $Modeldata = $tb_yibao_wuka -> where('user_id=' . $ID) -> find();
            if($type=='bank'){
                $d = $d.'/'.$Modeldata['user_bankImg'];
                unlink($d);
            }elseif ($type=='hezhao'){
                $d = $d.'/'.$Modeldata['user_AllImg'];
                unlink($d);
            }elseif ($type=='idcard_back'){
                $d = $d.'/'.$Modeldata['user_IDcardBack'];
                unlink($d);
            }else{
                //idcard_face
                $d = $d.'/'.$Modeldata['user_IDcardFront'];
                unlink($d);
            }
        }

        $tmp = $_FILES['img']['tmp_name'];

        $arr = pathinfo($_FILES['img']['name']);

        $imgname = time() . rand(100000, 999999) . '.' . $arr['extension'];

        $dir = __ROOT__ . 'znhkupload';
        //按照年月日创建目录
        $file_path = "$dir" . '/' . date("Y") . '/' . date("m") . '/' . date("d") . '/';
        if (!is_dir($file_path)) {
            mkdir($file_path, 0777, true);
        }
        if ($_FILES["img"]["size"] < 512000) {
            if (move_uploaded_file($tmp, $file_path . $imgname)) {
                //保存路径
                $lujing = $file_path . $imgname;
                $this -> SaveImg($lujing, $type, $ID);
                $this -> jsonout('success', '上传成功', null);
            } else {
                $this -> jsonout('faild', '上传失败', null);
            }
        } else {
            $this -> jsonout('faild', '照片大小超出最大值', null);
        }
    }

    //保存图片路径
    public function SaveImg($str, $type, $id) {
        if ($type == 'idcard_face') {
            $tb_yibao_wuka = M('znhk_register');
            // 实例化User对象
            $yibao_wuka['user_IDcardFront'] = $str;
            $flgnum = $tb_yibao_wuka -> where('user_id=' . $id) -> save($yibao_wuka);
            if ($flgnum > 0) {
                $zhuangtai['user_State'] = 3;
                $tb_yibao_wuka -> where('user_id=' . $id) -> save($zhuangtai);
                $this -> jsonout('success', '身份证正面照保存成功!', null);
            } else {
                $this -> jsonout('faild', '身份证正面照保存失败!', null);
            }
        }
        if ($type == 'idcard_back') {
            $tb_yibao_wuka = M('znhk_register');
            // 实例化User对象
            $yibao_wuka['user_IDcardBack'] = $str;
            $flgnum = $tb_yibao_wuka -> where('user_id=' . $id) -> save($yibao_wuka);
            if ($flgnum > 0) {
                $zhuangtai['user_State'] = 4;
                $tb_yibao_wuka -> where('user_id=' . $id) -> save($zhuangtai);
                $this -> jsonout('success', '身份证背面照保存成功!', null);
            } else {
                $this -> jsonout('faild', '身份证背面照保存失败!', null);
            }
        }
        if ($type == 'hezhao') {
            $tb_yibao_wuka = M('znhk_register');
            // 实例化User对象
            $yibao_wuka['user_AllImg'] = $str;
            $flgnum = $tb_yibao_wuka -> where('user_id=' . $id) -> save($yibao_wuka);
            if ($flgnum > 0) {
                $zhuangtai['user_State'] = 5;
                $tb_yibao_wuka -> where('user_id=' . $id) -> save($zhuangtai);
                $this -> jsonout('success', '合照保存成功!', $zhuangtai);
            } else {
                $this -> jsonout('faild', '合照保存失败!', null);
            }
        }
        if ($type == 'bank') {
            $tb_yibao_wuka = M('znhk_register');
            // 实例化User对象
            $yibao_wuka['user_bankImg'] = $str;
            $flgnum = $tb_yibao_wuka -> where('user_id=' . $id) -> save($yibao_wuka);
            if ($flgnum > 0) {
                $zhuangtai['user_State'] = 2;
                $tb_yibao_wuka -> where('user_id=' . $id) -> save($zhuangtai);
                $this -> jsonout('success', '银行卡照保存成功!', null);
            } else {
                $this -> jsonout('faild', '银行卡照保存失败!', null);
            }
        }
    }


    /**
     * 子商户信息查询
     * @param string $mobilePhone 子商户手机号码
     * @param string $customerNumber 子商户编号
     * @param string $customerType 商户类型
     */
    //todo 子商户信息查询
    public function infoQuery(){
        $mobilePhone='18625498727';
        $customerNumber='10024691472';

        $url="customerInforQuery.action";
        $data=array(
            'mainCustomerNumber'=>$this->config()['mainCustomerNumber'],
            'mobilePhone'=>$mobilePhone,
            'customerNumber'=>$customerNumber,

        );
        $result=$this->yeepayData($url,$data);
        print_r($result);

    }

    /**
     * 费率设置
     * @param string $customerNumber     子商户编号
     * @param number $productType        产品类型   1.交易 2.提现 3.日结通基本 4.日结通额外 5.日结通非工作日
     * @param number $rate               费率
     */
    //todo 费率设置
    public function fee($customerNumber){

        $productType='3';
        $rate='2';

        $url="feeSetApi.action";
        $data=array(
            'customerNumber'=>$customerNumber,
            'mainCustomerNumber'=>$this->config()['mainCustomerNumber'],
            'productType'=>$productType,
            'rate'=>$rate
        );

        $result=$this->yeepayData($url,$data);
       if ($result['code'] == '0000'){

           $productType1='8';
           $rate1='0.005';
           $url1="feeSetApi.action";
           $data1=array(
               'customerNumber'=>$customerNumber,
               'mainCustomerNumber'=>$this->config()['mainCustomerNumber'],
               'productType'=>$productType1,
               'rate'=>$rate1
           );

           $result1=$this->yeepayData($url1,$data1);
           if ($result1['code'] == '0000'){

               $productType2='4';
               $rate2='0';
               $url2="feeSetApi.action";
               $data2=array(
                   'customerNumber'=>$customerNumber,
                   'mainCustomerNumber'=>$this->config()['mainCustomerNumber'],
                   'productType'=>$productType2,
                   'rate'=>$rate2
               );
               $result2=$this->yeepayData($url2,$data2);

               if ($result2['code'] == '0000'){

                   $productType3='5';
                   $rate3='0';
                   $url3="feeSetApi.action";
                   $data3=array(
                       'customerNumber'=>$customerNumber,
                       'mainCustomerNumber'=>$this->config()['mainCustomerNumber'],
                       'productType'=>$productType3,
                       'rate'=>$rate3
                   );
                   $result3=$this->yeepayData($url3,$data3);
                   if ($result3['code'] == '0000'){


                       $this->jsonout('success',$result['message'],$result);

                   }else{
                       $this->jsonout('faild444',$result['message'],$result);
                   }

               }else{
                   $this->jsonout('faild333',$result['message'],$result);
               }
           }else{
               $this->jsonout('faild222',$result['message'],$result);
           }
       }else{
           $this->jsonout('faild111',$result['message'],$result);
       }
    }


    public function fee1(){
        $customerNumber='10024997707';
        $productType='5';
        $rate='0';

        $url="feeSetApi.action";
        $data=array(
            'customerNumber'=>$customerNumber,
            'mainCustomerNumber'=>$this->config()['mainCustomerNumber'],
            'productType'=>$productType,
            'rate'=>$rate
        );

        $result=$this->yeepayData($url,$data);
        print_r($result);
    }


//---------------------------------------子商户交易------------------------------------------
    /**
     * 绑卡/首次支付
     * @param string $customerNumber 子商户编号
     * @param number $amount         金额
     * @param string $requestId      收款订单号
     * @param string $src        支付方式  B - 店主收款
     * @param string $mcc            商品分类     5311：百货商店       4511：航空公司      4733：大型景区售票
     * @param string $callBackUrl    服务器回调
     * @param string $ip             IP
     * @param string $bankCardNo     银行卡号
     * @param string $bindMobile     预留手机号
     * @param string $productName    商品名称
     * @param string $repayPlanNo    还款计划编号
     * @param string $repayPlanStage   还款计划期数

     */
    //todo 绑卡/首次支付
    public function bindOrPay(){

        $url="bindOrPay.action";
        $user_id=$_GET['user_id']; //用户id
        $limit=$_GET['limit'];  //额度
        $bankname=$_GET['bankname'];  //银行卡名称
        $bindMobile=$_GET['bindMobile'];  //手机号
        $bankCardNo=$_GET['bankCardNo']; //银行卡

        $tb_register=M("znhk_register");
        $register['user_id']=$user_id;
        $list= $tb_register->where($register)->find();

      //  $list=$tb_register->where($register)->field();

        $name=$list['user_name'];  //姓名
        $idcard=$list['user_idcard'];   //身份证

        $customerNumber=$list['user_customerNumber'];
        $rand= str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
        $requestId =  substr($rand, 0, 6);     //注册请求号  每次请求唯一
        $ip=$_SERVER["SERVER_ADDR"];
        $amount='0';


        $callbackUrl='http://qp.webziti.com/mobile/znhk/xybank';

        $data=array(
            'amount'=>(string)$amount,
            'bankCardNo'=>$bankCardNo,
            'bindMobile'=>$bindMobile,
            'callbackUrl'=>$callbackUrl,
            'customerNumber'=>$customerNumber,
            'mainCustomerNumber'=>$this->config()['mainCustomerNumber'],
            'ip'=>$ip,
            'requestId'=>$requestId,
        );

        $result= $this->yeepayData($url,$data);

        if ($result['code'] == '0000'){

            $tb_xybank=M("znhk_xybank");
            $xybank['user_id']=$user_id;
            $xybank['phone']=$bindMobile;
            $xybank['xybank']=$bankCardNo;
            $xybank['limit']=$limit;
            $xybank['name']=$name;
            $xybank['idcard']=$idcard;
            $xybank['bankname']=$bankname;
            $xybank['requestId']=$requestId;
            $list=$tb_xybank->add($xybank);
            if ($list){
                $this->jsonout('success',$result['message'],$result);
            }
        }else{
            $this->jsonout('faild',$result['message'],$result);
        }

    }



    //todo  绑定信用卡回调
    public function xybank(){


       $code=$_POST['code'];
       $message=$_POST['message'];
       $requestId=$_POST['requestId'];
       $customerNumber=$_POST['customerNumber'];
       $bankCardNo=$_POST['bankCardNo'];
       $bindMobile =$_POST['bindMobile'];
       $bindStatus=$_POST['bindStatus'];
       $hmac=$_POST['hmac'];

        $data=array(
           'code'=>$code,
            'message'=>$message,
            'mainCustomerNumber'=>$this->config()['mainCustomerNumber'],
            'customerNumber'=>$customerNumber,
            'requestId'=>$requestId,
            'bankCardNo'=>$bankCardNo,
            'bindMobile'=>$bindMobile,
            'bindStatus'=>$bindStatus
        );

        if($hmac!=$this->buld_sign($this->buld_pram($data))){
            $info="签名检验失败";
            //开始业务处理
        }else{
            $info="SUCCESS";
            $tb_xybank=M("znhk_xybank");
            $xybank['status']=1;
            $tb_xybank->where("requestId=".$requestId)->save($xybank);
        }
        return $info;
    }


    /*
 * 创建任务列表
 *
 * @param int $money 总金额  必须为正整数
 *
 * return array  任务列表
 */
  private  function creatTaskList($money,$over){
        $times=(int)($money/$over);
        $verage=number_format($money/$times,2) ;
        $starttime=date('Y-m-d', strtotime(date('Y-m-d',time())." +1 day"));
        if($times<2){
            $tem['datetime']=strtotime($starttime." 08:00:00")+rand(1,7200);
            $tem['money']=$money;
            $result[]=$tem;
            return $result;
        }
        for($i=1;$i<=$times;$i++){

            switch ($i%4){
                case 1:
                    $tem['datetime']=strtotime($starttime." 08:00:00")+rand(1,7200);
                    $tem['money']=$verage-20+rand(1,40);
                    $result[]=$tem;
                    break;
                case 2:
                    $tem['datetime']=strtotime($starttime." 10:00:00")+rand(1,7200);
                    $tem['money']=$verage-20+rand(1,40);;
                    $result[]=$tem;
                    break;
                case 3:
                    $tem['datetime']=strtotime($starttime." 14:00:00")+rand(1,7200);
                    $tem['money']=$verage-20+rand(1,40);;
                    $result[]=$tem;
                    break;
                case 0:
                    $tem['datetime']=strtotime($starttime." 16:00:00")+rand(1,7200);
                    $tem['money']=$verage-20+rand(1,40);;
                    $result[]=$tem;
                    $starttime=date('Y-m-d', strtotime($starttime." +1 day"));
                    break;
            }
        }
        $all=0;
        for($j=0;$j<$times-1;$j++){
            $all=$all+$result[$j]['money'];
        }
        $result[$times-1]['money']=$money-$all;
        return $result;
    }

    public function bank(){

        $code=date("Y-m-d H:i:s");;
        $myfile = fopen("a222.txt", "a+") ;
        // $txt = "";
        fwrite($myfile, $code);

        fclose($myfile);
    }


    /**
     * 二次支付
     * @param string $customerNumber 子商户编号
     * @param number $amount         金额
     * @param string $requestId      收款订单号
     * @param string $source         支付方式  B - 店主收款
     * @param string $mcc            商品分类     5311：百货商店       4511：航空公司      4733：大型景区售票
     * @param string $callBackUrl    服务器回调
     * @param string $ip             IP
     * @param string $cardLastNo     银行卡号
     * @param string $productName    商品名称
     * @param string $repayPlanNo    还款计划编号
     * @param string $repayPlanStage   还款计划期数

     */
    //todo 二次支付
    public function secondPay(){
        $customerNumber='10024691472';
        $ip=$_SERVER["SERVER_ADDR"];
        $amount='500';
        $mcc='5311';
        $callBackUrl='http://qp.webziti.com/mobile/znhk/bank';
        $src='B';
        $rand= str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
        $requestId =  substr($rand, 0, 6);
        $cardLastNo='6226890160452974';
        $productName='便利收';
        $rand= str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
        $repayPlanNo =  substr($rand, 0, 6);
        $repayPlanStage='6';

        $url="orderSecondPayApi.action";
        $data=array(
            'mainCustomerNumber'=>$this->config()['mainCustomerNumber'],
            'customerNumber'=>$customerNumber,
            'requestId'=>$requestId,
            'amount'=>(string)$amount,
            'ip'=>$ip,
            'mcc'=>$mcc,
            'src'=> $src,
            'cardLastNo'=>$cardLastNo,
            'callBackUrl'=>$callBackUrl,
            'productName'=>$productName,
            'repayPlanNo'=>$repayPlanNo,
            'repayPlanStage'=> $repayPlanStage,

        );

        $result=$this->yeepayData($url,$data);
        print_r($result);

    }


    /**
     * 结算接口
     * @param string $customerNumber 子商户编号
     * @param number $amount 出款金额
     * @param string $externalNo 结算请求号
     * @param string $transferWay 结算方式： 1：T0 自助结算    2：T1 自助结算
     * @return array
     */
    //todo 结算接口
    public function Draw(){

        $customerNumber='10024691472';
        $rand= str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
        $externalNo =  substr($rand, 0, 6);
        $transferWay='1';
        $amount='500';
        $callBackUrl='http://qp.webziti.com/mobile/znhk/bank';
        $bankAccountNum='6226890160452974';
        $salesProduct='SKBDHT';
        $url="withDrawByCardApi.action";

        $data=array(
            'amount' => (string)$amount,
            'bankAccountNum'=>$bankAccountNum,
            'customerNumber'=>(string)$customerNumber,
            'externalNo'=>$externalNo,
            'mainCustomerNumber'=>$this->config()['mainCustomerNumber'],
            'transferWay'=>(string)$transferWay,
            'salesProduct'=>$salesProduct,
            'callBackUrl'=>$callBackUrl
        );


        $result=$this->yeepayData($url,$data);

        $hmaclocal=$this->buld_sign(
            $this->buld_pram(
                array(
                    'amount'=>$result['amount'],
                    'code'=> $result['code'],
                    'customerNumber'=> $result['customerNumber'],
                    'externalNo'=> $result['externalNo'],
                    'mainCustomerNumber'=> $this->config()['mainCustomerNumber'],
                    'message'=> $result['message'],
                    'serialNo'=> $result['serialNo'],
                    'transferWay'=> $result['transferWay'],
                )
            ));
        //	echo  $hmaclocal;

        if($result['hmac']!=$hmaclocal )
        {
            $return['code']="9998";
            $return['message']="服务端签名检验错误";
            return  $return;
        }

    }


    //----------------------------------异步通知----------------------------------------------


    /**
     * 结算异步通知--此处只做验签
     * @return string  SUCCESS
     */
    //todo 结算异步通知--此处只做验签
    public function drawNotice($mainCustomerNumber,$customerNumber,$externalNo,$serialNo,$transferStatus,$requestTime,$handleTime,$transferWay,$receiver,$receiverBankCardNo,$receiverBank,$amount,$fee,$basicFee,$exTargetFee,$actualAmount,$failReason){
        $response=array();
        $data=array(
            'mainCustomerNumber'=>$this->config()['mainCustomerNumber'],
            'customerNumber'=>$customerNumber, #子商户编码
            'externalNo'=>$externalNo, #大商户的出款订单号
            'serialNo'=>$serialNo, #易宝唯一的交易流水号
            'transferStatus'=>$transferStatus, #出款状态  已接受 RECEIVED, 处理中 PROCESSING, 打款成功 SUCCESSED, 打款失败 FAILED, 已退款 REFUNED, 已撤销 CANCELLED
            'requestTime'=>$requestTime, #请求时间 时间格式:yyyy-mm-dd hh:mm:ss
            'handleTime'=>$handleTime, #处理时间 时间格式:yyyy-mm-dd hh:mm:ss
            'transferWay'=>(string)$transferWay, #出款类型 1| T+0, 2 | T+1 ,3 |自动T+1结算
            'receiver'=>$receiver, #收款人
            'receiverBankCardNo'=>$receiverBankCardNo, #收款卡号
            'receiverBank'=>$receiverBank, #收款银行
            'amount'=>(string)$amount, #金额
            'fee'=>$fee, #手续费
            'basicFee'=>(string)$basicFee, #基本手续费
            'exTargetFee'=>(string)$exTargetFee, #额外手续费
            'actualAmount'=>(string)$actualAmount, #实收金额
            'failReason'=>$failReason, #失败原因
        );
        $info="SUCCESS";
        if($response['hmac']!=$this->buld_sign($this->buld_pram($data))){
            $info="签名检验失败";
            //开始业务处理
        }

        return $info;
    }

    /**
     * 收款回调处理--此处只做验签
     * @return string SUCCESS|成功
     */
    //todo 收款回调处理 --此处只做验签
    public function tradeNotice($code,$message,$requestId,$customerNumber,$externalld,$createTime,$payTime,$amount,$fee,$status,$busiType,$bankCode,$payerName,$payerPhone,$lastNo,$src,$list,$hmac){
        //$response=array();
        $data=array(
            'code'=> $code, #支付返回码
            'message'=> $message, #支付消息码具体信息
            'requestId'=> $requestId, #大商户请求的订单号
            'customerNumber'=>$customerNumber, #子商户编码
            'externalld'=>$externalld, #易宝交易流水号
            'createTime'=> $createTime, #请求时间
            'payTime'=>$payTime, #支付时间
            'amount'=>$amount, #订单金额
            'fee'=>$fee, #扣取的大商户手续费
            'status'=>$status, #订单状态：  FAIL|失败、未支付 SUCCESS|成功 FORZEN|冻结 THAWED|解冻
            'busiType'=>$busiType,  #业务类型：COMMON 普通交易 ASSURE 担保交易
            'bankCode'=>$bankCode, #银行编码
            'payerName'=>$payerName, #持卡人姓名
            'payerPhone'=>$payerPhone, #持卡人手机号
            'lastNo'=>$lastNo, #银行卡后四位
            'src'=>$src, #D - 卡号收款 B - 店主收款 S - 短信收款 T - 二维码收款 W - 微信支付
        );


        $info="SUCCESS";
        $hmaclocal=$this->buld_sign($this->buld_pram($data));
        //echo $hmaclocal;
        if($hmac !=$hmaclocal){
            $info="签名检验失败";
            //开始业务处理
        }
        return $info;
    }

    /**
     * 获取易宝数据
     * @param string $url
     * @param array $data
     */
    private function yeepayData($url="",$data=array()){


        if(!isset($data['hmac']));
        $data['hmac']= $this->buld_sign($this->buld_pram($data));
        $result=$this->exec($this->config()['yeepayUrl'].$url,$data);

        return   $result=json_decode($result,true);

    }
    private function yeepayData1($url="",$data=array()){


        if(!isset($data['hmac']));
        $data['hmac']= $this->buld_sign($this->buld_pram($data));
        $data['auditStatus']='SUCCESS';
        $result=$this->exec($this->config()['yeepayUrl'].$url,$data);

        return   $result=json_decode($result,true);

    }

    /**
     * 参数处理
     * @param array $data
     */
    private function buld_pram($data=array(),$method=""){



        $str='';
        if(count($data)<1) return $str;
        foreach($data as $key=>$val){
            if(gettype($val)!='string') continue;
            if($method!=''){
                if($str!='') $str.="&";
                $str.="$key=$val";
            }else{
                $str.=$val;
            }

        }
        // echo  $str;

        return $str;
    }

    /**
     * 生成签名
     * @param string $str
     */
    private function buld_sign($str=''){


         $str=$this->HmacMd5($str,$this->config()['md5Key']);

        return $str;
    }

    /**
     * HmacMd5计算
     * @param string $data
     * @param string $key
     */
    private  function HmacMd5($data="",$key="")
    {

        $b = 64; // byte length for md5
        if (strlen($key) > $b) {
            $key = pack("H*",md5($key));
        }
        $key = str_pad($key, $b, chr(0x00));
        $ipad = str_pad('', $b, chr(0x36));
        $opad = str_pad('', $b, chr(0x5c));
        $k_ipad = $key ^ $ipad ;
        $k_opad = $key ^ $opad;

        return md5($k_opad . pack("H*",md5($k_ipad . $data)));
    }

    /**
     * 加密
     * @param string $input 明文
     */
    private  function encrypt($input="") {

        $key=$this->config()['md5Key'];
        if(strlen($key)>16) $key=substr($key,0,16);
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $input = $this->pkcs5_pad($input, $size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td,$input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = $this->strToHex($data);
        return $data;
    }

    private  function pkcs5_pad ($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    /**
     * 字符串转16进制
     * @param string $string
     * @return string
     */
    function strToHex($string="")//字符串转十六进制
    {
        $hex="";
        for($i=0;$i<strlen($string);$i++)
            $hex.=dechex(ord($string[$i]));
        $hex=strtoupper($hex);
        return $hex;
    }

    /**
     * 十六进行转化成字符串
     * @param string $hex
     * @return string
     */
    function hexToStr($hex="")//十六进制转字符串
    {
        $string="";
        for($i=0;$i<strlen($hex)-1;$i+=2)
            $string.=chr(hexdec($hex[$i].$hex[$i+1]));
        return  $string;
    }

    /**
     * 解密
     * @param string $sStr 密文
     */
    private  function decrypt($sStr="") {
        $sKey=$this->config()['md5Key'];
        if(strlen($sKey)>16) $sKey=substr($sKey,0,16);
        $decrypted= mcrypt_decrypt(
            MCRYPT_RIJNDAEL_128,
            $sKey,
            $this->hexToStr($sStr),
            MCRYPT_MODE_ECB
        );

        $dec_s = strlen($decrypted);
        $padding = ord($decrypted[$dec_s-1]);
        $decrypted = substr($decrypted, 0, -$padding);
        // die(urldecode($decrypted));
        urldecode($decrypted);
        return $decrypted;
    }

    /**
     * 无警告信息获取用户提交的值
     * @param	string	$key		参数名：数字、字母、下划线
     * @param	string	$method		all,post与get
     * @param	string	$default	如为空的默认值
     * @return	string
     */
    function g($key="", $method='all',$default='') {
        if ($key == '') {
            return $default;
        }

        switch (strtolower($method)) {
            case 'post':$url = $_POST;
                break;
            case 'get':$url = $_GET;
                break;
            default:$url = $_REQUEST;
                break;
        }
        return (isset($url[$key])) ? $url[$key] : $default;
    }

    function exec($url,$post) {


        $ch = curl_init();
        //echo $this->url;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        // curl_setopt($ch, CURLOPT_SAFE_UPLOAD, 1);
        if (class_exists('\CURLFile')) {
            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
        } else {
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
            }
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        if( ! $result = curl_exec($ch))
        {
            trigger_error(curl_error($ch));
        }
        curl_close($ch);
        //  echo $result;

        return $result;
    }





}