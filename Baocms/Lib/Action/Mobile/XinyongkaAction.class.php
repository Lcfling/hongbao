<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/26
 * Time: 10:36
 */

class XinyongkaAction extends CommonAction {

        public function index(){
            $this->display();
        }
    public function jiaotong(){

        //通过id查找自己的个人信息
      // $user_id=$_SESSION['ID'];

        $user_id=$_COOKIE['ID'];

        //$user_id=811;
        $tb_user=M('user');
        $user['ID']=$user_id;
        $userlist=$tb_user->where($user)->find();

        $this->assign('userlist',$userlist);
        $this->assign('userpwd',md5($userlist['rand_psd']));
        $this->display();
    }
    public function pufa(){
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
    public function minsheng(){
       // $user_id=$_SESSION['ID'];
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
    public function pingan(){
       // $user_id=$_SESSION['ID'];
        $user_id=$_COOKIE['ID'];
        //通过id查找自己的个人信息
       // $user_id=811;
        $tb_user=M('user');
        $user['ID']=$user_id;
        $userlist=$tb_user->where($user)->find();

        $this->assign('userlist',$userlist);
        $this->assign('userpwd',md5($userlist['rand_psd']));
        $this->display();
    }
    public function guangda(){
       // $user_id=$_SESSION['ID'];
        $user_id=$_COOKIE['ID'];
        //通过id查找自己的个人信息
       // $user_id=811;
        $tb_user=M('user');
        $user['ID']=$user_id;
        $userlist=$tb_user->where($user)->find();

        $this->assign('userlist',$userlist);
        $this->assign('userpwd',md5($userlist['rand_psd']));
        $this->display();
    }
    public function zhongxin(){
       // $user_id=$_SESSION['ID'];
        $user_id=$_COOKIE['ID'];
       // $user_id=811;
        //通过id查找自己的个人信息
        $tb_user=M('user');
        $user['ID']=$user_id;
        $userlist=$tb_user->where($user)->find();

        $this->assign('userlist',$userlist);
        $this->assign('userpwd',md5($userlist['rand_psd']));
        $this->display();
    }
    public function shanghai(){
       // $user_id=$_SESSION['ID'];
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
    public function huaxia(){
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
}