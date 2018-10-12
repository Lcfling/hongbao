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

	}
}