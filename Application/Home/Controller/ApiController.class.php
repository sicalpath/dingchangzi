<?php
namespace Home\Controller;
use Think\Controller;
class ApiController extends Controller {

    public function _initialize(){

  }

    public function index(){
    	echo "";   
        }

    function getStatus(){
        $fid = I("get.fid");
        $ftime = I("get.ftime");
        $arrayName = array('status' => rand(0,1) , 'fid' => 1  ,'price' => '60');
    	echo json_encode($arrayName);
        }
    function getStatus2(){
        $arrayName = array();
        $weekday = date('w',strtotime(I("get.date")));      //0为星期日 6为星期六
        $map['stid'] = I('get.stid');
        $where['uid'] = session('uid');
        $where['dcz_bindtosch.stid'] = I('get.stid');
        $utype = M('bind') -> join('dcz_bindtosch on dcz_bind.schid = dcz_bindtosch.schid',LEFT) -> where($where) -> getField('utype');
        $map['fid'] = intval(I('get.fid')) - 1;
        $map['utype'] = $utype ? $utype : 0; 
        $fids = M('field') -> join ('dcz_fstatus on dcz_field.ffid = dcz_fstatus.ffid and ftime =\''.I("get.date").'\'','LEFT')-> where($map) -> order('fno') -> select();
        $j = 0;
        for ($i= 14 * $weekday; $i < 14 * ($weekday + 1) ; $i++) { 
            //echo $i;
            $arrayName[] = array('price' => substr($fids[$i]['price'],0,2) );
        }
        for ($k = 0 ; $k < 3; $k++) { 
            $map['utype'] = $k;
            $fids = M('field') -> join ('dcz_fstatus on dcz_field.ffid = dcz_fstatus.ffid and ftime =\''.I("get.date").'\'','LEFT')-> where($map) -> order('fno') -> select();
            $j = 0;
            for ($i= 14 * $weekday; $i < 14 * ($weekday + 1) ; $i++) { 
                //...............................
                //$arrayName[$j] =  ( !isset($arrayName[$j]) || $fids[$i]['status'] == '0' ) ? (array('status' => 0 ,'price' => substr($fids[$i]['price'],0,2) )) : $arrayName[$j];
                ( $fids[$i]['status'] == '0' && ( $arrayName[$j]['status'] = 0 ) ) || ( !isset($arrayName[$j]['status']) && ( $arrayName[$j]['status'] = 1 )); 
                $j++;
            }
        }
        echo json_encode($arrayName);
      }
    function getDateStatus(){
        $weekday = date('w',strtotime(I("get.date"))) ;
        $arrayName = array();
        $map['stid'] = I('get.stid');
        $where['uid'] = session('uid');
        $where['dcz_bindtosch.stid'] = I('get.stid');
        $utype = M('bind') -> join('dcz_bindtosch on dcz_bind.schid = dcz_bindtosch.schid',LEFT) -> where($where) -> getField('utype');
        $map['utype'] = $utype ? $utype : 0; 
        $fnum = M('field') -> count('distinct fid');        
        for ($i=0; $i < $fnum ; $i++) { 
            $map['fid'] = $i;
            $fids = M('field') -> join ('dcz_fstatus on dcz_field.ffid = dcz_fstatus.ffid','LEFT')-> where($map) -> order('fno') -> select();
            //$arrayName[$i]['fid'] = $i + 1;
            for ($j= 14 * $weekday; $j < 14 * ($weekday +1) ; $j++) { 
                # code...
                $arrayName[$i][] = array('ftime' => $j , 'status' => $fids[$j]['status'] ? $fids[$j]['status'] : 1 , 'price' => $fids[$j]['price'] ,'fid' => $i + 1);
            }
            
        }
        //dump($arrayName);
        echo json_encode($arrayName);
          
    }
    function verify(){

        }
    public function askpay(){
        if(I('post.orders'))   {

        $map['phone'] = I('post.phone');

        $map['name'] = I('post.name');

        $map['price'] = 0;

        $orders = I('post.orders');

        $stid = I('post.stid');
        
        foreach ($orders as $order) {
            # code...
            $order = explode(":",$order);
            $data['fid'] = $order[0];
            $data['fno'] = $order[1];
            $data['stid'] = $stid;
            $where['uid'] = session('uid');
            $where['dcz_bindtosch.stid'] = I('get.stid');
            $utype = M('bind') -> join('dcz_bindtosch on dcz_bind.schid = dcz_bindtosch.schid',LEFT) -> where($where) -> getField('utype');
            $data['utype'] = $utype ? $utype : 0; 
            $field = M('field') -> where($data) -> find();
            if($field == null){
                $this -> error('没有该场地(时间)');
                die();
            }
            else{
                $map['price'] += $field['price'];
                $data2['ffid'] =  $field['ffid'];
                $data2['ftime'] = I('post.usetime');
                $is_exist = M('fstatus') -> where ($data2) -> find();
                if($is_exist != null){
                    $this -> error('存在已被预订时间');
                    die();
               }
               else{
                   $data2['status'] = '0' ; //状态 0为未付款 1为付款未消费 2为已消费 3为已退 ……
                   $res = M('fstatus') -> add($data2);
                   if(!$res){
                    $this->error('锁定错误');
                   }
               }
           }
        }

        $map['stid'] = $stid;

        $map['uid'] = session('uid');
        
        $map['fsids'] = json_encode($orders);

        $map['oid'] = 'F'.$map['uid'].NOW_TIME ;        //生产订单编号

        $map['status'] = '0' ;        //订单状态 0为未付款 1为已付款 2为已消费完成

        $res = '';

        $res = M('order') -> add($map);
        
        if($res){
            $this->assign('order',$map);
            $this->display();
        }
        else
            $this->error("sth wrong");
    }
        else{
            $this ->error('没有订单数据');
        }
    }



    function testOrder(){
        dump($_POST);
        $orders = array();

        $orders = I('post.orders');

        dump($orders);
    }
    function bind(){

        $map['code'] = I('post.verify');
        $map['verify'] = I('post.phone');
        if( M('verifys') -> where($map) ->count()){
            $data['phone'];
            if(M('user')->where('uid='.session('uid'))->save($data)){
                session('phone',$data['phone']);
                $this -> ajaxReturn("true");
            }
            else
                $this -> ajaxReturn("false");
        }
        else
            $this -> ajaxReturn("false");
    }
}