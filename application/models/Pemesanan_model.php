<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pemesanan_model extends CI_Model {

    private $tb_master_barang = 'tb_master_barang', 
			$tb_order = 'tb_order_rev',
			$tb_detail_order = 'tb_detail_order_rev',
			$tb_detail_pembayaran = 'tb_detail_pembayaran',
			$tb_pembayaran = 'tb_pembayaran',
            $tb_pelanggan='tb_pelanggan';
	
	public function tampilPemesanan()
	{   
		$this->db->select("tb_order_rev.*, tb_pelanggan.*");
		$this->db->join("tb_pelanggan","tb_pelanggan.id_pelanggan=tb_order_rev.id_pelanggan");
		$this->db->order_by("tb_order_rev.id_order","DESC");
		$query = $this->db->get($this->tb_order);
		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return FALSE;
		}
	}

	public function tampilRiwayatPemesanan()
	{   
    $this->db->select("tb_order.*, tb_pelanggan.*, tb_master_barang.*, tb_pembayaran.*");
    $this->db->join('tb_pelanggan','tb_pelanggan.id_pelanggan=tb_order.id_pelanggan');
		$this->db->join('tb_master_barang','tb_master_barang.id_barang=tb_order.id_barang');
		$this->db->join('tb_pembayaran','tb_pembayaran.id_order=tb_order.id_order');
		$this->db->where('tb_order.status_order','Selesai Pengiriman');
		$query = $this->db->get($this->tb_order);
		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return FALSE;
		}
	}

	public function tampilPemesananById($id)
	{
		$this->db->select("tb_order_rev.*, tb_pelanggan.*, tb_pembayaran.*");
		$this->db->join("tb_pelanggan","tb_pelanggan.id_pelanggan=tb_order_rev.id_pelanggan");
		$this->db->join("tb_pembayaran","tb_order_rev.id_order=tb_pembayaran.id_order");
		$this->db->order_by("tb_order_rev.id_order","DESC");
		$this->db->where('tb_order_rev.id_order', $id);
		$query = $this->db->get($this->tb_order);
		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return FALSE;
		}
	}

	public function tampilPemesananBylog($log)
	{
		$this->db->where('log_time', $log);
		$query = $this->db->get($this->tb_order);
		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return FALSE;
		}
	}

	public function tampilDetailOrder($id_order)
	{
		$this->db->select($this->tb_detail_order.'.*, tb_master_barang.nama_barang');
		$this->db->join('tb_master_barang','tb_detail_order_rev.id_barang = tb_master_barang.id_barang');
		$this->db->where($this->tb_detail_order.'.id_order', $id_order);
		$query = $this->db->get($this->tb_detail_order);
		if ($query->num_rows() > 0) {
			foreach($query->result() as $val){
				$res[] = $val;
			}
			return $res;
		} else {
			return FALSE;
		}
	}

	public function tampilDetailOrderSJ($id_order)
	{
//		$this->db->select($this->tb_detail_order.'.*, tb_master_barang.nama_barang, tb_pembayaran.total_bayar,tb_pembayaran.dp, (tb_pembayaran.total_bayar - tb_pembayaran.dp) as sisa');
		$this->db->select($this->tb_detail_order.'.*, tb_master_barang.nama_barang, tb_tagihan.dikirim');
		$this->db->join('tb_master_barang','tb_detail_order_rev.id_barang = tb_master_barang.id_barang');
		$this->db->join('tb_pembayaran','tb_detail_order_rev.id_order = tb_pembayaran.id_order');
		$this->db->join('tb_tagihan','tb_detail_order_rev.id_detail_order = tb_tagihan.id_detail_order');
		$this->db->where($this->tb_detail_order.'.id_order', $id_order);
		$query = $this->db->get($this->tb_detail_order);
		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return FALSE;
		}
	}

	// public function tampilDetailOrderSJ($id_order)
	// {
	// 	$this->db->select($this->tb_detail_order.'.*, tb_master_barang.nama_barang, tb_pembayaran.total_bayar,tb_pembayaran.dp, (tb_pembayaran.total_bayar - tb_pembayaran.dp) as sisa');
	// 	$this->db->join('tb_master_barang','tb_detail_order_rev.id_barang = tb_master_barang.id_barang');
	// 	$this->db->join('tb_pembayaran','tb_detail_order_rev.id_order = tb_pembayaran.id_order');
	// 	$this->db->where($this->tb_detail_order.'.id_order', $id_order);
	// 	$query = $this->db->get($this->tb_detail_order);
	// 	if ($query->num_rows() > 0) {
	// 		return $query->result();
	// 	} else {
	// 		return FALSE;
	// 	}
	// }

	public function tampilHarga($id_order)
	{
		$this->db->select('harga');
		$this->db->where('id_order', $id_order);
		$query = $this->db->get($this->tb_detail_order);
		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return FALSE;
		}
	}

	public function updatePembayaran($id_pembayaran,$datapembayaran)
	{
		$this->db->where('id_pembayaran', $id_pembayaran)->update($this->tb_pembayaran,$datapembayaran);
		
		if ($this->db->affected_rows()>0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}


	public function updatePemesanan($id,$data)
	{
		$this->db->where('id_order', $id)->update($this->tb_order, $data);
		
		if ($this->db->affected_rows()>0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	//////////////////////____UPDATE____/////////////////////////////////////////////////////

	public function tampilkanOrder()
	{
		$this->db->select(
			'tb_order_rev.*,tb_pelanggan.nama_pelanggan,tb_pelanggan.alamat,
			tb_pembayaran.status_pembayaran'
		)
		->join($this->tb_pelanggan,'tb_pelanggan.id_pelanggan = tb_order_rev.id_pelanggan')
		->join($this->tb_pembayaran,'tb_pembayaran.id_order = tb_order_rev.id_order')
		->order_by("tb_order_rev.id_order","DESC");
		$query = $this->db->get($this->tb_order);
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return FALSE;
		}
	}

	public function tampilkanOrderByIdOrder($id_order)
	{
		$this->db->where("tb_order_rev.id_order",$id_order);
		$query = $this->db->get($this->tb_order);
		if ($query->num_rows() > 0) {
			foreach($query->result_array() as $val){
				$query2 = $this->db->select($this->tb_detail_order.'.*,tb_master_barang.nama_barang,
				(tb_detail_order_rev.harga / tb_detail_order_rev.jumlah) as harga_satuan')
				->where('tb_detail_order_rev.id_order',$val['id_order'])
				->join('tb_master_barang','tb_master_barang.id_barang = tb_detail_order_rev.id_barang')
				->get($this->tb_detail_order);
				$q2 = $query2->result_array();

				$res = array(
					'id_order' => $val['id_order'],
					'id_pelanggan' => $val['id_pelanggan'],
					'tanggal_order' => $val['tanggal_order'],
					'status_order' => $val['status_order'],
					'log_time' => $val['log_time'],
					'detail_order' => $q2,
				);
			}
			return $res;
		} else {
			return FALSE;
		}
	}

	public function hitungStatusByKeyword($keyword)
	{
		$this->db->like('status_order',$keyword, 'both');
		$query = $this->db->get($this->tb_order);
		if ($query->num_rows() > 0) {
			return $query->num_rows();
		} else {
			return 0;
		}
	}

	public function tampilPemesananByTgl($tgl)
	{
		$this->db->where('tanggal_order',$tgl);
		$query = $this->db->get($this->tb_order);
		if ($query->num_rows() > 0) {
			return $query->num_rows();
		} else {
			return 0;
		}
	}

	public function insertOrder($data)
	{
		$this->db->insert($this->tb_order, $data);
		if ($this->db->affected_rows()>0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function insertDetailOrder($data)
	{
		$this->db->insert_batch($this->tb_detail_order, $data);
		if ($this->db->affected_rows()>0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	// public function insertPembayaran($data)
	// {
	// 	$this->db->insert($this->tb_pembayaran, $data);
	// 	if ($this->db->affected_rows()>0) {
	// 		return TRUE;
	// 	} else {
	// 		return FALSE;
	// 	}
	// }

	public function updateOrder($id_order,$data)
	{
		$this->db->where('id_order', $id_order)->update($this->tb_order, $data);
		
		if ($this->db->affected_rows()>0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function caridetailOrder($id_order)
	{
		$this->db->select($this->tb_detail_order.'.*,tb_master_barang.nama_barang')
		->where('tb_detail_order_rev.id_order',$id_order)
		->join('tb_master_barang','tb_master_barang.id_barang = tb_detail_order_rev.id_barang');
		$query2 = $this->db->get($this->tb_detail_order);
		if($query2->num_rows() > 1){
			return $query2->result_array();
		}else{
			return 'ganok';
		}
	}
}