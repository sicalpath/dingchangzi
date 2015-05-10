<?php
namespace Home\Controller;
use Think\Controller;
class OauthController extends Controller {
/* 
* Type类型，初始化
* QQConnet  WeiboConnect 
*/
    public function index(){
    switch ($_GET['type']) {
    /* QQ互联登录 */
    case 'qq':
        $app_id = C('QQ_AUTH.APP_ID');
        $scope = C('QQ_AUTH.SCOPE');
        $callback = C('QQ_AUTH.CALLBACK');
        $sns = new \Org\Util\QQConnect;
        $sns->login($app_id, $callback, $scope);
    break;
    /* 新浪微博登录 */
    case 'sina':
	    $app_id = C('SINA_AUTH.APP_ID');
	    $scope = C('SINA_AUTH.SCOPE');
	    $callback = C('SINA_AUTH.CALLBACK');
	    $sns = new \Org\Util\WeiboConnect;
	    $sns->login($app_id, $callback, $scope);
    break;
    /* 默认无登录 */
    default:
    	$this->error("无效的第三方方式",U('/login/login'));
    break;
    }
    }  
      /*    
       * 互联登录返回信息
       * 获取code 和 state状态，查询数据库 
       *  */
 public function callback() {
    switch ($_GET['type']) {
    /* 接受QQ互联登录返回值 */
    case 'qq':
        empty($_GET['code']) && $this->error("无效的第三方方式",U('/login/login'));
        $app_id = C('QQ_AUTH.APP_ID');
        $app_key = C('QQ_AUTH.APP_KEY');
        $callback = C('QQ_AUTH.CALLBACK');
        $qq = new \Org\Util\QQConnect;
        /* callback返回openid和access_token */
        $back = $qq->callback($app_id , $app_key, $callback);
                        //防止刷新
        empty($back) && $this->error("请重新授权登录",U('/login/login'));
        //此处省略数据库查询，查询返回的$back['openid']
        $this -> dologin($qq,$back,'qq');
 	break;
     
    /* 接受新浪微博登录返回值     */
    case 'sina':
        empty($_GET['code']) && $this->error("无效的第三方方式",U('/login/login'));
        $app_id = C('SINA_AUTH.APP_ID');
        $app_key = C('SINA_AUTH.APP_KEY');
        $scope = C('SINA_AUTH.SCOPE');
        $callback = C('SINA_AUTH.CALLBACK');
        $weibo = new \Org\Util\WeiboConnect;
        /* callback返回openid和access_toke */
        $back = $weibo->callback($app_id , $app_key, $callback);
        empty($back) && $this->error("请重新授权登录",U('/login/login'));
        //此处省略数据库查询，查询返回的$back['openid']
        $this -> dologin($weibo,$back,'sina');
    break;
               /* 默认错误跳转到登录页面  */
    default:
        $this->error("无效的第三方方式",U('/login/login'));
    break;
    }
    }

    private function dologin($mo,$back,$type='qq'){
        switch($type){
            case 'qq':
                $where[$type] = $back['openid'];
                $data['openid'] = $back['openid'];
                $res = M('oauth')->where($where)->find();
                $User = new \Home\Model\UserModel();
                if($res){        
                    $User->loginsuccess($res['uid']);
                    $this->redirect('/');
                }
                else{
                    $user_info = $mo->get_user_info($app_id ,$back['token'],$back['openid']);
                    if($user_info['ret']<0) {
                        echo $user_info['msg'];
                        die;
                    }
                    $data['username'] = $user_info['nickname'];
                    while(1){
                        $i = 1;
                        if( $User->check_exist($data) ){
                            $data['username'] = $user_info['nickname'] . $i++;
                            continue;
                        }
                        else
                            break;                    
                    }
                    $data['password'] = rand(100000,999999);
                    $res2 = $User->add($data,$type);
                    if($res2){
                        $this -> assign('username',$data['username']);
                        $this -> assign('password',$data['password']);
                        $this -> display('Login:osuccess');
                    }
                }
            break;

            case 'sina':

                $where[$type] = $back['openid'];
                $data['openid'] = $back['openid'];
                $res = M('oauth')->where($where)->find();
                $User = new \Home\Model\UserModel();
                if($res){        
                    $User->loginsuccess($res['uid']);
                    $this->redirect('/');
                }
                else{
                    $user_info = $mo->get_user_info($app_id ,$back['token'],$back['openid']);
                    if($user_info['error_code']) {
                        echo 'error';
                        die;
                }
                $data['username'] = $user_info['name'];
                while(1){
                   $i = 1;
                    if( $User->check_exist($data) ){
                        $data['username'] = $user_info['name'] . $i++;
                        continue;
                    }
                    else
                        break;

                }
                $data['password'] = rand(100000,999999);
                $res2 = $User->add($data,$type);
                    if($res2){
                        $this -> assign('username',$data['username']);
                        $this -> assign('password',$data['password']);
                        $this -> display('Login:osuccess');
                    }
                    else
                        echo 'error';
                }
            break;

            default:
                $this->error('啊');
            break;
        }

            
    }
}	 