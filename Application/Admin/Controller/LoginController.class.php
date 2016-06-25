<?php

namespace Admin\Controller;

use Think\Controller;

class LoginController extends Controller {
    //载入后端管理登陆界面
    public function index(){
        $this->display();
    }
    //检测登陆
    public function checkLogin(){
        
    }
}
