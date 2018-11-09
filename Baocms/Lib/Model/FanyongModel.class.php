<?PHP

class FanyongModel extends CommonModel{



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

    public function fanyong($user_id,$mianis_edu){

        global $current_userid,$globalEdu;
        $current_userid=$user_id;
        $globalEdu=$mianis_edu;

        $distribution=D("distribution");
        $where['ID']='1';
        $line=$distribution->where($where)->find();

        global $numLevel;
        $numLevel=$line['numRen'];
        global $len;
        $len=$numLevel;

        $priceString=$line['price'];
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

        //$sql_pid="select user_id,pid from bao_users where user_id=$curId limit 0,1";
        $users=D('Users');
        $where['user_id']=$curId;
        $line_pid=$users->where($where)->find();


        $next_userid=$line_pid['pid'];//当前上级

        array_push($this->pidAry,$next_userid);

        $this->allPid($next_userid);

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

        //$add_sql="INSERT INTO  bao_fanyong (fabao_id,miansi_edu,fenyong_id,fengyong_edu,Lv) VALUES ($current_userid,$globalEdu,$p1id,$newPrice,$len+1)";

        $fanyong=D('fanyong');
        $data['fabao_id']=$current_userid;
        $data['miansi_edu']=$globalEdu;
        $data['fenyong_id']=$p1id;
        $data['fenyong_edu']=$newPrice;
        $data['Lv']=$len+1;
        $fanyong->add($data);



//$add_go=mysql_query($add_sql) or die("ERROR4");
//	if($add_go){
        $len++;
        $this->addFenYong();

        //}

    }


}

//$a=new FanYong('1675547',100);

?>