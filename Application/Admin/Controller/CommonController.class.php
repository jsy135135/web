<?php

namespace Admin\Controller;

use Think\Controller;

class CommonController extends Controller {
    public function _initialize(){
    	// echo 11111;die();
    	if(!session('username')){
    		$this->redirect('Login/index');
    		exit();
    	}
    }
}
