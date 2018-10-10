<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/12
 * Time: 14:07
 */

class CreditAction extends CommonAction{

    //todo 贷款      Lee_zhj
    public function credit(){


        //获取用户信息
        $user_id=$_GET['ID'];
        $randpwd=$_GET['randpwd'];


        //调用登录验证
        $this->login_verify($user_id,$randpwd);


        $username = $_GET['name'];
        $idcard = $_GET['sfz'];
        $phonenum = $_GET['phonenum'];
        $fromtongdao = $_GET['title'];
        $yzm = $_GET['yzm'];



        $tb_dkinfo = M('dkinfo');
        $dkinfo['tel'] = $phonenum;
        $dkinfo['yzm'] = $yzm;

        $wer = $tb_dkinfo->where($dkinfo)->find();
        if (!$wer){
            $this->jsonout('faild','验证码不对！');
        }


        if($user_id=="" || $username=="" || $idcard=="" || $phonenum=="" || $fromtongdao==""){
            $this->jsonout('faild','非法操作！');
        }


        date_default_timezone_set('PRC');
        $time = date("Y-m-d H:i:s");

        $tb_credit = M('credit');

        $data['uid'] = $user_id;
        $data['username'] = $username;
        $data['idcard'] = $idcard;
        $data['phonenum'] = $phonenum;
        $data['fromtongdao'] = $fromtongdao;
        $data['userdate'] = $time;
        //数据存入数据库
        $usermsg = $tb_credit->add($data);

        if($usermsg){
            if($fromtongdao=="中原消费金融"){
                $linkurl['url'] = 'http://app.hnzycfc.com:8080/Download/m_app.html';
                $this->jsonout('success','中原消费金融',$linkurl);
            }else if($fromtongdao=="众安点点"){
                $linkurl['url'] = 'https://fin-app.zhongan.com/sh/event/diandian-mark/index.html#/?registerPlatform=h5&channelCode=zyp01&campaignNo=';
                $this->jsonout('success','众安点点',$linkurl);
            }else if($fromtongdao=="房司令"){
                $linkurl['url'] = 'https://zujin.58fangdai.com/h5/register5/index?linksource=zyph';
                $this->jsonout('success','房司令',$linkurl);
            }else if($fromtongdao=="宜人贷"){
                $linkurl['url'] = 'https://bang.yirendai.com/signup?referrer=629bde37-7eef-37e3-b966-cba4648172f4&affiliate=qrcode_xdy&shareagent=wechatandroid';
                $this->jsonout('success','宜人贷',$linkurl);
            }else if($fromtongdao=="360借条"){
                $linkurl['url'] = 'https://mkt.360jie.com.cn/activity/ch/zhuoran/lanmu6';
                $this->jsonout('success','360借条',$linkurl);
            }else if($fromtongdao=="小黑鱼"){
                $linkurl['url'] = 'https://h5.blackfish.cn/p/4/01011043529000000';
                $this->jsonout('success','小黑鱼',$linkurl);
            }else if($fromtongdao=="拍拍贷"){
                $linkurl['url'] = 'https://ac.ppdai.com/activitypage?redirect=https://m.ppdai.com/loan/Users/UserInfo&style=&activityId=152&source=14987&regsourceid=yiyinpuhui2';
                $this->jsonout('success','拍拍贷',$linkurl);
            }else if($fromtongdao=="中安信业"){
                $linkurl['url'] = 'http://bd.zac.cn/bd/appweb/action/simpleApply?systemId=1yykj2017&extendTab=07';
                $this->jsonout('success','中安信业',$linkurl);
            }else if($fromtongdao=="你我贷"){
                $linkurl['url'] = 'https://ka.niwodai.com/loans-mobile/product.do?method=index&nwd_ext_aid=5020161774884457&source_id=7';
                $this->jsonout('success','你我贷',$linkurl);
            }else if($fromtongdao=="信用飞"){
                $linkurl['url'] = 'https://m.xinyongfei.cn/activity/guide-v3?utm_source=BJYYKJYXGS-BJYYKJYXGS-AY';
                $this->jsonout('success','信用飞',$linkurl);
            }else if($fromtongdao=="信而富"){
                $linkurl['url'] = 'https://promotion.crfchina.com/life/index.html?c=&s=life&salesmanNo=JKTZNJ0018&agentNo=JKTZNJ0091_20180409BJYY001';
                $this->jsonout('success','信而富',$linkurl);
            }else if($fromtongdao=="小树普惠"){//------------
                $linkurl['url'] = 'http://activity.xiaoshupuhui.com/170607_Register/index.html?key=2930684';
                $this->jsonout('success','小树普惠',$linkurl);
            }
        }else{
            $this->jsonout('faild','非法操作！');
        }





    }


}