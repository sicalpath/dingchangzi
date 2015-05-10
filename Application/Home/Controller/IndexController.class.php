<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
    	$stadiums = M('stadium') ->field('*,stid')-> join('dcz_sportstype on dcz_stadium.sid = dcz_sportstype.sid') ->limit('0,8')-> select(); 
        $this ->assign('stadiums',$stadiums);
        $this->display();
            
    }
}