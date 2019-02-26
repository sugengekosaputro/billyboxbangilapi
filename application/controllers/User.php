<?php 
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class User extends REST_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
	}
	
	public function index_get()
	{
		$id = $this->uri->segment(2);
		if($id == null){
			$user = $this->user_model->tampilUser();
			if($user){
				$this->response([
					'status' => TRUE,
					'user' => $user,
					],
					REST_Controller::HTTP_OK
				);
			}else{
				$this->response([
					'status' => FALSE,
					'message' => 'Data User Tidak Ada' 
					],
					REST_Controller::HTTP_NOT_FOUND
				);
			}
		}else{
			$user = $this->user_model->tampilUserById($id);
			$this->response($user,REST_Controller::HTTP_OK);
		}
	}
	
	public function index_post()
	{	
		$config['upload_path']    = './assets/upload/user/';
		$config['allowed_types']  = 'gif|jpg|jpeg|png';
		$config['max_size']       = 100000;
		$config['max_width']      = 100000;
		$config['max_height']     = 100000;
		$config['file_name'] 			= $this->post('username');

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('foto_user')){
			$error = array('error' => $this->upload->display_errors());
			$this->response($error,500);
		} else {
			$data = $this->upload->data();
			$this->load->library('compress');

			$get_file = base_url().'assets/upload/user/'.$data['file_name'];
			$new_name_image = $data['file_name'];
			$quality = 15;
			$pngQuality = 5; // Exclusive for PNG files
			$destination = base_url().'assets/upload/user';

			$compress = new Compress();
			$compress->file_url = $get_file;
			$compress->new_name_image = $new_name_image;
			$compress->quality = $quality;
			$compress->pngQuality = $pngQuality;
			$compress->destination = $destination;
			$result = $compress->compress_image();



			$body = array(
				'username' => $this->post('username'),
				'password' => $this->post('password'),
				'email' => $this->post('email'),
				'foto' => base_url().'assets/upload/user/'.$data['file_name'], 
				'role' => $this->post('role'),
			);
			
			$insert = $this->user_model->insertUser($body);
			if($insert){
				$this->response([
					'status' => TRUE,
					'message' => 'Data Berhasil Ditambahkan',
				],REST_Controller::HTTP_CREATED);
			} else {
				$this->response([
					'status' => FALSE,
					'message' => 'Data Gagal Ditambahkan',
				],REST_Controller::HTTP_BAD_REQUEST);
			}
		}
	}

	public function update_post()
	{
		$id = $this->uri->segment(3);
		$config['upload_path']		= './assets/upload/user/';
		$config['allowed_types']	= 'gif|jpg|jpeg|png';
		$config['max_size']       = 10000; //kb
		$config['max_width']      = 10000; //px
		$config['max_height']     = 10000; //px
		$config['overwrite'] 			= TRUE;
		$config['file_name'] 			= $this->post('username');

		$this->load->library('upload', $config);
		if (!$this->upload->do_upload('foto_user')){
			$error = array('error' => $this->upload->display_errors());

			$body = array(
				'username' => $this->post('username'),
				'password' => $this->post('password'),
				'email' => $this->post('email'),
				'role' => $this->post('role'),
			);

			$update = $this->user_model->updateUser($id,$body);
			if ($update) {
				$this->response([
					'status' => TRUE,
					'message' => 'Data Berhasil Diperbarui'
				],REST_Controller::HTTP_OK);
			} else {
				$this->response([
					'status' => FALSE,
					'message' => 'Data Gagal Diperbarui NO FILES'
				],REST_Controller::HTTP_BAD_REQUEST);
			}
		}else{
			$data = $this->upload->data();
			$this->load->library('compress');

			$get_file = base_url().'assets/upload/user/'.$data['file_name'];
			$new_name_image = $data['file_name'];
			$quality = 15;
			$pngQuality = 5; // Exclusive for PNG files
			$destination = base_url().'assets/upload/user';

			$compress = new Compress();
			$compress->file_url = $get_file;
			$compress->new_name_image = $new_name_image;
			$compress->quality = $quality;
			$compress->pngQuality = $pngQuality;
			$compress->destination = $destination;
			$result = $compress->compress_image();
	
			$body = array(
				'username' => $this->post('username'),
				'password' => $this->post('password'),
				'nama' => $this->post('nama'),
				'foto' =>  base_url().'assets/upload/user/'.$data['file_name'],
				'role' => $this->post('role'),
			);

			$update = $this->user_model->updateUser($id,$body);
			if ($update) {
				$this->response([
					'status' => TRUE,
					'message' => 'Data Berhasil Diperbarui WITH FILE'
				],REST_Controller::HTTP_OK);
			} else {
				$this->response([
					'status' => TRUE,
					'message' => 'Data Berhasil Diperbarui WITH FILE FIXED Change 404 -> 201'
				],REST_Controller::HTTP_OK);
			}
		}
	}

	public function index_delete()
	{
		$id = $this->uri->segment(2);
		$res = $this->user_model->tampilUserById($id);
		$foto_path = substr($res['foto'],35);

		$delete = $this->user_model->deleteUser($id);
		if ($delete) {
			unlink($foto_path);
			$this->response([
				'status' => TRUE,
				'message' => 'Data Berhasil Dihapus'
			],REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'status' => FALSE,
				'message' => 'Data Gagal Dihapus'
			],REST_Controller::HTTP_BAD_REQUEST);
		}
	}
}