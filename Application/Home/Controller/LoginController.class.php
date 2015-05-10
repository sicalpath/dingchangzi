<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller {



    public function index(){
        $this->display();
            
    }
    public function login(){        //ERROR CODE 0 成功 1 手机号或密码不能为空 2 手机号码不正确 3 账号或密码不正确 
        //待model
    	if($_POST){
          $usr = I('post.phone');
          $pwd = I('post.password');
          if(empty($usr)||empty($pwd)){
              $this->ajaxReturn("1");
            }
          else{
              if(!preg_match('/([0-9]{11})/',I('post.phone'))){
                $this->ajaxReturn("2");
                die();
              }
          }
        $map['phone'] = I('post.phone');
        $who = M('user') -> field('dcz_user.*,dcz_userinfo.nickname,dcz_bind.utype')->join('dcz_userinfo on dcz_userinfo.uid = dcz_user.uid','LEFT')
        ->join('dcz_bind on dcz_user.uid = dcz_bind.uid','LEFT')
        -> where($map) ->find();
        if($who['password'] == I('post.password')){

          $User = new \Home\Model\UserModel();
          $User->loginsuccess($who['uid']);
          /*session('phone',$who['phone']);
          session('uid',$who['uid']);
          session('name',$who['nickname']);
          session('is_login',1);*/
          $this->ajaxReturn("0");
          //$this->success('登录成功',I('post.reurl','/index.php'));
        }
        else{
          session(null);
          $this->ajaxReturn("3");
          //$this->error('登录失败,请检查账号或密码');
        }
    	}
    	else{
        $this->assign('reurl',I('server.HTTP_REFERER'));
    		$this->display();
    	}
    }
    public function reg(){
        if($_POST){
          if(empty(I('post.phone'))||empty(I('post.password'))){
            $this->error("手机号或密码不能为空");
          }
          else{
            if(!preg_match('/([0-9]{11})/',I('post.phone'))){
                $this->error("手机号码不正确");
                die();
            }
            $data['phone'] = I('post.phone');
            if(M('user')->where($data)->find())
                $this->error("已被注册");
            $code['phone'] = I('post.phone');
            $code['code'] = I('post.code');
            if(M('verifys')->where($code)->find()){
              $data['phone'] = I('post.phone');
              
              $data['password'] = I('post.password');
              $res = M('user') -> add($data);
              if($res){
                M('verifys') -> where($code) -> delete();
                session('phone', $data['phone']);
                session('uid', $res);
                session('nickname','佚名');
                session('is_login',1);
                session('utype',0);
                $this -> display('Login:success');
              }
              else{
                $this -> error("失败");
              }            
            }
            else{
              $this->error('验证码不正确');
            }
          }
        }
        else{

            $this->display("");
        }
    }
    public function success1(){
    	$this->display();
    }
    public function logoff(){
       session(null);
       //echo "<script>alert('注销成功');window.location='/index.php'</script>";
       $this->redirect('Index/index');
       //$this->success('注销成功','/index.php');
    }

    public function send(){
        if(!preg_match('/([0-9]{11})/',I('post.to'))){
                echo '手机号码不正确';
                die();
        }
        $action = I('post.action');
        if($action=="0"){
          $map['phone'] = I('post.to');
          if(M('user')->where($map)->count()) echo '手机号已被绑定其他账号';
        }
        //初始化必填
        $options['accountsid']='8b64ca2f51e26f083195e11b79bf268d'; //填写自己的
        $options['token']='4bedcf366079e8ded75883025652e868'; //填写自己的
        //初始化 $options必填
        $ucpass = new \Org\Util\Ucpaas($options);

        //短信验证码（模板短信）,默认以65个汉字（同65个英文）为一条（可容纳字数受您应用名称占用字符影响），超过长度短信平台将会自动分割为多条发送。分割后的多条短信将按照具体占用条数计费。
        $appId = "1d78a1f4dfd34288b9621f6f2f9efbc8";  //填写自己的
        $to = I('post.to');
        $templateId = "5452";
        $param=rand(100000,999999);
        $data['phone'] = $to;
        $data['code'] = $param;

        $param=rand(100000,999999);
        $arr=$ucpass->templateSMS($appId,$to,$templateId,$param);
        if (substr($arr,21,6) == '000000') {
            //如果成功就，这里只是测试样式，可根据自己的需求进行调节
            $res = M('verifys') -> add($data);
            echo "短信验证码已发送成功，请注意查收短信";
            
        }else{
            //如果不成功
            echo "短信验证码发送失败，请联系客服";
            
        }
        
        
    }

    public function verify(){
        $Verify = new \Think\Verify();
        dump($Verify);
        $Verify->entry();
        //$image -> buildImageVerify();
    }
}