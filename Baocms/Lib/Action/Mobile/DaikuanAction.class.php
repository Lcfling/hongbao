<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/25
 * Time: 14:30
 */
class DaikuanAction extends CommonAction {

    public function index(){
        $this->display();
    }
    public function load_web(){
        $this->display();
    }

    public function zhongyuanjinrong(){
        //$user_id=$_SESSION['ID'];
        $user_id=$_COOKIE['ID'];
        //  $user_id=811;
        //通过id查找自己的个人信息
        $tb_user=M('user');
        $user['ID']=$user_id;
        $userlist=$tb_user->where($user)->find();

        $this->assign('userlist',$userlist);
        $this->assign('userpwd',md5($userlist['rand_psd']));
        $this->display();
    }

    public function zongandiandian(){
        // $user_id=$_SESSION['ID'];
        $user_id=$_COOKIE['ID'];
        //通过id查找自己的个人信息
        $tb_user=M('user');
        $user['ID']=$user_id;
        $userlist=$tb_user->where($user)->find();

        $this->assign('userlist',$userlist);
        $this->assign('userpwd',md5($userlist['rand_psd']));
        $this->display();
    }
    public function fangsiling(){
          // $user_id=$_SESSION['ID'];
        $user_id=$_COOKIE['ID'];
        //通过id查找自己的个人信息
        $tb_user=M('user');
        $user['ID']=$user_id;
        $userlist=$tb_user->where($user)->find();

        $this->assign('userlist',$userlist);
        $this->assign('userpwd',md5($userlist['rand_psd']));
        $this->display();
    }
    public function yirendai(){
       // $user_id=$_SESSION['ID'];
        $user_id=$_COOKIE['ID'];
        //通过id查找自己的个人信息
        $tb_user=M('user');
        $user['ID']=$user_id;
        $userlist=$tb_user->where($user)->find();

        $this->assign('userlist',$userlist);
        $this->assign('userpwd',md5($userlist['rand_psd']));
        $this->display();
    }
    public function jietiao360(){
       // $user_id=$_SESSION['ID'];
       $user_id=$_COOKIE['ID'];
        //通过id查找自己的个人信息
      //  $user_id=811;
        $tb_user=M('user');
        $user['ID']=$user_id;
        $userlist=$tb_user->where($user)->find();

        $this->assign('userlist',$userlist);
        $this->assign('userpwd',md5($userlist['rand_psd']));
        $this->display();
    }
    public function xiaoheiyu(){
      //  $user_id=$_SESSION['ID'];
        $user_id=$_COOKIE['ID'];
        //通过id查找自己的个人信息
        $tb_user=M('user');
        $user['ID']=$user_id;
        $userlist=$tb_user->where($user)->find();

        $this->assign('userlist',$userlist);
        $this->assign('userpwd',md5($userlist['rand_psd']));
        $this->display();
    }
    public function paipaidai(){
       // $user_id=$_SESSION['ID'];
        $user_id=$_COOKIE['ID'];
        //通过id查找自己的个人信息
        $tb_user=M('user');
        $user['ID']=$user_id;
        $userlist=$tb_user->where($user)->find();

        $this->assign('userlist',$userlist);
        $this->assign('userpwd',md5($userlist['rand_psd']));
        $this->display();
    }
    public function zhonganxinye(){
      //  $user_id=$_SESSION['ID'];
        $user_id=$_COOKIE['ID'];
        //通过id查找自己的个人信息
        $tb_user=M('user');
        $user['ID']=$user_id;
        $userlist=$tb_user->where($user)->find();

        $this->assign('userlist',$userlist);
        $this->assign('userpwd',md5($userlist['rand_psd']));
        $this->display();
    }
    public function niwodai(){
       // $user_id=$_SESSION['ID'];
        $user_id=$_COOKIE['ID'];
        //通过id查找自己的个人信息
        $tb_user=M('user');
        $user['ID']=$user_id;
        $userlist=$tb_user->where($user)->find();

        $this->assign('userlist',$userlist);
        $this->assign('userpwd',md5($userlist['rand_psd']));
        $this->display();
    }
    public function xinyongfei(){
       // $user_id=$_SESSION['ID'];
        $user_id=$_COOKIE['ID'];
        //通过id查找自己的个人信息
        $tb_user=M('user');
        $user['ID']=$user_id;
        $userlist=$tb_user->where($user)->find();

        $this->assign('userlist',$userlist);
        $this->assign('userpwd',md5($userlist['rand_psd']));
        $this->display();
    }
    public function xinerfu(){
       // $user_id=$_SESSION['ID'];
        $user_id=$_COOKIE['ID'];
        //通过id查找自己的个人信息
        $tb_user=M('user');
        $user['ID']=$user_id;
        $userlist=$tb_user->where($user)->find();

        $this->assign('userlist',$userlist);
        $this->assign('userpwd',md5($userlist['rand_psd']));
        $this->display();
    }
    public function xiaoshupuhui(){
       // $user_id=$_SESSION['ID'];
        $user_id=$_COOKIE['ID'];
        //通过id查找自己的个人信息
        $tb_user=M('user');
        $user['ID']=$user_id;
        $userlist=$tb_user->where($user)->find();

        $this->assign('userlist',$userlist);
        $this->assign('userpwd',md5($userlist['rand_psd']));
        $this->display();
    }



}