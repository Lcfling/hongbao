<?php
class MessageModel extends CommonModel{
   // protected $pk   = 'msg_id';
   // protected $tableName =  'message';

	/*public function send($send_id,$receive_id,$parent_id,$content) {
		$send_id = (int) $send_id;
		$receive_id = (int) $receive_id;
		$parent_id = (int) $parent_id;
		if($send_id != 0){
			if(!D('User')->find($send_id)){
				return '0';
				die;
			}
		}
		if($receive_id != 0){
			if(!D('User')->find($receive_id)){
				return '0';
				die;
			}
		}else{
			return '0';
			die;
		}
		if(empty($content)){
			return '0';
			die;
		}
		$data = array();
		$data['send_id'] = $send_id;
		$data['receive_id'] = $receive_id;
		$data['parent_id'] = $parent_id;
		$data['content'] = $content;
		$data['create_time'] = time();
		$msg_id = D('Message')->add($data);
		return $msg_id;
	}*/
//通过id 判断是否有信息，返回条数
	public function messagenumber($user_id){
	    $dbsql=D("Message");
        $map['user_id']=$user_id;
        $map['ifread']=0;//未读
        $count = $dbsql->where($map)->count(); // 查询满足要求的总记录数
	   return $count;

    }

   public function messagelist($user_id)
   {
       $dbsql=D("Message");
       $map['user_id']=$user_id;
       //$map['ifread']=0;//未读
       $list = $dbsql->where($map)->order(array('id' => 'desc'))->limit(0 . ',' . 10)->select();
      // $count = $dbsql->where($map)->count(); // 查询满足要求的总记录数
       return $list;
   }

   public function readmessage($message_id)
   {
       $dbsql=D("Message");
       $map['id']=$message_id;
       //$map['ifread']=0;//未读
       $list = $dbsql->where($map)->select();
       if($list['ifread']==0){

         $data['ifread']=1;//标记已读
        $dbsql->where($map)->save($data);
       }
       // $count = $dbsql->where($map)->count(); // 查询满足要求的总记录数
       return $list;
   }

}



