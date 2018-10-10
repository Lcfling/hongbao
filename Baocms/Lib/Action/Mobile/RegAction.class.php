<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/20
 * Time: 18:16
 */

class RegAction extends CommonAction
{

    //todo 注册表单 reg      Lee_zhj
    public function register()
    {

        $this->display();
    }

    //todo 注册处理       Lee_zhj
    public function do_register()
    {

        $username = I('username');
        $password = I('password');
        $repassword = I('repassword');

        if (empty($password)) {
            $this->error('密码不能为空！');
        }

        if ($password != $repassword) {
            $this->error('两次输入密码不一致！');
        }

        //检测该用户是否已经注册
        $model = new $model('User');
        $user = $model->where(array('username' => $username))->find();
        if (empty($user)) {
            $this->error('用户名已存在！');
        }

        $data = array(
            'username' => $username,
            'password' => md5($password),
            'created_at' => time()
        );

        if (!($model->create($data) && $model->add())) {
            $this->error('注册失败！' . $model->getDbError());
        } else {
            $this->success('注册成功，请登录', U('login'));
        }
    }
}