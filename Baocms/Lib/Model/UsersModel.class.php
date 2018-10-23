<?php
class UsersModel extends CommonModel
{
    protected $pk = 'user_id';
    protected $tableName = 'users';
    //protected $_integral_type = array('share' => '发帖分享', 'reply' => '回复帖子', 'mobile' => '手机认证', 'email' => '邮件认证');

    /**根据用户id获取用户信
     * @param $user_id
     * @param bool $cleanCache
     * @return $userinfo
     */
    public function getUserByUid($user_id,$cleanCache=false){
        if($cleanCache){
            Cac()->set('userinfo_'.$user_id,null);
            $data = $this->find(array('where' => array('user_id' => $user_id)));
        }else{
            $data=Cac()->get('userinfo_'.$user_id);
            if($data!=null){
                $data=unserialize($data);
            }
            if(!empty($data)){
                return $this->_format($data);
            }else{
                $data = $this->find(array('where' => array('user_id' => $user_id)));
                Cac()->set('userinfo_'.$user_id,serialize($data));
            }
        }

        //$data = $this->find(array('where' => array('account' => $account)));
        return $this->_format($data);
    }
    /**根据用户id获取用户信
     * @param $user_id
     * @param bool $cleanCache
     * @return $userinfo
     */
    public function getUserByMobile($mobile,$cleanCache=false){
        if($cleanCache){
            //Cac()->set('userinfo_'.$mobile,null);
            $data = $this->find(array('where' => array('account' => $mobile)));
            Cac()->set('userinfo_mobile_'.$mobile,null);
        }else{
            $data=Cac()->get('userinfo_mobile_'.$mobile);
            if($data!=null){
                $data=unserialize($data);
            }
            if(!empty($data)){
                return $this->_format($data);
            }else{
                $data = $this->find(array('where' => array('user_id' => $mobile)));
                Cac()->set('userinfo_mobile_'.$mobile,serialize($data));
            }
        }
        //$data=$this->where(array('account'=>(String)$mobile))->find();
        //$data = $this->find(array('where' => array('account' => (String)$mobile)));

        return $data;
    }

    /**获取用户余额
     * @param $uid
     * @return mixed
     */
    public function getUserMoney($uid){
        $sql="SELECT SUM(money) AS usermoney FROM __PREFIX__paid WHERE user_id=$uid";
        $res=$this->Query($sql);
        $money=$res[0]['usermoney'];
        return $money;
    }

    /**更新用户缓存
     * @param $userInfo
     * @return mixed
     */
    public function updateLoginCache($userInfo){
        $userInfo['last_ip']=$data['last_ip']=getip();

        $userInfo['last_time']=$data['last_time']=time();
        $token=rand_string(6,1);
        $userInfo['token']=$data['token']=md5($token);

        $this->where(array('account'=>(string)$userInfo['account']))->save($data);
        Cac()->set('userinfo_'.$userInfo['user_id'],serialize($userInfo));
        Cac()->set('userinfo_mobile_'.$userInfo['account'],serialize($userInfo));
        return $userInfo;
    }

    public function insertUserInfo($mobile,$pid=0){
        $info['account']=$mobile;
        $info['password']=md5(rand_string(11,1));
        $info['nickname']=rand_string(11,1);
        $info['money']=0;
        $info['mobile']=$mobile;
        $info['reg_ip']=$info['last_ip']=getip();
        $info['reg_time']=$info['last_time']=time();
        $token=$info['token']=md5(rand_string(6,1));
        if($pid==0||$pid==""||$pid==null)
            $pid=0;
        $info['pid']=$pid;
        $this->add($info);
        $userInfo=$this->find(array('where'=>array('account'=>$mobile)));
        Cac()->set('userinfo_'.$userInfo['user_id'],serialize($userInfo));
        Cac()->set('userinfo_mobile_'.$userInfo['account'],serialize($userInfo));
        return $userInfo;
    }
    public function addmoney($uid,$money,$type,$is_afect=1,$remark=''){
        $info['order_id']=0;
        $info['money']=$money;
        $info['user_id']=$uid;
        $info['creatime']=time();
        $info['type']=$type;
        $info['remark']=$remark;
        $info['is_afect']=$is_afect;

        $m=D('Paid');
        if($m->add($info)){
            return true;
        }else{
            return false;
        }
    }
    public function reducemoney($uid,$money,$type,$is_afect=1,$remark=''){
        $info['order_id']=0;
        $info['money']=-$money;
        $info['user_id']=$uid;
        $info['creatime']=time();
        $info['type']=$type;
        $info['remark']=$remark;
        $info['is_afect']=$is_afect;

        $m=D('Paid');
        if($m->add($info)){
            return true;
        }else{
            return false;
        }
    }
    //获取一个随机机器用户信息
    public function randUser(){


    }

}