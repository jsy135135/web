<?php

namespace Admin\Controller;

use Think\Controller;

class UserController extends Controller {
	public function index(){

	}
	//注册用户
	public function register(){
		if(IS_POST){
			$User = M('user');
			$User->create();
			$User->password = md5($User->password);
			$User->time = date('Y-m-d H:i:s');
			// echo date('Y-m-d H:i:s' );die();
			$rs = $User->add();
			if($rs){
				$this->success('注册用户成功,请登录！~！',U('Login/index'),3);
			}
			exit();
		}else{
			$this->display();
		}
	}
	//展示用户
	public function userList(){
	  $User = M('user');
	  $UserList = $User->select();
	  $this->assign('userList',$UserList);
	  $this->display();
	}
	//删除用户
	public function delUser(){
		// $id = I('post.id');
		// $User = M('user');
		// $
		// $rs = $User->

	}
}