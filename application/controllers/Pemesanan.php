<?php 
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Pemesanan extends REST_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('pemesanan_model');
		$this->load->model('barang_model');
		$this->load->model('pelanggan_model');
		$this->load->model('pembayaran_model');
		$this->load->model('penagihan_model');
		$this->load->model('kategori_model');
		$this->load->model('jual_model');
		$this->load->model('tagihan_model');
  }

	public function index_get()
	{
		$id= $this->uri->segment(2);
		if($id == null){
			$pemesanan = $this->pemesanan_model->tampilkanOrder();
			if($pemesanan){
				$this->response([
					'status' => TRUE,
					'order' => [
						'order_baru' 		=> $this->pemesanan_model->hitungStatusByKeyword('baru'),
						'order_proses' 	=> $this->pemesanan_model->hitungStatusByKeyword('proses'),
						'order_dikirim' => $this->pemesanan_model->hitungStatusByKeyword('dikirim'),
						'order_selesai' 	=> $this->pemesanan_model->hitungStatusByKeyword('selesai'),
					],
					'pembayaran' => [
						'belum_bayar' => $this->pembayaran_model->hitungStatusByKeyword('belum bayar'),
						'proses_bayar' => $this->pembayaran_model->hitungStatusByKeyword('proses bayar'),
						'lunas' 			=> $this->pembayaran_model->hitungStatusByKeyword('lunas'),
					],
					'pemesanan' => $pemesanan,
				],REST_Controller::HTTP_OK);
			}else{
				$this->response([
					'status' => FALSE,
					'message' => 'Data Tidak Ada',
				],REST_Controller::HTTP_NOT_FOUND);
			}
		}else{
			$order = $this->pemesanan_model->tampilkanOrderByIdOrder($id);
			$pelanggan = $this->pelanggan_model->tampilPelangganById($order['id_pelanggan']);
			
			foreach($order['detail_order'] as $val){
				$id_detail_order[] = $val['id_detail_order'];
			}

			$pembayaran = $this->pembayaran_model->tampilPembayaranByIdOrder($id,$id_detail_order);
			//$detail = $this->pemesanan_model->caridetailOrder($id);
			$surat_jalan = $this->tagihan_model->tampilSuratJalanByIdOrder($id_detail_order);
			$data = array(
				'pelanggan' => $pelanggan,
				'order' => $order,
				'pembayaran' => $pembayaran,
				'surat_jalan' => array('history' => $surat_jalan),
			);
			$this->response($data);		}
	}

	public function index_post()
	{
		$tgl_now = date('Y-m-d');
		$time = date('H:i:s');

		$id_pelanggan = $this->post('id_pelanggan');
		$pesanan = $this->post('pesanan');
		$pembayaran = $this->post('pembayaran');

		$bodyOrder = array(
			'id_order' 			=> $this->cekIdOrder($tgl_now),
			'id_pelanggan' 	=> $id_pelanggan,
			'tanggal_order' => $tgl_now,
			'status_order' 	=> 'Baru',
			'log_time' 			=> $time,
		);

		$insertPemesanan = $this->pemesanan_model->insertOrder($bodyOrder);
		if($insertPemesanan){
			$i = 0;
			while($i < count($pesanan)){
				$bodyDetailOrder[] = array(
					'id_order' => $bodyOrder['id_order'],
					'id_barang' => $pesanan[$i]['id_barang'],
					'jumlah' => $pesanan[$i]['jumlah'],
					'harga' => $pesanan[$i]['total']
				);
				$i++;
			}
			$insertDetailPemesanan = $this->pemesanan_model->insertDetailOrder($bodyDetailOrder);
			if($insertDetailPemesanan){
				$bodyPembayaran = array(
					'id_order' => $bodyOrder['id_order'],
					'harga_pesan' => $pembayaran['harga_pesan'],
					'harga_dikirim' => null,
					'dp' => $pembayaran['dp'],
					'status_pembayaran' => 'Belum Bayar',
					'jatuh_tempo' => $pembayaran['jatuh_tempo'],
				);
				$insertPembayaran = $this->pembayaran_model->insertPembayaran($bodyPembayaran);		
				if($insertPembayaran){
					$this->response([
						'status' => TRUE,
						'id_order' => $bodyOrder['id_order'],
						'message' => 'Nota Pemesanan Berhasil Dikirim Ke Pelanggan',
					],REST_Controller::HTTP_OK);	
				}else{
					$this->response([
						'status' => FALSE,
					 'message' => 'Pembayaran Gagal Dibuat',
				 ],REST_Controller::HTTP_BAD_REQUEST);
				}
			}
		}else{
			$this->response([
				'status' => FALSE,
			 'message' => 'Pemesanan Gagal Dibuat',
		 ],REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function statusorder_put()
	{
		$id = $this->uri->segment(3);
		$body = array(
			'status_order' => $this->put('status_order'),
		);

    $update = $this->pemesanan_model->updateOrder($id,$body);
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
	
	public function statusbayar_put()
	{
		$id = $this->uri->segment(3);
		$body = array(
			'status_pembayaran' => $this->put('status_pembayaran'),
		);

    $update = $this->pembayaran_model->updatePembayaran($id,$body);
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
//		var_dump($id);
  }

	public function tesinput_post()
	{
		$body_pembayaran = array(
			'id_order' => $this->post('id_order'),
			'total_bayar' => '9978',
			'status_pembayaran' => 'Belum Bayar',
		);
		$insertPembayaran = $this->pembayaran_model->insertPembayaran($body_pembayaran);
		if($insertPembayaran){
			$this->response([
				'status' => TRUE,
				'message' => 'Tagihan Berhasil Dikirim',
			],REST_Controller::HTTP_OK);
		}else{
			$this->response([
				'status' => FALSE,
			 'message' => 'Data HargaPembayaran Gagal Ditambahkan',
		 ],REST_Controller::HTTP_BAD_REQUEST);					
		}
	}

	public function pelanggan_post(){
		$keyword = $this->post('keyword');
		 
		 	$result = $this->pemesanan_model->caripelanggan($keyword);
		 	if (count($result) > 0) {
		    foreach ($result as $row){
					$arr['query'] = $keyword;
					$arr['suggestions'][] = array(
						'value'	=>$row->nama_pelanggan,
						'id'	=>$row->id_pelanggan,
					);
				 }
			}
		echo json_encode($arr);
	}

	public function barang_post(){
		$keyword = $this->post('keyword');
	
			$result = $this->pemesanan_model->caribarang($keyword);
			if (count($result) > 0) {
				foreach ($result as $row){
					$arr['query'] = $keyword;
					$arr['suggestions'][] = array(
						'value'	=>$row->nama_barang,
						'id'	=>$row->id_barang,
						'harga_beli' =>$row->harga_beli,
						'id_kategori' =>$row->id_kategori,
					);
				}
		  }
			echo json_encode($arr);
	}

	public function send_post()
	{
		$id_order = $this->post('id_order');
		$emailTujuan = $this->post('email');
		
		$send = $this->notifEmail($id_order,$emailTujuan);
		if($send){
			echo 'OK';
		}else{
			echo 'not';
		}
	}

	public function cekIdOrder($tgl_now)
	{
		$digitAwal = date('ymd');
		
		$cek_id = $this->pemesanan_model->tampilPemesananByTgl($tgl_now);
		if($cek_id){
			$urut = ($cek_id)+1;
		}else{
			$urut = 1;
		}
		$id_order = $digitAwal.$urut;
		return $id_order;
	}
}