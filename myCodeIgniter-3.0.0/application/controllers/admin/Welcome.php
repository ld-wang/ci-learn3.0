<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index($param)
	{

		
		var_dump($param);

		// 加载多个配置文件
		$this->config->load('level',false);
		//var_dump($this->config->item('num1'));
		//var_dump($this->config->item('num1','level'));

		error_reporting(-1);

		
		$arr = array('<div>abc</div>','<p>abc</p>');
		var_dump($arr);
		html_escape($arr);

		var_dump($arr);

		die;
		echo remove_invisible_characters('fd\%101012',true);

		//引用地址符
		$a=&get_config();
		//$a['subclass_prefix']='YOUR';

		//配置文件的两种调用方法
		echo config_item('subclass_prefix');
		echo $this->config->item('subclass_prefix');


		//$this->load->view('welcome_message');
	}

	public function myadd(){
	
		echo 123;
	
	}


	public function test2(){

		echo 'test2';
	}


	public function routeset(){
	
		echo 'trans here';
	}
}
