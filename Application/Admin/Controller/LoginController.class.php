<?php

namespace Admin\Controller;

use Think\Controller;

class LoginController extends Controller {
    //载入后端管理登陆界面
    public function index(){
        $this->display();
    }
    //退出登录
    public function logout(){
        session(null);
        $this->success('退出登录',U('Login/index'),3);
    }
    //检测登陆
    public function checkLogin(){
    	$UserModel = M('user');
    	// echo '这里我是来进行登录权限认证的';
    	$data = $_POST;
    	$data['password'] = md5(I('post.password'));
    	$data['time'] = time();
    	$data = $UserModel->create($data);
    	$rs = $UserModel->where("username = '".$data['username']."' AND password = '".$data['password']."'")->find();
        // echo $UserModel->getLastSql();die();
    	if($rs){
    		$this->success('登录成功\(^o^)/~',U('Index/index'),3);
            session('username',$rs['username']);
            session('uid',$rs['id']);
            session('remark',$rs['remark']);
    	}else{
    		$this->error('登录失败/(ㄒoㄒ)/~~,请重新登录！',U('Login/index'),3);
    	}
    	// var_dump($data);

    }
    //检测登录
    // public function checkLogin(){
    // 	$User = M('user');
    // 	$User->create();
    // 	$User->password = md5($User->password);
    // 	$User->time = time();
    // 	$rs = $User->where("password = '".$User->password."' AND username ='".$User->username."'")->find();
    // 	if($rs){
    // 		$this->success('登录成功O(∩_∩)O~',U('Index/index'),3);
    // 	}else{
    // 		$this->error('登录失败/(ㄒoㄒ)/~~,请重新登录',U('Login/index'),3);
    // 	}
    // }

}
