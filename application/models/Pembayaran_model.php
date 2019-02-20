<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pembayaran_model extends CI_Model {

	private $tabel = 'tb_pembayaran';
	private $tabel_detail = 'tb_detail_pembayaran';

  public function tampilPembayaranById($id_pembayaran)
	{
		$this->db->where('id_pembayaran', $id_pembayaran);
		$query = $this->db->get($this->tabel);
		if ($query->num_rows() > 0) {
			foreach($query->result() as $val){
				$res = $val;
			}
			return $res;
		} else {
			return FALSE;
		}
  }
    
	public function tampilPembayaranByIdOrder($id_order,$id_detail_order)
	{
		$this->db->where('id_order', $id_order);
		$query = $this->db->get($this->tabel);
		if ($query->num_rows() > 0) {
			foreach($query->result_array() as $val){
				$this->db->select('
				tb_tagihan.id_detail_order, SUM(tb_tagihan.dikirim) as dikirim,
				tb_master_barang.nama_barang')
				->join('tb_detail_order_rev','tb_tagihan.id_detail_order = tb_detail_order_rev.id_detail_order')
				->join('tb_master_barang','tb_detail_order_rev.id_barang = tb_master_barang.id_barang')
				->where_in('tb_tagihan.id_detail_order',$id_detail_order)
				->group_by('tb_tagihan.id_detail_order');
				$nota = $this->db->get('tb_tagihan')->result_array();
				$detail_pem = $this->db->where('id_pembayaran',$val['id_pembayaran'])->get('tb_detail_pembayaran')->result_array();
				
				$dibayar = array_sum(array_column($detail_pem,'dibayar'));

				if($val['harga_dikirim'] == null){
					$hrgAwal = $val['harga_pesan'];
				}else{
					$hrgAwal = $val['harga_dikirim'];
				}

				$sisa = $hrgAwal-$dibayar;

				$res = [
					'id_pembayaran' => $val['id_pembayaran'],
					'id_order' => $val['id_order'],
					'harga_pesan' => $val['harga_pesan'],
					'harga_dikirim' => $val['harga_dikirim'],
					'dp' => $val['dp'],
					'status_pembayaran' => $val['status_pembayaran'],
					'jatuh_tempo' => $val['jatuh_tempo'],
					'nota' => $nota,
					'sudah_dibayar' => $dibayar,
					'sisa_pembayaran' => $sisa,
					'detail_pembayaran' => $detail_pem,
				];
			}
			return $res;
		} else {
			return FALSE;
		}
  }

	public function insertPembayaran($data)
	{
		$this->db->insert($this->tabel, $data);
		if ($this->db->affected_rows()>0) {
			return TRUE;
		} else {
			return FALSE;
		}
  }
    
	public function insertDetailPembayaran($data)
	{
		$this->db->insert($this->tabel_detail, $data);
		if ($this->db->affected_rows()>0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function updatePembayaran($id_order,$data)
	{
		$this->db->where('id_order', $id_order)->update($this->tabel, $data);
		if ($this->db->affected_rows()>0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function hitungStatusByKeyword($keyword)
	{
		$this->db->like('status_pembayaran',$keyword, 'both');
		$query = $this->db->get($this->tabel);
		if ($query->num_rows() > 0) {
			return $query->num_rows();
		} else {
			return 0;
		}
	}
}

