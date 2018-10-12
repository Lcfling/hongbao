<?php
require_once LIB_PATH.'/GatewayClient/Gateway.php';
use GatewayClient\Gateway;
class IndexAction extends CommonAction
{
	public function index()
	{
	    //$gameInfo=D('Room')->
        $data=array(
            0=>array(
                'title'=>'扫雷',
                'game'=>'saolei'
            ),
            1=>array(
                'title'=>'接龙',
                'game'=>'jielong'
            )
        );
        $this->ajaxReturn($data,'success',1);









	    $arr=array(
		    'roomid'=>1007,
            'm'=>3,//
            'data'=>array(
                'username'=>'美女',
                'user_id'=>'177777',
                'hongbao_id'=>'38',
                'money'=>'500',
                'bom_num'=>5,
            )
        );
        $arr=array(
            'status'=>1,
            'info'=>'',//
            'data'=>array(
                'type'=>'2',
                'remark'=>'红包过期',
                'hongbao_id'=>'38',
                'money'=>'500',
                'bom_num'=>5,
                'is_selfin'=>'1',
                'selfmoney'=>'128',
                'list'=>array(
                    0=>array(
                        'username'=>'美女',
                        'user_id'=>'177777',
                        'avatar'=>'http://www',
                        'is_robot'=>0,
                        'money'=>'500',
                        'recivetime'=>'16:09:01',
                    ),
                    1=>array(
                        'username'=>'免死',
                        'user_id'=>'177777',
                        'avatar'=>'http://www',
                        'is_robot'=>1,
                        'money'=>'500',
                        'recivetime'=>'16:09:01',

                    )
                ),
            )
        );
		echo json_encode($arr);
		die();

	}
	public function set(){
        Cac()->del('push');
        $ar=array(1,2,3,4,5,6);
        foreach ($ar as $v){
            Cac()->rPush('push',$v);
        }
    }
}