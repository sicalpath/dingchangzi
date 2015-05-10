<?php
namespace Home\Controller;
use Think\Controller;
class SiteController extends Controller {
    public function index(){
    	$map['stype'] = I('get.stype',1);
        $count      = M('stadium') -> join('dcz_sportstype on dcz_stadium.sid = dcz_sportstype.sid') -> where($map) ->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,C('S_PAGE'));// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $Page ->setConfig('prev','上一页');
        $Page ->setConfig('next','下一页');
        $show       = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $stadiums = M('stadium') ->field('*,stid')-> join('dcz_sportstype on dcz_stadium.sid = dcz_sportstype.sid') -> where($map) ->limit($Page->firstRow.','.$Page->listRows)-> select(); 
        $this ->assign('page',$show);// 赋值分页输出
    	$this ->assign('stadiums',$stadiums);
        $this->display();
            
    }
    public function detail(){
        $where['stid'] = intval(I('get.stid',1));
        $res = M('sportstype') -> where($where) ->find();
            if($res == null){
                //echo M('sportstype')->getLastSql();
                $this->error('没有此场馆');
            }
            else{
            	$map['sid'] = $res['sid'];
                $sports = M('sportstype') -> where($map) -> select();
            	$stadium = M('stadium') -> where($map) -> find();
                $this -> assign('sports',$sports);
                $this -> assign('stype',$res);
                $this -> assign('stid',$where['stid']);
            	$this -> assign('stadium',$stadium);
            	$this->display();
        }
    }
}