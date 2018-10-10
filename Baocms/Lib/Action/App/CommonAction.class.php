<?php 


class CommonAction extends BaseAction {
   

    protected $token = '';
    protected $member = array();

    //初始化 验证登陆信息 开启跨域
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
	//增加模板结束

    public function verify() {
        import('ORG.Util.Image');
        Image::buildImageVerify(4,2,'png',60,30);
    }


}