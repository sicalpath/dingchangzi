<?php
//用户model 	by sicalpath
namespace Home\Model;
use Think\Model;

class UserModel extends Model {

	protected $mode = '';

	public function __construct($mode=null){
		//empty($mode) && print('用户错误');
		//$this->mode = $mode;
	}

	public function loginsuccess($uid,$cas = 0){
		$map['dcz_user.uid'] = $uid;
		$who = M('user') -> field('dcz_user.*,dcz_userinfo.nickname,dcz_bind.utype')->join('dcz_userinfo on dcz_userinfo.uid = dcz_user.uid','LEFT')
        ->join('dcz_bind on dcz_user.uid = dcz_bind.uid','LEFT')
        -> where($map) ->find();
		session('phone',$who['phone']);
        session('uid',$who['uid']);
        session('name',$who['nickname']);
        session('utype',($who['utype']) ? $who['utype'] :0  );
        session('is_login',1);
        if($cas)	session('cas',1);
	}

	public function login($info){
		$res = M('user') -> where($info) ->find();

		if($res)	
			$this->loginsuccess($res['uid']);
		else
			return false;
	}

	public function add($data,$type=''){	//0 默认注册 1 CAS 登陆 2 QQ OAUTH 3 sina oauth

		switch($type){
			case '':
			break;
			case 'sina':
			try {
				$user['username'] = $data['username'];
				$user['name'] = $data['username'];
				$user['password'] = $data['password'];
				$oauth['uid'] = M('user') -> add($user);
				$oauth['sina'] = $data['openid'];
				$res = M('oauth') -> add($oauth);
				$this->loginsuccess($oauth['uid']);
				return true;
			} catch (Exception $e) {
				var_dump($e);
				return false;
				exit();
			}				
			break;
			case 'qq':
			try {
				$user['username'] = $data['username'];
				$user['name'] = $data['username'];
				$user['password'] = $data['password'];
				$oauth['uid'] = M('user') -> add($user);
				$oauth['qq'] = $data['openid'];
				$this->loginsuccess($oauth['uid']);
				return true;
			} catch (Exception $e) {
				var_dump($e);
				return false;
				exit();
			}				
			break;
			default:
			break;
		}

	}

	public function check_exist($where = array()){
		if(M('user')->where($where)->count())	return true;

		return false;
	}
}