<?php
namespace Home\Controller;
use Think\Controller;
class SearchController extends Controller {
    public function index(){
    	$keywords = explode(' ',I('post.keywords'));
    	//dump($keywords);
    	if(empty($keywords[0])){
    		echo "no keywords!";
    		die();
    	}
    	else{
    		$map = array();
	    	$sql = "array(";
		    foreach ($keywords as $keyword) {
		   		$sql .= 'array("like","%'.$keyword.'%"),';
		   	}
		   	$sql .= "'and');";
		   	eval("$"."map['name']=".$sql);
		   }
        $count = M('stadium') -> where($map) -> join('dcz_sportstype on dcz_stadium.sid = dcz_sportstype.sid')-> count();
        $Page       = new \Think\Page($count,C('S_PAGE'));// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $Page ->setConfig('prev','上一页');
        $Page ->setConfig('next','下一页');
        $show       = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $stadiums = M('stadium') ->field('*,stid')-> join('dcz_sportstype on dcz_stadium.sid = dcz_sportstype.sid') -> where($map) ->limit($Page->firstRow.','.$Page->listRows)-> select(); 
        $this ->assign('page',$show);// 赋值分页输出
    	$this ->assign('stadiums',$stadiums);
        $this->display();
    }
}