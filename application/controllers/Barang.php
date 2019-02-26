<?php 
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Barang extends REST_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('barang_model');
		$this->load->model('pelanggan_model');
	}
	
	public function index_get()
	{
		$id = $this->uri->segment(2);
		if($id == null){
			$barang = $this->barang_model->tampilBarang();
			if($barang){
				$this->response([
					'status' => TRUE,
					'barang' => $barang,
					],
					REST_Controller::HTTP_OK
				);
			}else{
				$this->response([
					'status' => FALSE,
					'message' => 'Data Barang Tidak Ada' 
					],
					REST_Controller::HTTP_NOT_FOUND
				);
			}
		}else{
			$barang = $this->barang_model->tampilBarangById($id);
			$this->response($barang,REST_Controller::HTTP_OK);
		}
	}

	public function cari_post()
	{
		$key = $this->post('keyword');
		$result = $this->barang_model->tampilBarangByKeyword($key,null);
		if($result){
			foreach($result as $row){

				$arr['query'] = $key;
				$arr['suggestions'][] = array(
					'value'	=>$row['nama_barang'],
					'id'	=>$row['id_barang'],
					'harga_beli' =>$row['harga_beli'],
					'id_kategori' =>$row['id_kategori'],
					'foto_barang' => $row['foto_barang'],
					'stok' => $row['stok'],
				);
			}
			$this->response($arr,REST_Controller::HTTP_OK);
		}	
	}

	public function cariby_post()
	{
		$id = $this->post('id_pelanggan');
		$key = $this->post('keyword');
		$result = $this->barang_model->tampilBarangByKeyword($key,$id);
		if($result){
			foreach($result as $val){
				$jual = array_sum([$val['harga_jual'],$val['laba']]);

				$ari['query'] = $key;
				$ari['suggestions'][] = array(
					'value'				=> $val['nama_barang'],
					'id'					=> $val['id_barang'],
					'harga'				=> $val['harga_jual'],
					'laba'				=> $val['laba'],
					'harga_jual' 	=> $jual,
				);
			}
			$this->response($ari,REST_Controller::HTTP_OK);
		}
	}
	
	public function index_post()
	{
		$config['upload_path']    = './assets/upload/barang/';
		$config['allowed_types']  = 'gif|jpg|jpeg|png';
		$config['max_size']       = 100000; //kb
		$config['max_width']      = 100000; //px
		$config['max_height']     = 100000; //px
		$config['file_name'] 			= $this->post('id_barang');

		$this->load->library('upload', $config);
		if (!$this->upload->do_upload('foto_barang')){
			$error = array('error' => $this->upload->display_errors());
			$this->response($error,500);
		} else {
			$data = $this->upload->data();
			$this->load->library('compress');

			$get_file = base_url().'assets/upload/barang/'.$data['file_name'];
			$new_name_image = $data['file_name'];
			$quality = 15;
			$pngQuality = 5; // Exclusive for PNG files
			$destination = base_url().'assets/upload/barang';

			$compress = new Compress();
			$compress->file_url = $get_file;
			$compress->new_name_image = $new_name_image;
			$compress->quality = $quality;
			$compress->pngQuality = $pngQuality;
			$compress->destination = $destination;
			$result = $compress->compress_image();

			$body = array(
				'id_barang' => $this->post('id_barang'),
				'id_kategori' => $this->post('id_kategori'),
				'nama_barang' => $this->post('nama_barang'),
				'ukuran' => $this->post('ukuran'),
				'gramatur' => $this->post('gramatur'),
				'foto_barang' => base_url().'assets/upload/barang/'.$data['file_name'], 
				'harga_beli' => $this->post('harga_beli'),
				'harga_jual' => $this->post('harga_jual'),
			);

			$insert = $this->barang_model->insertBarang($body);
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
		$config['upload_path']		= './assets/upload/barang/';
		$config['allowed_types']	= 'gif|jpg|jpeg|png';
		$config['max_size']       = 10000; //kb
		$config['max_width']      = 10000; //px
		$config['max_height']     = 10000; //px
		$config['overwrite'] 			= TRUE;
		$config['file_name'] 			= $this->post('id_barang');

		$this->load->library('upload', $config);
		if (!$this->upload->do_upload('foto_barang')){
			$error = array('error' => $this->upload->display_errors());

			$id = $this->post('id_barang');
			$body = array(
				'id_kategori' => $this->post('id_kategori'),
				'nama_barang' => $this->post('nama_barang'),
				'ukuran' => $this->post('ukuran'),
				'gramatur' => $this->post('gramatur'),
				'harga_beli' => $this->post('harga_beli'),
				'harga_jual' => $this->post('harga_jual'),
			);

			$update = $this->barang_model->updateBarang($id,$body);
			if ($update) {
				$this->response([
					'status' => TRUE,
					'message' => 'Data Berhasil Diperbarui NO FILES'
				],REST_Controller::HTTP_OK);
			} else {
				$this->response([
					'status' => FALSE,
					'message' => 'Data Gagal Diperbarui NO FILES'
				],REST_Controller::HTTP_BAD_REQUEST);
			}
		} else {
			$data = $this->upload->data();
			$this->load->library('compress');

			$get_file = base_url().'assets/upload/barang/'.$data['file_name'];
			$new_name_image = $data['file_name'];
			$quality = 15;
			$pngQuality = 5; // Exclusive for PNG files
			$destination = base_url().'assets/upload/barang';

			$compress = new Compress();
			$compress->file_url = $get_file;
			$compress->new_name_image = $new_name_image;
			$compress->quality = $quality;
			$compress->pngQuality = $pngQuality;
			$compress->destination = $destination;
			$result = $compress->compress_image();
			
			$id = $this->post('id_barang');
			$body = array(
				'id_kategori' => $this->post('id_kategori'),
				'nama_barang' => $this->post('nama_barang'),
				'ukuran' => $this->post('ukuran'),
				'gramatur' => $this->post('gramatur'),
				'foto_barang' =>  base_url().'assets/upload/barang/'.$data['file_name'],
				'harga_beli' => $this->post('harga_beli'),
				'harga_jual' => $this->post('harga_jual'),
			);

			$update = $this->barang_model->updateBarang($id,$body);
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

	public function stok_put()
	{
		$id = $this->put('id_barang');
		$data = array(
			'stok' => $this->put('stok'),
		);
		
		$update = $this->barang_model->updateBarang($id,$data);
		if ($update) {
			$this->response([
				'status' => TRUE,
				'message' => 'Stok Diperbarui'
			],REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'status' => FALSE,
				'message' => 'Stok Gagal Diperbarui'
			],REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function index_delete()
	{
		$id = $this->uri->segment(2);
		$res = $this->barang_model->tampilBarangById($id);
		$foto_path = substr($res['foto_barang'],35);

		$delete = $this->barang_model->deleteBarang($id);
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