<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/22
 * Time: 15:12
 */
    //todo 海报 zf
require_once LIB_PATH.'Net/grafika-master/src/autoloader.php';
use Grafika\Grafika;

use Grafika\Color;
class HaibaoAction extends CommonAction {


//todo 推广海报
  public function tghaibao(){

      require_cache(APP_PATH.'Lib/phpqrcode/phpqrcode.php');

            $ID=$_GET['ID'];
            $i=$_GET['i'];

//                $randpwd=$_GET['randpwd'];
//             $this->login_verify($ID,$randpwd);







            $tb_user=M('user');
            $user['ID']=$ID;
            $list=$tb_user->where($user)->find();

            if ( strlen($list['nickName'])  >9){
               $nickName= substr($list['nickName'],0,9)."...";
            }else{
                $nickName=$list['nickName'];
            }

      $value= $url = "http://qp.webziti.com/mobile/saoma/index?ID=".$ID."&daili_id=".$list['daili_id']."&url=tg";					//二维码内容
          $errorCorrectionLevel = 'L';	//容错级别
          $matrixPointSize = 7;//生成图片大小
         header('Content-type: image/png');
            $url="./erweima/".$ID.".png";
      //生成二维码图片
           QRcode::png($value,$url,$errorCorrectionLevel, $matrixPointSize,2);

      //生成海报
        $editor = Grafika::createEditor();
          $editor->open($image1 , './tghaibao/bg'.$i.'.jpg'); //背景海报
          $editor->open($image2 ,  $url); // 二维码代码
          $editor->blend ( $image1, $image2 , 'normal', 1, 'center',0,350);//拼接成海报
          $editor->text($image1 ,"业务经理:".$nickName,20,220,1066,new Color("#000000"),LIB_PATH.'Net/grafika-master/fonts/songti.TTF',0);//打印文字
          $editor->text($image1 ,'长按识别二维码',20,220,1100,new Color("FFF"),LIB_PATH.'Net/grafika-master/fonts/songti.TTF',0);//打印文字
          header('Content-type: image/jpeg');
          $image1->blob('jpeg');

  }

  //推广海报列表
  public function getlist_tg(){
       $ID= $_GET['ID'];
    for ($i=1;$i<7;$i++){
    $url[]['hburl']="http://qp.webziti.com/mobile/haibao/tghaibao?ID=".$ID."&i=".$i;
}
   // print_r($url);
      $this->jsonout('success','推广海报',$url);
  }

//todo 贷款海报
    public function dkhaibao(){

        require_cache(APP_PATH.'Lib/phpqrcode/phpqrcode.php');

        $ID=$_GET['ID'];
        $i=$_GET['i'];
        $tb_user=M('user');
        $user['ID']=$ID;
        $list=$tb_user->where($user)->find();

        if ( strlen($list['nickName'])  >9){
            $nickName= substr($list['nickName'],0,9)."...";
        }else{
            $nickName=$list['nickName'];
        }

        $value= $url = "http://qp.webziti.com/mobile/saoma/index?ID=".$ID."&daili_id=".$list['daili_id']."&url=dk".$i;					//二维码内容
        $errorCorrectionLevel = 'L';	//容错级别
        $matrixPointSize = 7;//生成图片大小
        header('Content-type: image/png');
        $url="./erweima/".$ID.".png";
        //生成二维码图片
        QRcode::png($value,$url,$errorCorrectionLevel, $matrixPointSize,2);

        //生成海报
        $editor = Grafika::createEditor();
        $editor->open($image1 , './dkhaibao/bg'.$i.'.jpg'); //背景海报
        $editor->open($image2 ,  $url); // 二维码代码
        $editor->blend ( $image1, $image2 , 'normal', 1, 'center',0,250);//拼接成海报
        $editor->text($image1 ,"业务经理:".$nickName,20,220,880,new Color("#000000"),LIB_PATH.'Net/grafika-master/fonts/songti.TTF',0);//打印文字
        $editor->text($image1 ,'长按识别二维码',20,220,930,new Color("#000000"),LIB_PATH.'Net/grafika-master/fonts/songti.TTF',0);//打印文字
        header('Content-type: image/jpeg');
        $image1->blob('jpeg');

    }
//贷款海报列表
  public function getlist_dk(){

      $ID=$_GET['ID'];
      $i=$_GET['i'];
      $tb_user=M('user');
      $user['ID']=$ID;
      $list=$tb_user->where($user)->find();
      $url = "http://qp.webziti.com/mobile/saoma/index?ID=".$ID."&daili_id=".$list['daili_id']."&url=dk".$i;
      $this->jsonout('success','贷款链接',$url);
  }

//todo 信用卡海报

    public function xyhaibao(){

        require_cache(APP_PATH.'Lib/phpqrcode/phpqrcode.php');

        $ID=$_GET['ID'];
        $i=$_GET['i'];
        $tb_user=M('user');
        $user['ID']=$ID;
        $list=$tb_user->where($user)->find();

        if ( strlen($list['nickName'])  >9){
            $nickName= substr($list['nickName'],0,9)."...";
        }else{
            $nickName=$list['nickName'];
        }

        $value= $url = "http://qp.webziti.com/mobile/saoma/index?ID=".$ID."&daili_id=".$list['daili_id']."&url=xy".$i;					//二维码内容
        $errorCorrectionLevel = 'L';	//容错级别
        $matrixPointSize = 7;//生成图片大小
        header('Content-type: image/png');
        $url="./erweima/".$ID.".png";
        //生成二维码图片
        QRcode::png($value,$url,$errorCorrectionLevel, $matrixPointSize,2);

        //生成海报
        $editor = Grafika::createEditor();
        $editor->open($image1 , './xyhaibao/bg'.$i.'.jpg'); //背景海报
        $editor->open($image2 ,  $url); // 二维码代码
        $editor->blend ( $image1, $image2 , 'normal', 1, 'center',0,250);//拼接成海报
        $editor->text($image1 ,"业务经理:".$nickName,20,220,880,new Color("#000000"),LIB_PATH.'Net/grafika-master/fonts/songti.TTF',0);//打印文字
        $editor->text($image1 ,'长按识别二维码',20,220,930,new Color("#000000"),LIB_PATH.'Net/grafika-master/fonts/songti.TTF',0);//打印文字
        header('Content-type: image/jpeg');
        $image1->blob('jpeg');

    }
 // 信用卡海报列表
    public function getlist_xy(){
        $ID=$_GET['ID'];
        $i=$_GET['i'];
        $tb_user=M('user');
        $user['ID']=$ID;
        $list=$tb_user->where($user)->find();
        $url = "http://qp.webziti.com/mobile/saoma/index?ID=".$ID."&daili_id=".$list['daili_id']."&url=xy".$i;

        $this->jsonout('success','贷款链接',$url);
    }


}



