<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/27
 * Time: 18:15
 */
class SaomaAction extends CommonAction
{


    public function index()
    {
        $jieshou = $_GET['ID'];//分享者的 ID
        $daili_id = $_GET['daili_id'];//代理id
        $url= $_GET['url']; //跳转地址

        $p1id = $_COOKIE['haoyouID'];
        $ID=$_COOKIE['ID'];

        if ($p1id != "" && $ID!="") {
            setcookie("haoyouID", $jieshou, time() + 3600 * 12 * 30);
            $this->url($url);
        } else {
            setcookie("haoyouID", $jieshou, time() + 3600 * 12 * 30);
            setcookie("dailiID", $daili_id, time() + 3600 * 12 * 30);
            setcookie("url", $url, time() + 3600 * 12 * 30);
            header(
                "Location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7d9c147ad80517b5&redirect_uri=http://qp.webziti.com/mobile/saoma/getUserinfo&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect");
        }
    }

    public function getUserinfo()
    {

        $code = $_GET["code"];
        $appid = "wx7d9c147ad80517b5";
      //  $appid="wx5e261a404af2450c";

        $appsecret = "110954975335b354ca4bb1cf262ed09d";
        //$appsecret="d1526d652c5db95f076bc1d08e093d68";

        $access_token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appsecret&code=$code&grant_type=authorization_code";
        $access_token_json = $this->https_request($access_token_url);
        $access_token_array = json_decode($access_token_json, true);
        $access_token = $access_token_array['access_token'];
        $openid = $access_token_array['openid'];
        $userinfo_url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
        $userinfo_json = $this->https_request($userinfo_url);
        $userinfo_array = json_decode($userinfo_json, true);
       // global $userOpenID;
        $userOpenID = $userinfo_array['openid'];//nickname  headimgurl
        $userNickname = $userinfo_array['nickname'];
        $headimgurl = $userinfo_array['headimgurl'];
        $unionid = $userinfo_array['unionid'];

//        $_SESSION['openid'] = $userOpenID;
//        $_SESSION['nickname'] = $userNickname;
//        $_SESSION['headimgurl'] = $headimgurl;
//
//        $unionid="oOoVK01xzG83sqOkn3hE7bvPIHnY00022";
//        $userOpenID="asdas666";
//        $userNickname="张00002";
//        $headimgurl="666";

//        echo "useropenID == ".$userOpenID;
//        echo "userNickname ==".$userNickname;
//
//        die("unionid ==").$unionid;



        $tb_user=M("user");
        $user1['unionid']=$unionid;

        $userlist1=$tb_user->where($user1)->find();

        if ($userlist1) {
            cookie('ID',$userlist1['ID']);
                $this->addshangji($userlist1['ID']);

        } else {
            $time = date("Y-m-d H:i:s");

            $tb_user=M("user");
            $user['openID']=$userOpenID;   //openid
            $user['nickName']=$userNickname; //用户名称
            $user['headerImg']=$headimgurl;
            $user['payDate']=$time;
            $user['user_lv']=1;
            $user['pay_num']=0;
            $user['vip_rank']=0;
            $user['p1id']=0;
            $user['p2id']=0;
            $user['p3id']=0;
            $user['p4id']=0;
            $user['ulimit']=0;
            $user['if_pay']=0;
            $user['unionid']=$unionid;
            $rand_psd=$this->genRandomString();
            $user['rand_psd']= $rand_psd;
            $userlist=$tb_user->add($user);
            if ($userlist){
                $user1['unionid']=$unionid;
                $list=$tb_user->where($user1)->find();
                if ($list){
                    cookie('ID',$list['ID']);
                    $this->addshangji($list['ID']);
                }
            }
        }
    }

    public function addshangji($ID)
    {
       // $shangjiID=812;
       // $dailiID=811;
     //   $ID = $_SESSION['ID'];
        $shangjiID = $_COOKIE['haoyouID'];
        $dailiID = $_COOKIE['dailiID'];
        $url = $_COOKIE['url'];
        cookie('ID',$ID);



        if ($ID != "") {
            $tb_user=M("user");
            $user['ID']=$ID;
            $user['p1id']='0';
            $list=$tb_user->where($user)->find();
            if ($list){
                $user1['p1id']=$shangjiID;
                $user1['daili_id']=$dailiID;
                $tb_user->where('ID='.$ID)->save($user1);
                $this->url($url);
            }else{
                $this->url($url);
            }


        }

    }
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
}