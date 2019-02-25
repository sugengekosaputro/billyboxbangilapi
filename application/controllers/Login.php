<?php 
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Login extends REST_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
	}
	
	public function index_post()
	{
      $email = $this->post('email');
			$password = md5($this->post('password'));
			$cek = $this->Login_model->cekUser($email,$password);
			if ($cek) {
                $tampil = $this->Login_model->getUsername($email);
                $userdata = array(  
									'status'   => TRUE, 
									'id_user' => $tampil->id_user,
									'username' => $tampil->username,
									'email' => $tampil->email,
									'password' => $tampil->password,
									'foto' => $tampil->foto,
									'role' => $tampil->role,
								);
				
                $this->response($userdata,REST_Controller::HTTP_OK);      
			} else {
				$this->response([
					'status' => FALSE
				],REST_Controller::HTTP_OK);
			}
	}


	public function cookieupdate_post()
	{
		$cookie = array(
						'cookie' => $this->post('cookie'),
					);
		$id = $this->post('id');
			$update = $this->Login_model->updateCookie($id, $cookie);
			if($update){
				$this->response([
					'status' => TRUE,
					'message' => 'Cookie Berhasil Diperbarui'
				],REST_Controller::HTTP_OK);
			}else {
				$this->response([
					'status' => FALSE,
					'message' => 'Cookie gagal Diperbarui'
				],REST_Controller::HTTP_OK);
		}
	}

	public function cookieget_post()
	{
		$cookie = $this->post('cookie');
		$cek = $this->Login_model->cekCookie($cookie);
			if ($cek) {
                $tampil = $this->Login_model->getCookie($cookie);
                $userdata = array(  
									'status'   => TRUE, 
									'id_user' => $tampil->id_user,
									'username' => $tampil->username,
									'email' => $tampil->email,
									'password' => $tampil->password,
									'foto' => $tampil->foto,
									'role' => $tampil->role,
								);
                $this->response($userdata,REST_Controller::HTTP_OK);      
			} else {
				$this->response([
					'status' => FALSE
				],REST_Controller::HTTP_OK);
			}
	}

}