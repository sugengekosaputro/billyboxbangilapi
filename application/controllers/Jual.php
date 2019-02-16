<?php 
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Jual extends REST_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('jual_model');
  }

	public function index_post()
	{
		$body = array(
			'id_pelanggan' => $this->post('id_pelanggan'),
			'id_barang' => $this->post('id_barang'),
			'laba' => $this->post('laba'),
		);
		$insert = $this->jual_model->insertHargaJual($body);
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

  public function index_put()
	{
		$id = $this->uri->segment(2);
		$body = array(
			'laba' => $this->put('laba'),
		);

    $update = $this->jual_model->updateLaba($id,$body);
		if ($update) {
			$this->response([
				'status' => TRUE,
				'message' => 'Data Berhasil Diperbarui'
			],REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'status' => FALSE,
				'message' => 'Data Gagal Diperbarui'
			],REST_Controller::HTTP_BAD_REQUEST);
		}
  }
  
  public function index_delete()
	{
		$id = $this->uri->segment(2);
		$delete = $this->jual_model->deleteHargaJual($id);
		if ($delete) {
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