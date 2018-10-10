<?php



class WeixinAction extends CommonAction {




    public function wxlogin(){

        $code=$_POST['code'];

        $myfile = fopen("aaa.txt", "a+") ;
       // $txt = "";
        fwrite($myfile, $code);
       
        fclose($myfile);






        $appid="wx57faf750ee231971";
        $appsecret="930362097e58afb918f53e82d9b74426";

        //通过code获取access_token的接口。
        $access_token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$appsecret."&code=".$code."&grant_type=authorization_code";

        $access_token= $this->httpget($access_token_url);

        if ($access_token['errcode']){

            die(json_encode($access_token));
        }

        //刷新或续期access_token使用
        $access_token_url1="https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=".$appid."&grant_type=refresh_token&refresh_token=".$access_token['refresh_token'];
        $access_token1=$this->httpget($access_token_url1);

        if ($access_token1['errcode']){
            die(json_encode($access_token1));
        }

        //检验授权凭证（access_token）是否有效
        $access_token_url2="https://api.weixin.qq.com/sns/auth?access_token=".$access_token1['access_token']."&openid=".$access_token1['openid'];
        $access_token2=$this->httpget($access_token_url2);

        if ($access_token_url2['errcode'] != 0){
            die(json_encode($access_token2));
        }

        //获取用户个人信息（UnionID机制）
        $access_token_url3="https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token1['access_token']."&openid=".$access_token1['openid'];
        $userdata=$this->httpget($access_token_url3);

        if ($userdata['errcode']){
            die(json_encode($userdata));
        }

        $qqq=$userdata['unionid'];

        //判断用户是否首次登陆


        //查询用户信息
        $tb_user=M("user");
        $user['unionid']=$qqq;
        $user1=$tb_user->where($user)->find();


        //判断用户是否第一次登陆
        if ($user1['ID']>0){

            $rand_psd= $this->genRandomString();
            $tb_user->where('unionid='.$qqq)->save($rand_psd);
            $user2['unionid']=$qqq;
            $list=$tb_user->where($user2)->find();




            $user['user_id']=$list['ID'];
            $user['password']=md5($list['rand_psd']);
            $user['error']="success";
            $user['msg']="";
            die(json_encode($user)) ;

        }else{

            $nickname=$userdata['nickname']; //用户昵称
            $headimgurl=$userdata['headimgurl'];//用户头像
            $unionid=$userdata['unionid']; //用户统一标识
            $openid=$userdata['openid'];

            date_default_timezone_set('PRC');
            $time=date("Y-m-d H:i:s");
            //$time=date("Y-m-d H:i:s",time());

//            $sql="INSERT INTO  tb_user (openID,nickName,headerImg,payDate,user_lv,pay_num,vip_rank,p1id,p2id,p3id,p4id,ulimit,if_pay,unionid,randpwd) VALUES
//          ('$openid','$nickname','$headimgurl','$time',1,0,0,'554','0','0','0',0,0,'$unionid','$randpwd')";
//
//            $userlist=mysql_query($sql);

            //数据入库
            $tb_user=M("user");
            $user['openID']=$openid;   //openid
            $user['nickName']=$nickname; //用户名称
            $user['headerImg']=$headimgurl;
            $user['payDate']=$time;
            $user['user_lv']=1;
            $user['pay_num']=0;
            $user['vip_rank']=0;
            $user['p1id']=554;
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
                $tb_user=M("user");
                $user['unionid']=$unionid;
                $list=$tb_user->where($user)->find();

                $user['user_id']=$list['ID'];
                $user['password']=md5($list['rand_psd']);
                $user['error']="success";
                $user['msg']='';
                die(json_encode($user));
            }else{
                $user['user_id']='';
                $user['password']='';
                $user['error']="fault";
                $user['msg']='';
                die(json_encode($user));

            }
        }
    }

    public function  httpget($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($data,true);
        return $data;
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