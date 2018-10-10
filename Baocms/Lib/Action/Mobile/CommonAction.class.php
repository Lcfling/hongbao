<?php
class CommonAction extends Action
{
    protected $uid = 0;
    protected $member = array();
    protected $_CONFIG = array();
    protected $bizs = array();

    protected function _initialize()
    {
        header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
        header('Access-Control-Allow-Methods:GET, POST');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
    }
	//lcf $error success faild
	public function jsonout($error="faild",$msg="",$data){
        $result=array();
        $result['error']=$error;
        $result['msg']=$msg;
        $result['data']=$data;
      //  print_r($result);
        die(json_encode($result));
    }

    public function ulimit($user_id){
        //查询总收益
        $tb_fanyong=M('fanyong');
        $fanyong1['p1id']=$user_id;
        $money1=$tb_fanyong->where($fanyong1)->field('sum(p1fy)+sum(p1gl) as money')->select();

        $fanyong2['p2id']=$user_id;
        $money2=$tb_fanyong->where($fanyong2)->field('sum(p2fy)+sum(p2gl) as money')->select();

        $fanyong3['p3id']=$user_id;
        $money3=$tb_fanyong->where($fanyong3)->field('sum(p3fy)+sum(p3gl) as money')->select();
        //总收益
        $money=$money1[0]['money']+$money2[0]['money']+$money3[0]['money'];
        return $money;
    }


    // 总结算
    public function yijiesuan($user_id){

        //查询已提现金额
        $tb_record=M("record");
        //未审核
        $record['user_ID']=$user_id;
        $record['status']=0;
        $money1=$tb_record->where($record)->field('sum(txmoney) as money')->find();

        //已审核
        $record1['user_ID']=$user_id;
        $record1['status']=1;
        $money2=$tb_record->where($record1)->field('sum(txmoney) as money')->find();
        //已打款
        $record2['user_ID']=$user_id;
        $record2['status']=2;
        $money3=$tb_record->where($record2)->field('sum(txmoney) as money')->find();
        //已结算
        $money= $money1['money']+$money2['money']+$money3['money'];

        return $money;

    }
        //随机密码
    private function genRandomString($len = 6) {
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );
        $charsLen = count($chars) - 1;
        // 将数组打乱
        shuffle($chars);
        $output = "";
        for ($i = 0; $i < $len; $i++) {
            $output .= $chars[mt_rand(0, $charsLen)];
        }
        return $output;
    }

    //登陆验证
    public function login_verify($ID,$randpwd){

        //查询用户密码
        $tb_user=M('user');
        $user['ID']=$ID;
        $list=$tb_user->where($user)->field('rand_psd')->select();
        //判断随机密码是否一致
        if ($randpwd != md5($list[0]['rand_psd'])){
            $data['msg']="loginfaild";
            $data['error']='faild';
            $this->jsonout('faild','loginfaild');
        }
    }

    //xml转数组...
    function xmlToArray($xml) {
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }
    //xml数据转字符串
    function simplest_xml_to_array($xmlstring) {
        return json_decode(json_encode((array) simplexml_load_string($xmlstring)), true);
    }
    //获取MD5
    function get_md5($sstring) {
        return strtoupper(md5($sstring));
    }
    //获取签名字符串
    function getSign($arr_Allvalu) {
        //定义商户秘钥
        $qp_Key = "F1E9132EC2AA2F35";
        //定义签名字符串
        $sign_Str = "";
        //按照key的升序排列
        ksort($arr_Allvalu);
        //遍历数组
        foreach ($arr_Allvalu as $key => $value) {
            $lower_Key = strtolower($key);
            $sign_Str .= $lower_Key . "=" . $value . "&";
        }
        //拼接秘钥和字符串
        $sign_Str = $sign_Str . "key=" . $qp_Key;
        return $sign_Str;
    }
    //post请求方法
    function https_post($url, $data) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $ref = curl_exec($curl);

        if (curl_errno($curl)) {
            return 'Errno' . curl_error($curl);
        }
        curl_close($curl);
        return $ref;
    }
    function https_request($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {return 'ERROR '.curl_error($curl);}
        curl_close($curl);
        return $data;
    }
    private function getTemplateTheme(){
        define('THEME_NAME', 'default');
        if ($this->theme) {
            // 指定模板主题
            $theme = $this->theme;
        } else {
            /* 获取模板主题名称 */
            $theme = D('Template')->getDefaultTheme();
            if (C('TMPL_DETECT_THEME')) {
                // 自动侦测模板主题
                $t = C('VAR_TEMPLATE');
                if (isset($_GET[$t])) {
                    $theme = $_GET[$t];
                } elseif (cookie('think_template')) {
                    $theme = cookie('think_template');
                }
                if (!in_array($theme, explode(',', C('THEME_LIST')))) {
                    $theme = C('DEFAULT_THEME');
                }
                cookie('think_template', $theme, 864000);
            }
        }
        return $theme ? $theme . '/' : '';
    }
    private function parseTemplate($template = ''){
        $depr = C('TMPL_FILE_DEPR');
        $template = str_replace(':', $depr, $template);
        // 获取当前主题名称
        $theme = $this->getTemplateTheme();
        define('NOW_PATH', BASE_PATH . '/themes/' . $theme . 'Mobile/');
        // 获取当前主题的模版路径
        define('THEME_PATH', BASE_PATH . '/themes/default/Mobile/');
        define('APP_TMPL_PATH', __ROOT__ . '/themes/default/Mobile/');
        // 分析模板文件规则
        if ('' == $template) {
            // 如果模板文件名为空 按照默认规则定位
            $template = strtolower(MODULE_NAME) . $depr . strtolower(ACTION_NAME);
        } elseif (false === strpos($template, '/')) {
            $template = strtolower(MODULE_NAME) . $depr . strtolower($template);
        }
        $file = NOW_PATH . $template . C('TMPL_TEMPLATE_SUFFIX');
        if (file_exists($file)) {
            return $file;
        }
        return THEME_PATH . $template . C('TMPL_TEMPLATE_SUFFIX');
    }
    public function show($templateFile = ''){
        $this->seo();
        parent::display($templateFile);
    }
    public function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = ''){
        $this->seo();
        parent::display($this->parseTemplate($templateFile), $charset, $contentType, $content = '', $prefix = '');
    }
    private function tmplToStr($str, $datas){
        preg_match_all('/{(.*?)}/', $str, $arr);
        foreach ($arr[1] as $k => $val) {
            $v = isset($datas[$val]) ? $datas[$val] : '';
            $str = str_replace($arr[0][$k], $v, $str);
        }
        return $str;
    }
    private function seo() {
        $this->assign('mobile_title', $this->mobile_title);
        $this->assign('mobile_keywords', $this->mobile_keywords);
        $this->assign('mobile_description', $this->mobile_description);
    }
    //todo 跳转地址
    public function url($url){

        switch ($url)
        {
            //贷款跳转
            case 'dk0':
                header("Location:http://qp.webziti.com/mobile/daikuan/zhongyuanjinrong"); //中原金融
                break;

            case 'dk1':
                header("Location:http://qp.webziti.com/mobile/daikuan/zongandiandian"); //众安点点
                break;
            case 'dk2':
                header("Location:http://qp.webziti.com/mobile/daikuan/fangsiling"); //方司令
                break;
            case 'dk3':
                header("Location:http://qp.webziti.com/mobile/daikuan/yirendai"); //宜人贷
                break;
            case 'dk4':
                header("Location:http://qp.webziti.com/mobile/daikuan/jietiao360"); //360借条
                break;
            case 'dk5':
                header("Location:http://qp.webziti.com/mobile/daikuan/xiaoheiyu"); //小黑鱼
                break;
            case 'dk6':
                header("Location:http://qp.webziti.com/mobile/daikuan/paipaidai"); //拍拍贷
                break;
            case 'dk7':
                header("Location:http://qp.webziti.com/mobile/daikuan/zhonganxinye"); //中安信业
                break;
            case 'dk8':
                header("Location:http://qp.webziti.com/mobile/daikuan/niwodai");  //你我贷
                break;
            case 'dk9':
                header("Location:http://qp.webziti.com/mobile/daikuan/xinyongfei"); //信用飞
                break;
            case 'dk10':
                header("Location:http://qp.webziti.com/mobile/daikuan/xinerfu"); //信而富
                break;
            case 'dk11':
                header("Location:http://qp.webziti.com/mobile/daikuan/xiaoshupuhui"); //小树普惠
                break;

            //信用卡跳转
            case 'xy1':
                header("Location:http://qp.webziti.com/mobile/xinyongka/jiaotong"); //交通信用卡
                break;
            case 'xy2':
                header("Location:http://qp.webziti.com/mobile/xinyongka/pufa"); //浦发信用卡
                break;
            case 'xy3':
                header("Location:http://qp.webziti.com/mobile/xinyongka/minsheng"); //民生信用卡
                break;
            case 'xy4':
                header("Location:http://qp.webziti.com/mobile/xinyongka/pingan"); //平安信用卡
                break;
            case 'xy5':
                header("Location:http://qp.webziti.com/mobile/xinyongka/guangda"); //光大信用卡
                break;
            case 'xy6':
                header("Location:http://qp.webziti.com/mobile/xinyongka/zhongxin"); //中信信用卡
                break;
            case 'xy7':
                header("Location:http://qp.webziti.com/mobile/xinyongka/shanghai"); //上海信用卡
                break;
            case 'xy8':
                header("Location:http://qp.webziti.com/mobile/xinyongka/huaxia"); //华夏信用卡
                break;

            default:
                header("Location:http://qp.webziti.com/mobile/daikuan/load_web"); //首页
                break;
        }
    }
    const MAX_ENCRYPT_BLOCK = 117;
    const MAX_DECRYPT_BLOCK = 128;

    /**
     * 私钥加密
     * @param $plainString
     * @param $privateKey
     * @return string
     */
    public  function priEncrypt($plainString, $privateKey)
    {
        $encryptedString = "";
        $strlen = strlen($plainString);
        if ($strlen < 118) {
            openssl_private_encrypt($plainString, $encryptedString, self::conertKey($privateKey,"pri"));//私钥加密
        } else {
            foreach (str_split($plainString, self::MAX_ENCRYPT_BLOCK) as $chunk) {
                openssl_private_encrypt($chunk, $chunkString, self::conertKey($privateKey,"pri"));
                $encryptedString .= $chunkString;
            }
        }
        return $encryptedString;
    }
    /**
     * 私钥解密
     * @param $encryptedString
     * @param $privateKey
     * @return string
     */
    public static function priDecrypt($encryptedString, $privateKey)
    {
        $plainString = "";
        $strlen = strlen($encryptedString);
        if ($strlen < 129) {
            openssl_private_decrypt($encryptedString,$plainString,self::conertKey($privateKey,"pri"));//私钥解密
        } else {
            foreach (str_split($encryptedString, self::MAX_DECRYPT_BLOCK) as $chunk) {
                openssl_private_decrypt($encryptedString,$chunkString,self::conertKey($privateKey,"pri"));//私钥解密
                $plainString .= $chunkString;
            }
        }
        return $plainString;
    }
    /**
     * 公钥加密
     * @param $plainString
     * @param $publicKey
     * @return string
     */
    public static function pubEncrypt($plainString, $publicKey)
    {
        $encryptedString = "";
        $strlen = strlen($plainString);
        if ($strlen < 118) {
            openssl_public_encrypt($plainString,$encryptedString,self::conertKey($publicKey,"pub"));//公钥加密
        } else {
            foreach (str_split($plainString, self::MAX_ENCRYPT_BLOCK) as $chunk) {
                openssl_public_encrypt($chunk,$chunkString,self::conertKey($publicKey,"pub"));//公钥加密
                $encryptedString .= $chunkString;
            }
        }
        return $encryptedString;
    }
    /**
     * 公钥解密
     * @param $encryptedString
     * @param $publicKey
     * @return string
     */
    public static function pubDecrypt($encryptedString, $publicKey)
    {
        $plainString = "";
        $strlen = strlen($encryptedString);
        if ($strlen < 129) {
            openssl_public_decrypt($encryptedString,$plainString,self::conertKey($publicKey,"pub"),OPENSSL_PKCS1_PADDING);//公钥解密
        } else {
            foreach (str_split($encryptedString, self::MAX_DECRYPT_BLOCK) as $chunk) {
                openssl_public_decrypt($chunk, $chunkString, self::conertKey($publicKey,"pub"),OPENSSL_PKCS1_PADDING);
                $plainString .= $chunkString;
            }
        }
        return $plainString;
    }
    public static function sign($data, $key)
    {
        $encSign = "";
        openssl_sign($data, $encSign, self::conertKey($key,"pri"), OPENSSL_ALGO_MD5);
        return $encSign;
    }

    public static function verify($data, $sign, $key)
    {
        return openssl_verify($data, base64_decode($sign), self::conertKey($key,"pub"), OPENSSL_ALGO_MD5);
    }

    public static function conertKey($key,$type){
        {
            if($type == 'pri'){
                $begin = "-----BEGIN PRIVATE KEY-----\n";
                $end = "\n-----END PRIVATE KEY-----";
            }else{
                $begin = "-----BEGIN PUBLIC KEY-----\n";
                $end = "\n-----END PUBLIC KEY-----";
            }
            $key_string = $begin . wordwrap($key, 64, "\n", true) . $end;
            return $key_string;
        }
    }
    //POST 请求
    public static function https_post1($URL,$Post_data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $Post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($Post_data))
        );
        ob_start();
        curl_exec($ch);
        $return_content = ob_get_contents();
        ob_end_clean();
        $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return  $return_content;
    }
    //GET请求
    public static function http_request($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {return 'ERROR '.curl_error($curl);}
        curl_close($curl);
        return $data;
    }
}