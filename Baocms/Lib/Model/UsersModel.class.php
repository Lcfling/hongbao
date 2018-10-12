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

    /**获取用户余额
     * @param $uid
     * @return mixed
     */
    public function getUserMoney($uid){

        return $money;
    }


}