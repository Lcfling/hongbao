<?php
class RoomModel extends CommonModel
{
    protected $pk = 'id';
    protected $tableName = 'room';


    //获取房间信息
    public function getroom($roomid)
    {
        $data=unserialize(Cac()->get('room_'.$roomid));
        if(empty($data)){
            $data=$this->find();
        }
    }
    /*获取房间列表 全部或者某一个类型
     *@param $type
     *
     */
    public function getroomlist($type=''){

    }
    //保存信息房间
    public function saveroom($data = '', $options = array())
    {

    }


    /**更改房间信息 房间号
     * @param $id
     */
    public function updateroom($id){

    }

    /**更新房间列表的缓存
     *
     */
    protected function updateroomlist(){

    }
}