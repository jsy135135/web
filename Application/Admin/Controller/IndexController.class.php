<?php

namespace Admin\Controller;

use Think\Controller;

class IndexController extends CommonController {

    //加载管理后台首页
    public function index() {
        $this->display();
    }

    //登陆页面
    public function Login() {
        $this->display();
    }

    //退出操作
    public function logout() {
        
    }

}
