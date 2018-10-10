<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/5
 * Time: 9:54
 */
error_reporting(E_ALL);
header('Content-Type:text/html;Charset=utf-8;');
require LIB_PATH . 'Net/Curlobject.class.php';
require_once LIB_PATH.'Net/grafika-master/src/autoloader.php';
use Grafika\Grafika;
use Grafika\Color;

class YeepaySkbSdkAction extends CommonAction {
    /**
     * 子商户注册接口--->1
     */
    public function reg() {

        $ID = $_GET['ID'];
        $tb_yibao_wuka = M('yibao_wuka');
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
            'mainCustomerNumber' => $this -> config['parentId'],
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
        $res = json_decode($res, true);
        //判断易宝的返回值状态
        if ($res['code'] != '0000') {
            $this -> jsonout('faild', $res['message'], null);
        } else {
            $xxx = M('xxx');
            $xxx -> add($sss);
            $customerNumber = $res['customerNumber'];

            $type1 = 1;
            $rate1 = 0.0055;
            $type2 = 3;
            $rate2 = 2;
            $type3 = 4;
            $rate3 = 0;
            $type4 = 5;
            $rate4 = 0;

            $user_State['user_State'] = 6;
            $user_State['user_customerNumber'] =$customerNumber;
            $tb_yibao_wuka -> where('user_id=' . $ID) -> save($user_State);

            //这个是设置费率的
            $this->fee($customerNumber,$type1,$rate1);
            //这个是设置单笔的
            $this->fee($customerNumber,$type2,$rate2);
            //这个是设置T0 自助结算工作日额外费率
            $this->fee($customerNumber,$type3,$rate3);
            //这个是设置T0 自助结算非工作日额外费率
            $this->fee($customerNumber,$type4,$rate4);
            $this -> jsonout('success', $user_State['user_State'], null);
        }
    }

    /*
    寻找储蓄卡
    */

    public function findBank(){
        $ID = $_GET['ID'];
        $tb_yibao_wuka = M('yibao_wuka');
        $Modeldata = $tb_yibao_wuka -> where('user_id=' . $ID) -> find();
        $data['bankname'] = $Modeldata['user_bankname'];
        $data['banknum'] = $Modeldata['user_banknum'];
        $this -> jsonout('success', '储蓄卡查询成功!', $data);
    }
    /*
    绑定信用卡
    */

    public function FindXinyong(){
        $xyid = $_GET['xyid'];
        $money = $_GET['money'];
        $ID = $_GET['ID'];
        $mc = "5311";
        $randNum = rand_string(20);

        $jilu['uid'] = $ID;
        $jilu['deal_ordernumber'] = $randNum;
        $jilu['deal_status'] = 1;
        $tb_yyy = M('yyy');
        $tb_yyy -> add($jilu);

        $xxx = M('xxx');
        // 实例化xxx对象
        $Modeldata = $xxx -> where('id=' . $xyid) -> find();
        $banknum = $Modeldata['banknum'];


        //实例化wuka对象
        $tb_yibao_wuka = M('yibao_wuka');
        // 实例化User对象
        $Modeldata1 = $tb_yibao_wuka -> where('user_id=' . $ID) -> find();
        $user_customerNumber = $Modeldata1['user_customerNumber'];

        $res = $this->trade($user_customerNumber,$money,$randNum,$banknum,$mc);
        $res=json_decode($res,true);
        //$this -> jsonout('success', '支付接口!', $res);
        $url = $res['url'];

        $URL = $this->decrypt($url);
        $urldata['url'] = $URL;
        $this -> jsonout('success', '支付地址获取成功!', $urldata);
    }
    /*
     * 数据节流
     */


    public function SaveData() {
        $ID = $_GET['ID'];
        //$randpwd = $_GET['randpwd'];
        $bk_info_name = $_GET['bk_info_name'];
        $bk_info_idCard = $_GET['bk_info_idCard'];
        $bk_info_bankNum = $_GET['bk_info_bankNum'];
        $bk_info_bankName = $_GET['bk_info_bankName'];
        $bk_info_phoneNum = $_GET['bk_info_phoneNum'];
        //数据插入返佣表
        $tb_yibao_wuka = M('yibao_wuka');
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
                $this -> jsonout('success', '实名认证成功!', null);
            } else {
                $this -> jsonout('faild', '实名认证失败!', null);
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
                $this -> jsonout('success', '实名认证成功!', null);
            } else {
                $this -> jsonout('faild', '实名认证失败!', null);
            }
        }
    }
    //保存图片路径

    public function SaveImg($str, $type, $id) {
        if ($type == 'idcard_face') {
            $tb_yibao_wuka = M('yibao_wuka');
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
            $tb_yibao_wuka = M('yibao_wuka');
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
            $tb_yibao_wuka = M('yibao_wuka');
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
            $tb_yibao_wuka = M('yibao_wuka');
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

    protected $config = array(
        // 商户需要修改成生产地址
        // 内测地址 http://211.151.82.148:8081/skb-app
        // 生产地址 https://skb.yeepay.com/skb-app/
        'yeepayUrl' => 'https://skb.yeepay.com/skb-app/', #易宝服务端站点
        'md5Key' => '3010V219KNBZZu7RN6D4u22Te7v60fK2wn14A3R97j24H007V994184A6101', #Md5密钥
        'parentId' => '10024394576', #易宝大商号户
        'tradeNotifyUrl' => "http://qp.webziti.com/mobile/YeepaySkbSdk/yeepaynotify", #收款回调页面地址
        'tradeReturnUrl' => "http://www.sundayltd.com/skb/return.php", #收款跳转地址
        'drawNoticeUrl' => "http://www.sundayltd.com/skb/drawnotice.php", #出款回调页面地址
    );

    /*
     * 银行卡照片上传
     * */
    public function UploadPhotosBank() {
        $ID = $_POST['ID'];
        $randpwd = $_POST['randpwd'];
        $type = $_POST['type'];
        $d = BASE_PATH;
        if(!empty($ID)){
            $tb_yibao_wuka = M('yibao_wuka');
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

        $dir = __ROOT__ . 'upload';
        //按照年月日创建目录
        $file_path = "$dir" . '/' . date("Y") . '/' . date("m") . '/' . date("d") . '/';
        if (!is_dir($file_path)) {
            mkdir($file_path, 0777, true);
        }
       // if ($_FILES["img"]["size"] < 5120000) {
            if (move_uploaded_file($tmp, $file_path . $imgname)) {

                //保存路径
                $lujing = $file_path . $imgname;
                $editor = Grafika::createEditor();
                $editor->open($image1 , './'.$lujing); // 打开yanying.jpg并且存放到$image1
                $editor->resizeExactWidth($image1 , 600);  //按比例缩小图片
                $editor->save($image1 , $lujing);

                $this -> SaveImg($lujing, $type, $ID);
                $this -> jsonout('success', '银行卡上传成功', null);
            } else {
                $this -> jsonout('faild', '银行卡上传失败', null);
            }
      //  } else {
          //  $this -> jsonout('faild', '照片大小超出最大值', null);
       // }
    }

    /*
     * 判断是否注册成功
     * */
    public function findUser() {
        $id = $_GET['ID'];
        $tb_yibao_wuka = M('yibao_wuka');
        // 实例化User对象
        $Modeldata = $tb_yibao_wuka -> where('user_id=' . $id) -> find();
        if(empty($Modeldata)){
            $this -> jsonout('faild', '未注册!', null);
        }else{
            $xinxi['name']=$Modeldata['user_name'];
            $xinxi['idcard']=$Modeldata['user_idcard'];
            $xinxi['phone']=$Modeldata['user_phone'];
            $xinxi['banknum']=$Modeldata['user_banknum'];
            $xinxi['bankname']=$Modeldata['user_bankname'];

            $xinxi['bankImg']=$Modeldata['user_bankImg'];
            $xinxi['IDcardFront']=$Modeldata['user_IDcardFront'];
            $xinxi['IDcardBack']=$Modeldata['user_IDcardBack'];
            $xinxi['AllImg']=$Modeldata['user_AllImg'];

            $this -> jsonout('success', $Modeldata['user_State'], $xinxi);
        }
    }

    /*
     * 子商户信息查询接口--->2
     * @param string $mobilePhone 子商户手机号码
     */
    public function info($mobilePhone='') {
        $url = "customerInforQuery.action";
        $data = array('mainCustomerNumber' => $this -> config['parentId'], 'mobilePhone' => $mobilePhone);
        return $this -> yeepayData($url, $data);
    }

    /**
     * 子商户费率设置接口--->3
     * @param string $customerNumber
     * @param number $type
     * @param number $rate
     */
    public function fee($customerNumber = '', $type,$rate) {
        $url = "feeSetApi.action";
        $data = array('customerNumber' => $customerNumber, #子商户编号
            'mainCustomerNumber' => $this -> config['parentId'], 'productType' => (string)$type, #整数类型, 1.交易 2.提现 3.日结通基 本 4.日结通额外 5.日结通非工作日 6.微信
            'rate' => (string)$rate, );

        return $this -> yeepayData($url, $data);
    }

    /*
     * 子商户费率查询接口--->4
     * @param string $customerNumber
     * @param number $type
     */
    public function queryFee($customerNumber = '', $type = 1) {
        $url = "queryFeeSetApi.action";
        $data = array('customerNumber' => $customerNumber, #子商户编号
            'mainCustomerNumber' => $this -> config['parentId'], 'productType' => (string)$type, #整数类型, 1.交易 2.提现 3.日结通基 本 4.日结通额外 5.日结通非工作日 6.微信
        );
        return $this -> yeepayData($url, $data);
    }

    /**
     * 收款接口（仅店主收款）--->5
     * @param string $customerNumber
     * @param number $amount
     * @param string $tradeNo
     * @param string $payBankNo
     * @param string $mcc
     */
    public function trade($customerNumber, $amount, $tradeNo, $payBankNo, $mcc='5311') {

        $url = "receiveApi.action";
        $data = array('source' => 'B', #支付方式 D - 卡号收款B - 店主收款S - 短信收款T - 二维码收款 W - 微信支付
            'mainCustomerNumber' => $this -> config['parentId'], 'customerNumber' => $customerNumber,
            'amount' => (string)$amount,
            'mcc' => $mcc,
            'requestId' => $tradeNo,
            'callBackUrl' => $this -> config['tradeNotifyUrl'], #回调页面地址
            'webCallBackUrl' => $this -> config['tradeReturnUrl'], #支付后跳转地址
        );
        if ($payBankNo != '')
            $data['payerBankAccountNo'] = $payBankNo;
        #支付银行卡号
        $result = $this -> yeepayData2($url, $data);
        if ($result['code'] == '0000') {
            $result['url'] = $this -> decrypt($result['url']);
        }
        return $result;
    }

    /*
     * 交易查询接口--->6
     */
    public function transactionQuery($customerNumber) {
        //$customerNumber = '10025087387';
        $ID = $_GET['ID'];
        $tb_yibao_wuka = M('yibao_wuka');
        // 实例化User对象
        $Modeldata = $tb_yibao_wuka -> where('user_id=' . $ID) -> find();
        $customerNumber = $Modeldata['customerNumber'];

        $url = "tradeReviceQuery.action";
        $data = array('mainCustomerNumber' => $this -> config['parentId'],
            'customerNumber' => $customerNumber,
           // 'requestId' => '',
            'createTimeBegin' => '2018-09-01 00:00:00',
            'createTimeEnd' => '2018-09-13 23:59:59',
            //'payTimeBegin' => '',
           // 'payTimeEnd' => '',
            //'lastUpdateTimeBegin' => '',
           // 'lastUpdateTimeEnd' => '',
            'pageNo' => '1', );
        $result = $this -> yeepayData($url, $data);
        var_dump($result);
        return $result;
    }

    /**
     * 结算接口--->7
     * @param string $customerNumber 子商户编号
     * @param number $amount 出款金额
     * @param string $externalNo 出款流水号
     * @param string $transferWay 出款方式:1|日结通,2|委托结算
     * @return array
     */
    public function Draw() {
        $customerNumber = '10025005574';
        $amount = 500.48;
        $externalNo = "okokokk1";
        $transferWay = '1';

        $url = "withDrawApi.action";

        $data = array('amount' => (string)$amount, 'customerNumber' => (string)$customerNumber, 'externalNo' => $externalNo, 'mainCustomerNumber' => $this -> config['parentId'], 'transferWay' => (string)$transferWay, 'callBackUrl' => $this -> config['drawNoticeUrl'], );

        $return = array('code' => '0000', 'message' => '', );

        $result = $this -> yeepayData($url, $data);
        //test($result);
        if ((string)$result['code'] != '')
            return $result;
        if ($result['hmac'] != $this -> buld_sign($this -> buld_pram(array('amount' => $result['amount'], 'code' => $result['code'], 'customerNumber' => $result['customerNumber'], 'externalNo' => $result['externalNo'], 'mainCustomerNumber' => $this -> config['parentId'], 'message' => $result['message'], 'serialNo' => $result['serialNo'], 'transferWay' => (string)$result['transferWay'], )))) {
            $return['code'] = "9998";
            $return['msg'] = "服务端签名检验错误";
        }
        echo ('123lk');
        var_dump($result);
        return $result;
    }

    /**
     * 结算记录查询接口--->8
     */
    public function settlementRecordQuery($customerNumber = '') {
        $url = "transferQuery.action";
        $data = array('customerNumber' => $customerNumber,
            'externalNo' => '',
            'mainCustomerNumber' => $this -> config['parentId'],
            'pageNo' => '',
            'requestDateSectionBegin' => '',
            'requestDateSectionEnd' => '',
            'serialNo' => '',
            'transferStatus' => '',
            'transferWay' => '', );
        $result = $this -> yeepayData($url, $data);
        return $result;
    }

    /*
     * 可用余额查询接口--->9
     * @param string $customernumber 子商户编号
     * @param string $type 1|日结通可用余额, 2|提现可用余额,3|账户余额
     */
    public function userBalance($customernumber = '', $type = 1) {

        $url = "customerBalanceQuery.action";
        $data = array('mainCustomerNumber' => $this -> config['parentId'], 'customerNumber' => $customernumber, 'balanceType' => (string)$type, );

        $return = array('code' => '0000', 'message' => '', );

        $result = $this -> yeepayData($url, $data);
        test($result);
        if ((string)$result['code'] != '')
            return $result;
        if ($result['hmac'] != $this -> buld_sign($this -> buld_pram(array('code' => $result['code'], 'message' => $result['message'], 'mainCustomerNumber' => $this -> config['parentId'], 'customerNumber' => $result['customerNumber'], 'balanceType' => $result['balanceType'], 'balance' => $result['balance'], )))) {
            $return['code'] = "9998";
            $return['msg'] = "服务端签名检验错误";
        }
        return $result;
    }

    /**
     * 系统商 返佣转账接口--->10
     */
    public function systemRemission($customerNumber = '') {
        $url = "transferQuery.action";
        $data = array('mainCustomerNumber' => '', 'customerNumber' => $customerNumber, 'requestId' => '', 'transAmount' => '', 'remark' => '', );
        $result = $this -> yeepayData($url, $data);
        return $result;
    }

    /**
     * 生成查看子商户资质的URL
     * @param string $customerNumber
     * @param string $mobilePhone
     * @return string
     */
    public function queryUserPhoto($customerNumber = "", $mobilePhone = '') {
        $url = "queryCustomerQualification.action";

        $data = array('mainCustomerNumber' => $this -> config['parentId'], );

        if ($customerNumber != '')
            $data['customerNumber'] = $customerNumber;
        if ($mobilePhone != '')
            $data['mobilePhone'] = $mobilePhone;
        $data['hmac'] = $this -> buld_sign($this -> buld_pram($data));

        return $this -> config['yeepayUrl'] . $url . "?" . $this -> buld_pram($data, "key");
    }

    /**
     * 补充子商户资质
     * @param string $customerNumber 子商户编号
     * @param string $bankCardPhoto 银行卡正面照
     * @param string $busiNessLicenSePhoto 个体工商证正面照
     * @param string $idCardPhoto  身份证正面照
     * @param string $idCardBackPhoto 身份证背面照
     * @param string $personPhoto 本人、身份证、银行照
     *-----------------------------------几个照片至少传一张
     */
    public function userPhoto($customerNumber = '', $bankCardPhoto = '', $busiNessLicenSePhoto = '', $idCardPhoto = '', $idCardBackPhoto = '', $personPhoto = '') {

        $url = "customerPictureUpdate.action";
        $data = array('mainCustomerNumber' => $this -> config['parentId'], 'customerNumber' => $customerNumber, );

        $data['hmac'] = $this -> buld_sign($this -> buld_pram($data));

        $photos = array('bankCardPhoto' => $bankCardPhoto, 'BusinessLicensePhoto' => $busiNessLicenSePhoto, 'idCardPhoto' => $idCardPhoto, 'idCardBackPhoto' => $idCardBackPhoto, 'personPhoto' => $personPhoto, );

        foreach ($photos as $key => $val) {
            if ($bankCardPhoto != '' && is_file($val)) {
                $data[$key] = new CURLFile($val, 'image/jpeg', "photo.jpg");
            }
        }

        $return = array('code' => '0000', 'message' => '', );

        $result = $this -> yeepayData($url, $data);
        test($result);
        if ((string)$result['code'] != '')
            return $result;
        if ($result['hmac'] != $this -> buld_sign($this -> buld_pram(array('code' => $result['code'], 'message' => $result['message'], 'status' => $result['status'])))) {
            $return['code'] = "9998";
            $return['msg'] = "服务端签名检验错误";
        }
        return $result;
    }

    /**
     *
     * @param string $customerNumber 子商户编号
     * @param string $target  登封成功后跳转的页面:
     *              all或默认|链至【掌上收银台】,有底部导航栏,
     *              handCaher|链至【掌上收银台】,没有底部导航栏,
     *              myAccount:链至【我的账户】,没有底 部导航栏
     * @return string 子商户的URL地址
     */
    public function login($customerNumber = '', $target = "all") {

        $url = $this -> config['yeepayUrl'] . "loginFromApp.action?";
        $data = array("mainCustomerNumber" => $this -> config['parentId'], 'customerNumber' => $customerNumber, );

        $data['hmac'] = $this -> buld_sign($this -> buld_pram($data));
        $data['target'] = $target;
        return $url . $this -> buld_pram($data, "key");
    }

    /**
     * 提现异步通知
     * @return string
     */
    public function drawNotice() {
        $data = array('mainCustomerNumber' => $this -> config['parentId'],
            'customerNumber' => $this -> g('customerNumber'), #子商户编码
            'externalNo' => $this -> g('externalNo'), #大商户的出款订单号
            'serialNo' => $this -> g('externalNo'), #易宝唯一的交易流水号
            'transferStatus' => $this -> g('transferStatus'), #出款状态  已接受 RECEIVED, 处理中 PROCESSING, 打款成功 SUCCESSED, 打款失败 FAILED, 已退款 REFUNED, 已撤销 CANCELLED
            'requestTime' => $this -> g('requestTime'), #请求时间 时间格式:yyyy-mm-dd hh:mm:ss
            'handleTime' => $this -> g('handleTime'), #处理时间 时间格式:yyyy-mm-dd hh:mm:ss
            'transferWay' => $this -> g('transferWay'), #出款类型 1| T+0, 2 | T+1 ,3 |自动T+1结算
            'receiver' => $this -> g('receiver'), #收款人
            'receiverBankCardNo' => $this -> g('receiverBankCardNo'), #收款卡号
            'receiverBank' => $this -> g('receiverBank'), #收款银行
            'amount' => $this -> g('amount'), #金额
            'fee' => $this -> g('fee'), #手续费
            'basicFee' => $this -> g('basicFee'), #基本手续费
            'exTargetFee' => $this - g > ('exTargetFee'), #额外手续费
            'actualAmount' => $this -> g('actualAmount'), #实收金额
            'failReason' => $this -> g('failReason'), #失败原因
        );
        $info = "SUCCESS";
        if ($this -> g("hmac") == $this -> buld_sign($this -> buld_pram($data))) {
            $info = "签名检验失败";
            //开始业务处理
        }
        var_dump($info);
        return $info;
    }

    /**
     * 收款回调处理
     * @return string SUCCESS|成功，其它|失败，直接返给易宝即可
     */
    public function tradeNotice() {

        $data = array('code' => $this -> g("code"), #支付返回码
            'message' => $this -> g('message'), #支付消息码具体信息
            'requestId' => $this -> g('requestId'), #大商户请求的订单号
            'customerNumber' => $this -> g('customerNumber'), #子商户编码
            'externalld' => $this -> g('externalld'), #易宝交易流水号
            'createTime' => $this -> g('createTime'), #请求时间
            'payTime' => $this -> g('payTime'), #支付时间
            'amount' => $this -> g('amount'), #订单金额
            'fee' => $this -> g('fee'), #扣取的大商户手续费
            'status' => $this -> g('status'), #订单状态：  FAIL|失败、未支付 SUCCESS|成功 FORZEN|冻结 THAWED|解冻
            'busiType' => $this -> g('busiType'), #业务类型：COMMON 普通交易 ASSURE 担保交易
            'bankCode' => $this -> g('bankCode'), #银行编码
            'payerName' => $this -> g('payerName'), #持卡人姓名
            'payerPhone' => $this -> g('payerPhone'), #持卡人手机号
            'lastNo' => $this -> g('lastNo'), #银行卡后四位
            'src' => $this -> g('src'), #D - 卡号收款 B - 店主收款 S - 短信收款 T - 二维码收款 W - 微信支付
        );

        $info = "SUCCESS";
        if ($this -> g("hmac") == $this -> buld_sign($this -> buld_pram($data))) {
            $info = "签名检验失败";
            //开始业务处理
        }
        return $info;
    }

    /**
     * 审核用户
     * @param string $customerNumber 子商户编号
     * @param string $status 审核状态 SUCCESS|通过,FAILED|拒绝
     * @param string $reason 如果审核状态为拒绝，此处为拒绝原因
     */
    public function usreAudit($customerNumber = '', $status = "SUCCESS", $reason = '') {
        $url = "auditMerchant.action";
        $data = array('customerNumber' => $customerNumber, 'mainCustomerNumber' => $this -> config['parentId'], 'status' => $status, 'reason' => $reason, );
        if ($reason == '')
            unset($data['reason']);

        return $this -> yeepayData($url, $data);
    }

    /**
     * 订单解冻
     * @param string $customerNumber 子商户编号
     * @param string $tradeNo 交易订单号
     */
    public function thawTrade($customerNumber = "", $tradeNo = "") {
        $url = "thawTrade.action";
        $data = array('customerNumber' => $customerNumber, 'mainCustomerNumber' => $this -> config['parentId'], 'requestId' => $tradeNo, );

        $return = array('code' => '0000', 'message' => '', );

        $result = $this -> yeepayData($url, $data);
        if ((string)$result['code'] != '')
            return $result;
        if ($result['hmac'] != $this -> buld_sign($this -> buld_pram(array('code' => $result['code'], 'requestId' => $result['requestId'], 'amount' => $result['amount'])))) {
            $return['code'] = "9998";
            $return['msg'] = "易宝签名检验错误";
        }
        return $result;

    }

    /**
     * 更改用户结算方式
     * @param string $customerNumber 子商户编号
     * @param string $riskReserveDay 客户结算方式:0|T0结算,1|T1结算
     * @param string $bankName 是否自动提款给客户 Y|需客户手工操作，N|T1自助结算给用户
     */
    public function userRisk($customerNumber = "", $riskReserveDay = "", $manualSettle = "Y") {
        $url = "customerInforUpdate.action";
        $data = array('mainCustomerNumber' => $this -> config['parentId'], 'customerNumber' => $customerNumber, 'riskReserveDay' => (string)$riskReserveDay, 'manualSettle' => (string)$manualSettle, );
        $data['hmac'] = $this -> buld_sign($this -> buld_pram($data));
        $data['modifyType'] = '3';
        return $this -> yeepayData($url, $data);
    }

    /*
     * 冻结用户
     * @param string $customerNumber 子商户编号
     * @param number $type 冻结或解冻 0|冻结,1|解冻
     * @param string $freezeDays 冻结天数，如果为冻结，此处为冻结天数
     */
    public function userFreeze($customerNumber = "", $type = 0, $freezeDays = '') {
        $url = "customerInforUpdate.action";
        $data = array('mainCustomerNumber' => $this -> config['parentId'], 'customerNumber' => $customerNumber, 'whiteList' => (string)$type, 'freezeDays' => (string)$freezeDays, );
        $data['hmac'] = $this -> buld_sign($this -> buld_pram($data));
        $data['modifyType'] = '1';
        return $this -> yeepayData($url, $data);
    }

    /**
     * 更改用户银行卡信息
     * @param string $customerNumber 子商户编号
     * @param string $bankCardNumber 银行卡卡号
     * @param string $bankName 开户行 【工商银行、农业银行、招商银 行、建设银行、交通银行、中信银行、光大银行、北京银行、深圳发展银行 、中国银行、兴业银行、民生银行】
     */
    public function userBank($customerNumber = "", $bankCardNumber = "", $bankName = "") {
        $url = "customerInforUpdate.action";
        $data = array('mainCustomerNumber' => $this -> config['parentId'], 'customerNumber' => $customerNumber, 'bankCardNumber' => (string)$bankCardNumber, 'bankName' => (string)$bankName, );
        $data['hmac'] = $this -> buld_sign($this -> buld_pram($data));
        $data['modifyType'] = '2';
        return $this -> yeepayData($url, $data);
    }

    /**
     * 子商户限额查询接口
     * @param string $customerNumber 子商户编号
     * @param string $bankCardType 银行卡类型:DEBIT 借记卡 CREDIT 信用卡
     * @param number $tradeLimitConfigKey  限额类型： 1店主 2非店主 3子商户 4非店主卡
     * @param string $bankCardNo 银行卡号
     */
    public function queryTradeLimit($customerNumber = '', $bankCardType = "DEBIT", $tradeLimitConfigKey = 1, $bankCardNo = '') {
        $url = "tradeLimitQuery.action";
        $data = array('customerNumber' => (string)$customerNumber, 'mainCustomerNumber' => $this -> config['parentId'], 'bankCardType' => (string)$bankCardType, 'bankCardNo' => $bankCardNo, 'tradeLimitConfigKey' => (string)$tradeLimitConfigKey, );
        if ($data['bankCardNo'] == '')
            unset($data['bankCardNo']);
        return $this -> yeepayData($url, $data);
    }

    /**
     * 子商户限额设置接口
     * @param number $customerNumber 子商户编号
     * @param number $tradeLimitConfigKey  限额类型： 1店主 2非店主 3子商户 4非店主卡
     * @param string $bankCardType 银行卡类型:DEBIT 借记卡 CREDIT 信用卡
     * @param number $singleAmount 单笔限额:正整数,且不大于默认值
     * @param number $dayAmount 日限额:正整数,且不大于默认值
     * @param number $monthAmount 月限额:正整数,且不大于默认值
     * @param number $dayCount 日累计次数:正整数
     * @param number $monthCount 月累计次数
     * @param string $bankCardNo 银行卡号
     */
    public function tradeLimit($customerNumber = 0, $tradeLimitConfigKey = 1, $bankCardType = "DEBIT", $singleAmount = 0, $dayAmount = 0, $monthAmount = 0, $dayCount = 0, $monthCount = 0, $bankCardNo = '') {
        $url = "tradeLimitSet.action";
        $data = array('customerNumber' => (string)$customerNumber, 'mainCustomerNumber' => $this -> config['parentId'], 'bankCardType' => (string)$bankCardType, 'bankCardNo' => $bankCardNo, 'tradeLimitConfigKey' => (string)$tradeLimitConfigKey, 'singleAmount' => (string)$singleAmount, 'dayAmount' => (string)$dayAmount, 'monthAmount' => (string)$monthAmount, 'dayCount' => (string)$dayCount, 'monthCount' => (string)$monthCount, );
        if ($data['bankCardNo'] == '')
            unset($data['bankCardNo']);
        return $this -> yeepayData($url, $data);
    }

    /**
     *
     * 获取易宝数据
     * @param string $url
     * @param array $data
     */

    private function yeepayData($url = "", $data = array()) {
        if (!isset($data['hmac']))
            $data['hmac'] = $this -> buld_sign($this -> buld_pram($data));
        $curl = new curlobject();
        $curl -> url = $this -> config['yeepayUrl'] . $url;
        $curl -> upload = true;
        $curl -> post = $data;
        $result = $curl -> exec();
        //$result = json_decode($result['body'], true);

        return $result;
    }

    private function yeepayData2($url = "", $data = array()) {
        if (!isset($data['hmac']))
            $data['hmac'] = $this -> buld_sign($this -> buld_pram($data));
        $data['autoWithdraw'] = 'true';
        $curl = new curlobject();
        $curl -> url = $this -> config['yeepayUrl'] . $url;
        $curl -> upload = true;
        $curl -> post = $data;
        $result = $curl -> exec();
        //$result = json_decode($result['body'], true);

        return $result;
    }

    private function yeepayData1($url = "", $data = array()) {
        if (!isset($data['hmac']))
            $data['hmac'] = $this -> buld_sign($this -> buld_pram($data));
        $data['auditStatus'] = 'SUCCESS';
        $curl = new Curlobject();
        $curl -> url = $this -> config['yeepayUrl'] . $url;
        $curl -> upload = true;
        $curl -> post = $data;
        $result = $curl -> exec();
        //$result = json_decode($result['body'], true);

        return $result;
    }

    /*下面均是辅助方法*/

    /**
     * 参数处理
     */
    private function buld_pram($data = array(), $method = "") {
        $str = '';
        if (count($data) < 1)
            return $str;
        foreach ($data as $key => $val) {
            if (gettype($val) != 'string')
                continue;
            if ($method != '') {
                if ($str != '')
                    $str .= "&";
                $str .= "$key=$val";
            } else {
                $str .= $val;
            }

        }
        return $str;
    }

    /**
     * 生成签名
     * @param string $str
     */
    private function buld_sign($str = '') {
        return $this -> HmacMd5($str, $this -> config['md5Key']);
    }

    /**
     * HmacMd5计算
     * @param string $data
     * @param string $key
     */
    private function HmacMd5($data = "", $key = "") {
        $b = 64;
        // byte length for md5
        if (strlen($key) > $b) {
            $key = pack("H*", md5($key));
        }
        $key = str_pad($key, $b, chr(0x00));
        $ipad = str_pad('', $b, chr(0x36));
        $opad = str_pad('', $b, chr(0x5c));
        $k_ipad = $key ^ $ipad;
        $k_opad = $key ^ $opad;

        return md5($k_opad . pack("H*", md5($k_ipad . $data)));
    }

    /**
     * 加密
     * @param string $input 明文
     */
    private function encrypt($input = "") {

        $key = $this -> config['md5Key'];
        if (strlen($key) > 16)
            $key = substr($key, 0, 16);
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $input = $this -> pkcs5_pad($input, $size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = $this -> strToHex($data);
        return $data;
    }

    private function pkcs5_pad($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    /**
     * 字符串转16进制
     * @param string $string
     * @return string
     */
    function strToHex($string = "")//字符串转十六进制
    {
        $hex = "";
        for ($i = 0; $i < strlen($string); $i++)
            $hex .= dechex(ord($string[$i]));
        $hex = strtoupper($hex);
        return $hex;
    }

    /**
     * 十六进行转化成字符串
     * @param string $hex
     * @return string
     */
    function hexToStr($hex = "")//十六进制转字符串
    {
        $string = "";
        for ($i = 0; $i < strlen($hex) - 1; $i += 2)
            $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        return $string;
    }

    /**
     * 解密
     * @param string $sStr 密文
     */
    function decrypt($sStr) {
        $sKey = $this -> config['md5Key'];
        if (strlen($sKey) > 16)
            $sKey = substr($sKey, 0, 16);
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $sKey, $this -> hexToStr($sStr), MCRYPT_MODE_ECB);

        $dec_s = strlen($decrypted);
        $padding = ord($decrypted[$dec_s - 1]);
        $decrypted = substr($decrypted, 0, -$padding);
        $decrypted = urldecode($decrypted);
        //echo($decrypted);
        return $decrypted;
    }

    /**
     * 无警告信息获取用户提交的值
     * @param	string	$key		参数名：数字、字母、下划线
     * @param	string	$method		all,post与get
     * @param	string	$default	如为空的默认值
     * @return	string
     */
    function g($key = "", $method = 'all', $default = '') {
        if ($key == '') {
            return $default;
        }
        switch (strtolower($method)) {
            case 'post' :
                $url = $_POST;
                break;
            case 'get' :
                $url = $_GET;
                break;
            default :
                $url = $_REQUEST;
                break;
        }
        return (isset($url[$key])) ? $url[$key] : $default;
    }

    public function yeepaynotify(){

/*
        $input=file_get_contents('php://input');
        $file = fopen('./input.txt', 'a+');
        fwrite($file,' \n '.var_export($input,true));
        fwrite($file,' \n POST= '.var_export($_POST,true));
        fclose($file);
        die('filed');
*/
        //名字
        $payerName = $_POST['payerName'];
        //银行名字
        $bankCode = $_POST['bankCode'];
        //金额
        $amount = $_POST['amount'];
        //支付时间
        $payTime = $_POST['payTime'];
        //信用卡卡号
        $lastNo = $_POST['lastNo'];
        //订单号
        $requestId = $_POST['requestId'];
        //状态
        $deal_status = 2;

        $jilu['deal_user_name'] = $payerName;
        $jilu['deal_xinyong_name'] = $bankCode;
        $jilu['deal_money'] = $amount;
        $jilu['deal_time'] = $payTime;
        $jilu['deal_xinyong_num'] = $lastNo;
        $jilu['deal_status'] = $deal_status;

        $tb_yyy = M('yyy');
        $flgnum = $tb_yyy -> where('deal_ordernumber="'.$requestId.'"') -> save($jilu);
        $flginfo=$tb_yyy->where('deal_ordernumber="'.$requestId.'"')->find();
        $uid=$flginfo['uid'];
        $this->fanyong($uid,$amount,'商户支付返佣');
        if($flgnum > 0){
            $this -> jsonout('faild', 'success', null);
        }
    }
}