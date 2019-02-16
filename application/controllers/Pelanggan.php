<?php 
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Pelanggan extends REST_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('pelanggan_model');
		$this->load->model('jual_model');
	}
	
	public function index_get()
	{
		$id= $this->uri->segment(2);
		if($id == null){
			$pelanggan = $this->pelanggan_model->tampilPelanggan();
			if($pelanggan){
				$this->response([
					'status' => TRUE,
					'total' => count($pelanggan),
					'pelanggan' => $pelanggan,
					],
					REST_Controller::HTTP_OK
				);
			}else{
				$this->response([
					'status' => FALSE,
					'message' => 'Data Pelanggan Tidak Ada' 
					],
					REST_Controller::HTTP_NOT_FOUND
				);
			}
		}else{
			$pelanggan = $this->pelanggan_model->tampilPelangganById($id);
			$this->response($pelanggan,REST_Controller::HTTP_OK);
		}
	}
	
	public function index_post()
	{	
		$body = array(
			'nama_pelanggan' => $this->post('nama_pelanggan'),
			'alamat' => $this->post('alamat'),
			'nomor_telepon' => $this->post('nomor_telepon'),
			'email' => $this->post('email'), 
		);
		
		$insert = $this->pelanggan_model->insertPelanggan($body);
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
			'nama_pelanggan' => $this->put('nama_pelanggan'),
			'alamat' => $this->put('alamat'),
			'nomor_telepon' => $this->put('nomor_telepon'),
			'email' => $this->put('email'), 
		);

    $update = $this->pelanggan_model->updatePelanggan($id,$body);
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

	public function cari_post()
	{
		$key = $this->post('keyword');
		$result = $this->pelanggan_model->tampilPelangganByKeyword($key);
		if($result){
			foreach($result as $row){
				$arr['query'] = $key;
				$arr['suggestions'][] = array(
					'value'	=>$row['nama_pelanggan'],
					'id'	=>$row['id_pelanggan'],
				);
			}
			$this->response($arr,REST_Controller::HTTP_OK);
		}	
	}

	public function index_delete()
	{
		$id = $this->uri->segment(2);
		$delete = $this->pelanggan_model->deletePelanggan($id);
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