<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function __construct(){
	
		parent::__construct();

		//var_dump($_SERVER);
		//var_dump($_GET);
		
		
		//var_dump($this->input->get(array('a','b')));

		//var_dump($this->input->get('b'));

		//var_dump($this->input->get('d[a3]'));	
		//
		// $this->input->request_headers();

		//var_dump($this->input->__get('headers'));


		// echo '<br>';
		// echo PHP_SAPI;echo '<br>';
		// echo "<pre>";
		// print_r(filter_list());

		// var_dump($_POST);
		// $php_input = file_get_contents('php://input','r');
		// var_dump($php_input);



		//header("X-Powered-By:".PHP_SAPI);

		//var_dump( $this->input->__get('raw_input_stream') );


		// $list = $this->db->query('show processlist')->result_array();
		// print_r($list);

	}

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
		echo __FILE__;
		
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
		
		
		echo $this->router->fetch_directory();echo '/';
		echo $this->router->fetch_class();echo '/';
		echo $this->router->fetch_method();echo '<br>';

		echo 123;
	
	}


	public function test2(){
		echo 'test2';
	}


	public function routeset(){
	
		echo 'trans here';
	}


	public function routeget(){
		
		echo 'trans get ';

		//var_dump($this->db);
	
	}

	public function routepost(){
		
		echo 'trans post ';
	
	}

	public function _test(){
	
		echo 'start with _';
	}


	public function show($num,$cate){
	
		echo $num;echo '<br>';echo $cate;
	}

	public function  testdb(){
	
		$query = $this->db->get_where('news');
		var_dump( $query->row_array());
	}


	public function testparams(){
		
		var_dump($this->db->hostname);

		$db90 = $this->load->database('db90',true);

		var_dump($this->db->hostname);

		var_dump($db90->hostname);

		$db2 = $this->load->database('mysqli://root:999999@192.168.3.90/test?port=3306',true);



		//$this->load->library('FTP','','copyftp');

		//$this->load->library('FTP');

		//var_dump($this->copyftp->hostname);

		//var_dump($this);
		
		$res = $this->uri->uri_to_assoc(3);
		var_dump($res);
	
	
	}


	public function loadview(){

		// header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		// header("Cache-Control: no-cache");
		// header("Pragma: no-cache");
		// 
		$this->output->enable_profiler(true);

		$this->output->cache(10);  //开启缓存

		$this->load->view('welcome_message');
	}


	public function testlibrary(){

		$this->load->library('FTP');

		print_r($this->ftp);
	}


	public function testsetsession(){


		$this->session->set_flashdata('item', 'value');
		$this->session->set_flashdata('item2', 'value2');

		$this->session->mark_as_temp('item', 300);
		$this->session->mark_as_temp('item2', 300);

		var_dump($_SESSION['__ci_vars']);
		var_dump($_SESSION);

		echo session_id();

	}

	public function testgetsession(){

		// var_dump($_SESSION['__ci_vars']);
		// var_dump($_SESSION);
		echo 'in controller<br>';
		print_r($this->session->flashdata());

		
	}	


	function testForm(){

        $this->load->helper(array('form', 'url'));

        $this->load->library('form_validation');


        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view('myform');
        }
        else
        {
            $this->load->view('formsuccess');
        }

	}
	/*
	public function _output(){

		echo 123123;

	}
	
	
	public function _remap($method){

		var_dump($method);
	
	}
	*/
}
