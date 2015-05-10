<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends Controller {
	public function _initialize(){

	    if(!session('?is_login')){
	    	$this->redirect('Login/login');
	    	die();
	    }
  }
    public function index(){
        $this->display();
            
    }
    public function orders(){
        $map['uid'] = session('uid');
        $count =  M('order')
        ->field('dcz_stadium.name as sname,dcz_stadium.address as address')   //dcz_order.usetimes,dcz_order.ordertime,dcz_order.oid,dcz_order.price,dcz_order.fsids
        ->join('dcz_sportstype on dcz_sportstype.stid = dcz_order.stid')
        ->join('dcz_stadium on dcz_sportstype.sid = dcz_stadium.sid')
        ->where($map) -> count();
        $Page       = new \Think\Page($count,C('O_PAGE'));// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $Page ->setConfig('prev','上一页');
        $Page ->setConfig('next','下一页');
        $show       = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $orders = M('order')
        ->field('*,dcz_stadium.name as sname,dcz_stadium.address as address,dcz_stadium.url')   //,dcz_order.usetimes,dcz_order.ordertime,dcz_order.oid,dcz_order.price,dcz_order.fsids
        ->join('dcz_sportstype on dcz_sportstype.stid = dcz_order.stid')
        ->join('dcz_stadium on dcz_sportstype.sid = dcz_stadium.sid')
        ->where($map)->order('ordertime desc')
        ->limit($Page->firstRow.','.$Page->listRows)
        -> select();
        for ($i=0; $i < count($orders) ; $i++) { 
            # code...
            $json = json_decode($orders[$i]['fsids']);
            $orders[$i]['fsids'] = '';
                foreach ($json as $k=>$jns ) {
                  # code...
                  $jn = explode(':', $jns);         //2月1日（周日）8:00-9:00
                  $orders[$i]['fsids'] .= $jn[0] . '号场地:'. (intval($jn[1])%14+8) .':00-' . (intval($jn[1])%14+9) .':00\n';
                }
            $orders[$i]['fsids'] = substr($orders[$i]['fsids'], 0,-2);
        }
        /*$map2['stid'] = $orders['stid'];
        $stadium = M('stadium') -> join('dcz_sportstype on dcz_sportstype.sid = dcz_stadium.sid') -> where($map2) -> find();
        $this -> assign('stadium',$stadium);*/
        $this ->assign('page',$show);// 赋值分页输出
        $this->assign('orders',$orders);
    	$this->display();
    }
    public function wallet(){
    	$this->display();
    }
    public function settings(){
        $uid = session('uid');
        if($_POST){
            $data = array();
            $data['nickname'] = I('post.nickname');
            $data['realname'] = I('post.realname');
            $data['birthday'] = I('post.birthday');
            $data['sex'] = I('post.sex');
            $data['address'] = I('post.address');
            $data['qq'] = I('post.qq');
            if(M('userinfo')->where("uid={$uid}")->count()){
                $res = M('userinfo') -> where("uid={$uid}") -> save($data);
                if($res)
                    $this -> success('修改成功','settings');
                else
                    $this -> error('修改失败');
            }
            else{
                $data['uid'] = $uid;
                $res = M('userinfo') ->add($data);

                if($res)
                    $this -> success('修改成功','settings');
                else
                    $this -> error('修改失败');
            }
        }
        else{           
            $userinfo = M('userinfo') -> where("uid={$uid}") -> find();
            $this->assign('userinfo',$userinfo);
    	   $this->display();
        }
    }
    public function feedback(){
        if($_POST){
            $data['content'] = I('post.contents');
            $data['contact'] = I('post.contact');
            $data['uid'] = session('uid');
            if(M('feedbacks')->add($data))  $this->success('感谢您的提交','/index.php/User');
            else   $this->error('提交失败');
        }
        else{
    	   $this->display();
        }
    }
    public function favorites(){

        $map['uid'] = session('uid');

        $count = M('ufavorites') 

        -> join('dcz_sportstype on dcz_ufavorites.stid = dcz_sportstype.stid')

        -> join('dcz_stadium on dcz_stadium.sid = dcz_sportstype.sid')

        -> where($map) -> count();

        $Page       = new \Think\Page($count,C('F_PAGE'));// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $Page ->setConfig('prev','上一页');
        $Page ->setConfig('next','下一页');
        $show       = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        

        $favorites = M('ufavorites') 

        -> join('dcz_sportstype on dcz_ufavorites.stid = dcz_sportstype.stid')

        -> join('dcz_stadium on dcz_stadium.sid = dcz_sportstype.sid')

        -> where($map)->limit($Page->firstRow.','.$Page->listRows) -> select();

        $this ->assign('page',$show);// 赋值分页输出
        $this->assign('favorites',$favorites);
    	$this->display();
    }
    public function card(){
    	echo "good!";
    }

    public function addFavorites(){

        if(empty($_POST['stid']))
            die();

        $map['stid'] = I('post.stid');

        if( !(M('sportstype') -> where($map) -> count()) )
            die();

        $map['uid'] = session('uid');

        if(M('ufavorites') -> where($map) -> count())
            $this -> ajaxReturn("FALSE");
        else{

            if( M('ufavorites') -> add($map) )
                $this -> ajaxReturn("TRUE");
            else
                $this -> ajaxReturn("FALSE");
        }

    }
}