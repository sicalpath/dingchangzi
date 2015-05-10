<?php
namespace Home\Controller;
use Think\Controller;
use Common\Lib\Alipay\Alipay;

class PayController extends Controller {
    public function _initialize(){
        
    }
    public function index(){
        if(!session('?is_login')){
            $this->ajaxReturn('0');
            die();
        }
        else
            if(!session('?phone')){
                $this->ajaxReturn('1');
                die();
            }    
    }
    public function pay(){
        if(!session('?is_login')){
            $this->error('没有登录');
            die();
        }
    	$this->display();
    }
    public function askpay(){
        if(!session('?is_login')){
            $this->error('没有登录');
            die();
        }
        if(I('post.orders'))   {
            $verify = new \Think\Verify();
            if( !($verify->check(I('post.checkcode'))) ){
                $this->error('验证码错误');
            }

            $map['phone'] =  session('phone');  //I('post.phone','~');

            $map['name'] = I('post.name','佚名');

            $map['price'] = 0;

            $orders = I('post.orders');

            $stid = I('post.stid');

            if(count($orders) > 5){
                echo "所选场次过多";
                die();
            }
            $oid = 'F'.$map['uid'].NOW_TIME;
            foreach ($orders as $order) {
                # code...
                $order = explode(":",$order);
                $data['fid'] = intval($order[0])-1;
                $data['fno'] = $order[1];
                $data['stid'] = $stid;
                $data['locked'] = 0;
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
                        $data2['oid'] = $oid;
                        $data2['status'] = '0' ; //状态 0为未付款 1为付款未消费 2为已消费 3为已退 ……
                        $res = M('fstatus') -> add($data2);
                        if(!$res){
                            $this->error('锁定错误');
                        }
                   }
               }
            }
            $map['usetimes'] = I('post.usetime');

            $map['stid'] = $stid;

            $map['uid'] = session('uid');
            
            $map['fsids'] = json_encode($orders);

            $map['oid'] = $oid ;        //生产订单编号

            $map['status'] = '0' ;        //订单状态 0为未付款 1为已付款 2为已消费完成

            $res = '';

            $res = M('order') -> add($map);
            
            if($res){
                $st['stid'] = $stid;
                $stadium = M('sportstype') -> join('dcz_stadium on dcz_stadium.sid = dcz_sportstype.sid') -> where($st) -> find();
                $map['id'] = $res;
                $order = M('order') -> where($map) -> find();
                $this->assign('stadium',$stadium);
                $this->assign('order',$order);
                $this->display();
            }
            else
                $this->error("sth wrong");
        }
        else{
            $this ->error('没有订单数据');
        }
}
    public function success(){
    	$this->display();
    }

    public function alipay()
    {
        /**
         * 需要修改！
         * 写自己的业务逻辑（获取POST过来的订单那数据）~或把此方法集成到其他控制器（比如说Buycontroller）中。
         */
        $map['oid'] = I('get.oid',1);
        $order = M('order') -> where($map) -> find();
        if(!$order||$order['status']==-1){
            $this->error('没有订单信息或已过期');
            die();
        }
        $alipayp['total_fee'] = '0.01';//$order['price'];//订单总金额
        $alipayp['out_trade_no'] = $map['oid'];//商户订单ID
        $alipayp['subject'] = '预定场地费用';//订单商品标题
        $alipayp['body'] = '预定场地费用';//订单商品描述
        $alipayp['show_url'] = '';//订单商品地址
        $alipay = new alipay();
        $alipay->toAlipay($alipayp);
    }

    public function alipayReturn()
    {

        if (empty($_GET) ) {
            $this->error('您查看的页面不存在');
        }
        $alipay = new alipay();
        if ( !$alipay->isAlipay($_GET) ) {
            $this->error('验证失败请不要做违法行为！');
        }
        $alipay_no = I('get.trade_no');
        $order_id = I('get.out_trade_no');
        $status = I('get.trade_status');
        /**
         * 这里需要修改。！！！
         * --------------------------
         * 写出自己的业务逻辑。
         * 从数据库中获取订单信息，然后判断订单状态是否经过处理什么的！！
         * ------------------
         */
        
            if ( $status == 'TRADE_FINISHED' || $status == 'TRADE_SUCCESS') {
                /**
                 * 这里写出更新订单状态等的业务逻辑
                 */
                $map['oid'] = $order_id;
                $data['status'] = 1;
                $fst = M('fstatus') -> where($map) -> save($data);
                $data['verify'] = rand(100000,999999);
                $res = M('order') -> where($map) -> save($data);
                if(!$res){
                    echo "出现故障，请联系客服";
                }
                else{//发送预订成功验证码
                    $order = M('order') ->field('dcz_order.*,dcz_stadium.name as sname')
                    ->join('dcz_sportstype on dcz_order.stid = dcz_sportstype.stid') 
                    ->join('dcz_stadium on dcz_sportstype.sid = dcz_stadium.sid')
                    -> where($map) -> find();
                    $this->send($order);
                }
            } else {
                /**
                 * 应该是hacking行为了
                 */
                $this->error('求您高抬贵手');
            }
        $alipay->logResult($order['verify']);
        $st['stid'] = $order['stid'];
        $stadium = M('sportstype') -> join('dcz_stadium on dcz_stadium.sid = dcz_sportstype.sid') -> where($st) -> find();
        $this->assign('stadium',$stadium);
        $this->assign('order',$order);
        $this->display('success');
    }

    public function alipayNotify(){

        if ( empty($_POST) ) {
            $this->error('您查看的页面不存在');
            die();
        }
        $alipay = new alipay();
        if ( !$alipay->isAlipay($_POST) ) {
            $this->error('请不要做违法行为！');
            die();
        }
        $alipay_no = I('post.trade_no');
        $order_id = I('post.out_trade_no');
        $status = I('post.trade_status');

        if($status == 'TRADE_FINISHED') {
        //判断该笔订单是否在商户网站中已经做过处理
            //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
            //如果有做过处理，不执行商户的业务程序

           $map['oid'] = $order_id;
                $data['status'] = 1;
                $fst = M('fstatus') -> where($map) -> save($data);
                $data['verify'] = rand(100000,999999);
                $res = M('order') -> where($map) -> save($data);
                if(!$res){
                    echo "fail";
                    die();

                }
                else{//发送预订成功验证码
                    $order = M('order') ->field('dcz_order.*,dcz_stadium.name as sname')
                    ->join('dcz_sportstype on dcz_order.stid = dcz_sportstype.stid') 
                    ->join('dcz_stadium on dcz_sportstype.sid = dcz_stadium.sid')
                    -> where($map) -> find();
                    $this->send($order);
                    echo 'success';
                    die();

                }
                
        //注意：
        //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知

        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
        else if ($status == 'TRADE_SUCCESS') {
        //判断该笔订单是否在商户网站中已经做过处理
            //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
            //如果有做过处理，不执行商户的业务程序
                $map['oid'] = $order_id;
                $data['status'] = 1;
                $fst = M('fstatus') -> where($map) -> save($data);
                $data['verify'] = rand(100000,999999);
                $res = M('order') -> where($map) -> save($data);
                if(!$res){
                    echo "fail";
                    die();

                }
                else{//发送预订成功验证码
                    $order = M('order') ->field('dcz_order.*,dcz_stadium.name as sname')
                    ->join('dcz_sportstype on dcz_order.stid = dcz_sportstype.stid') 
                    ->join('dcz_stadium on dcz_sportstype.sid = dcz_stadium.sid')
                    -> where($map) -> find();
                    $this->send($order);
                    echo 'success';
                    die();

                }
                
        //注意：
        //付款完成后，支付宝系统发送该交易状态通知

        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
              }

        //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
        
           //请不要修改或删   
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    else {
        //验证失败
        echo "fail";
        die();
        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }
    
}

    function send($order){
        //初始化必填
        static $count = 0; //短信发送计数
        $options['accountsid']='8b64ca2f51e26f083195e11b79bf268d'; //填写自己的
        $options['token']='4bedcf366079e8ded75883025652e868'; //填写自己的
        //初始化 $options必填
        $ucpass = new \Org\Util\Ucpaas($options);
        
        //短信验证码（模板短信）,默认以65个汉字（同65个英文）为一条（可容纳字数受您应用名称占用字符影响），超过长度短信平台将会自动分割为多条发送。分割后的多条短信将按照具体占用条数计费。
        $appId = "1d78a1f4dfd34288b9621f6f2f9efbc8";  //填写自己的
        $templateId = "5459";

        $msg = $order['name'].',';
        $msg .= $order['sname'].',';
        $msg .= $order['usetimes'].',';
        $json = json_decode($order['fsids']);
            foreach ($json as $k=>$jns ) {
                # code...
                $jn = explode(':', $jns);         //2月1日（周日）8:00-9:00
                $msg .= $jn[0] . '号场地:'. (intval($jn[1])%14+8) .':00-' . (intval($jn[1])%14+9) .':00;';
            }
        $msg .= ','.$order['verify'];

        $arr=$ucpass->templateSMS($appId,$order['phone'],$templateId,$msg);
        if (substr($arr,21,6) != '000000') {
            //如果不成功就，这里只是测试样式，可根据自己的需求进行调节
            ++$count;
            if($coumt < 3)
                $this->send($order);
            else
                die();
        }
        
        
    }
    function logResult($word='') {
        $fp = fopen("log.txt","a");
        flock($fp, LOCK_EX) ;
        fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".$word."\n");
        flock($fp, LOCK_UN);
        fclose($fp);
        }

    public function verify(){
        $config =    array(
            'fontSize'    =>    30,    // 验证码字体大小
            'length'      =>    4,     // 验证码位数
            'useNoise'    =>    false, // 关闭验证码杂点
        );
        $Verify = new \Think\Verify($config);
        $Verify->entry();

    }
}