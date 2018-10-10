<?php
error_reporting(E_ALL);

$starttime="2018-09-06";


/*
 * 创建任务列表
 *
 * @param $starttime format "2018-09-06"
 *
 * @param int $days  分几天还清
 *
 * @param int $money 总金额  必须为正整数
 *
 * return array  任务列表
 */

function feilv($y){
    return ($y+3)/(1-0.005);
}


/*
 * 创建任务列表
 *
 * @param int $money 总金额  必须为正整数
 *
 * return array  任务列表
 */
function creatTaskList2($money,$over){
    $times=(int)($money/$over);
    $verage=number_format($money/$times,2) ;
    $starttime=date('Y-m-d', strtotime(date('Y-m-d',time())." +1 day"));
    if($times<2){
        $tem['datetime']=strtotime($starttime." 08:00:00")+rand(1,7200);
        $tem['money']=$money;
        $result[]=$tem;
        return $result;
    }
    for($i=1;$i<=$times;$i++){

        switch ($i%4){
            case 1:
                $tem['datetime']=strtotime($starttime." 08:00:00")+rand(1,7200);
                $tem['money']=$verage-20+rand(1,4000)/100;
                $result[]=$tem;
                break;
            case 2:
                $tem['datetime']=strtotime($starttime." 10:00:00")+rand(1,7200);
                $tem['money']=$verage-20+rand(1,4000)/100;;
                $result[]=$tem;
                break;
            case 3:
                $tem['datetime']=strtotime($starttime." 14:00:00")+rand(1,7200);
                $tem['money']=$verage-20+rand(1,4000)/100;;
                $result[]=$tem;
                break;
            case 0:
                $tem['datetime']=strtotime($starttime." 16:00:00")+rand(1,7200);
                $tem['money']=$verage-20+rand(1,4000)/100;;
                $result[]=$tem;
                $starttime=date('Y-m-d', strtotime($starttime." +1 day"));
                break;
        }
    }
    $all=0;
    for($j=0;$j<$times-1;$j++){
        $all=$all+$result[$j]['money'];
    }
    $result[$times-1]['money']=$money-$all;
    return $result;
}


/*foreach ($r as &$vl){
    $vl=date("Y-m-d H:i:s",$vl);
}*/

$money=$_GET['money'];
$r=creatTaskList2($money,450);
foreach ($r as &$value){
    $value['datetime']=date('Y-m-d H:i:s', $value['datetime']);
}
print_r($r);
?>