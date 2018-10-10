<?php


class IndexAction extends CommonAction {


    //todo 资讯 ZF
    public function message(){

        //获取咨询 类别-5
        $tb_news=M('news');
        $news['lb']=5;

        $data=$tb_news->where($news)->select();

        foreach ($data as $k=>$v){
//            $data[$k]['content']=htmlentities($data[$k]['content']);
            $data[$k]['content']=$data[$k]['content'];
        }
        die(json_encode($data));

    }




    //todo 信用卡 ZF
    public function creditcard(){

        //获取信用卡 类别-6
        $tb_news=M('news');
        $news['lb']=6;
        $data=$tb_news->where($news)->select();


        foreach ($data as $k=>$v){
//            $data[$k]['content']=htmlentities($data[$k]['content']);
            $data[$k]['content']=htmlspecialchars($data[$k]['content']);
        }

        if ($data){
            $ret['error']="success";
            $ret['msg']=1;
            $ret['data']=$data;
            die(json_encode($ret));
        }else{
            $ret['error']="fault";
            $ret['msg']=2;
            $ret['data']=$data;
            die(json_encode($ret));
        }
    }

    //todo 贷款 ZF
    public function loans(){

        //获取咨询 类别-7
        $tb_news=M('news');
        $news['lb']=7;
        $data=$tb_news->where($news)->select();
        foreach ($data as $k=>$v){
//            $data[$k]['content']=htmlentities($data[$k]['content']);
            $data[$k]['content']=htmlspecialchars($data[$k]['content']);
        }
        if ($data){
            $ret['error']="success";
            $ret['msg']=1;
            $ret['data']=$data;
            die(json_encode($ret));
        }else{
            $ret['error']="fault";
            $ret['msg']=2;
            $ret['data']=$data;
            die(json_encode($ret));
        }
    }


    //todo 链接

    public function index(){


        $id=$_GET['id']; //资讯id
        //static $redis;
        $redis=new redis();
        $redis->connect('39.105.87.150');
        $redis->auth('lcf2954626');
        $list=unserialize($redis->get('new_'.$id));

        /*if(empty($list)){
            $tb_news=M('news');
            $news['id']=$id;
            $list=$tb_news->where($news)->find();
            $redis->set('new_'.$id,serialize($list));
            echo "使用mysql";
        }*/

        /*for($i=0;$i<1000000;$i++){
            $list="hello world3333333";
        }*/
        $list="hello world888888888";
        //$redis->set('new_'.$id,serialize($list));
        $this->jsonout("seccess",'链接',$list);

    }
}
