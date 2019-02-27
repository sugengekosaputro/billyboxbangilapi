<?php 
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Penagihan extends REST_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('penagihan_model');
		$this->load->model('tagihan_model');
		$this->load->model('pembayaran_model');
    }
    
	public function index_get()
	{
		$id = $this->uri->segment(3);
		$res = $this->penagihan_model->tampilPenagihan();
		$resId = $this->penagihan_model->tampilPenagihanByIdOrder($id);
		if(empty($id)){
			if ($res) {
				$this->response($res,REST_Controller::HTTP_OK);
			} else {
				$this->response([
					'status' => FALSE,
					'message' => 'Data Tidak Ada'
				],REST_Controller::HTTP_NOT_FOUND);
			}
		}else{
			if ($resId) {
				$this->response($resId,REST_Controller::HTTP_OK);
			} else {
				$this->response([
					'status' => FALSE,
					'message' => 'Data Tidak Ada'
				],REST_Controller::HTTP_NOT_FOUND);
			}
		}
	}
	
	public function pembayaran_post()
	{
		$id_order = $this->post('id_order');
		$data = [
			'id_pembayaran' => $this->post('id_pembayaran'),
			'dibayar' => $this->post('dibayar'),
			'tanggal' => date('Y-m-d'),
		];
		$insertPembayaran = $this->pembayaran_model->insertDetailPembayaran($data);
		if($insertPembayaran){
			$status = array('status_pembayaran' => 'Proses Bayar');
			$this->pembayaran_model->updatePembayaran($id_order,$status);
			$this->response([	
				'status' => TRUE,
				'message' => 'Berhasil Input Pembayaran',
			],REST_Controller::HTTP_OK);
		}else{
			$this->response([
				'status' => FALSE,
			 'message' => 'Gagal Input Pembayaran',
		 ],REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function suratjalan_post()
	{
		$sj = $this->post('surat_jalan');
		$id_order = $this->post('id_order');

		$insertSj = $this->tagihan_model->insertSuratJalan($sj);
		if($insertSj){

			//ambil harga sj
			foreach($sj as $id){
				$id_detail[] = $id['id_detail_order'];
			}

			$data = $this->tagihan_model->tampilSuratJalanByIdOrder($id_detail);
			foreach($data as $dt){
				$hrg_array[] = $dt['harga'];
			}
			$harga = array('harga_dikirim' => array_sum($hrg_array));
			//update harga_dikirim
			$update = $this->pembayaran_model->updatePembayaran($id_order,$harga);
			if($update){
				$this->response([
					'status' => TRUE,
					'message' => 'Berhasil Input Surat Jalan',
				],REST_Controller::HTTP_OK);
			}else{
				$this->response([
					'status' => FALSE,
				 'message' => 'Gagal Input Surat Jalan',
			 ],REST_Controller::HTTP_BAD_REQUEST);
			}
		}else{
			$this->response([
				'status' => FALSE,
			 'message' => 'Gagal Input Surat Jalan',
		 ],REST_Controller::HTTP_BAD_REQUEST);
		}
	}
}