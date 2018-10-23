<?PHP
require "connectdb.php";

class FanYong{

//获取分销信息数据
//获取分销级
//匹配字符串，获取分销数额
private $numLevel;
private $globalEdu;//免死额度;
private $priceAry=array();
private $priceString;
private $current_userid;
private $len;
private $pidAry=array();

 function __construct($user_id,$mianis_edu){
 global $current_userid,$globalEdu;
 $current_userid=$user_id;
$globalEdu=$mianis_edu;

$sql_distribution="select * from bao_distribution where ID=1 limit 0,1";
//$sql_distribution=D('distribution')->where('ID',1)->select();
$go_distribution=mysql_query($sql_distribution) or die("ERROR2");
	if($go_distribution){
		 $line=mysql_fetch_assoc($go_distribution);
		 global $numLevel;
		$numLevel=$line['numRen'];
		global $len;
		$len=$numLevel;
		
		$priceString=$line['price'];
	}
global $priceAry;	
$priceAry = explode(",",$priceString);//分销额度存入数组

	$this->allPid($current_userid);
}


//获取uid的所有上级
function allPid($curId){

global $len;

	if($len==0){
		$this->addFenYong();//数据保存
	
	
	return;
	}else{
	$len--;
	}
	
	$sql_pid="select user_id,pid from bao_users where user_id=$curId limit 0,1";
//$sql_pid=D('users')->where('ID',1)->select();
	$go_pid=mysql_query($sql_pid) or die("ERROR3");
		if($go_pid){
		 $line_pid=mysql_fetch_assoc($go_pid);
		 $next_userid=$line_pid['pid'];//当前上级
	
			array_push($this->pidAry,$next_userid);
			
			$this->allPid($next_userid);	
		 }
} 



//对应返佣额度
private function addFenYong(){

global $len,$pidAry,$numLevel,$priceAry,$globalEdu,$current_userid;

	if($len>($numLevel-1)){	
	
	return;
	}else{
	

	}

$newPrice=$priceAry[$len]/100;
$p1id=$this->pidAry[$len];

$add_sql="INSERT INTO  bao_fanyong (fabao_id,miansi_edu,fenyong_id,fengyong_edu,Lv) VALUES ($current_userid,$globalEdu,$p1id,$newPrice,$len+1)";

$add_go=mysql_query($add_sql) or die("ERROR4");
	if($add_go){
	$len++;
	$this->addFenYong();
	
	}

}


}

$a=new FanYong('1675547',100);

?>