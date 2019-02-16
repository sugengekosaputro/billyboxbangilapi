<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Barang_model extends CI_Model {

	private $tabel = 'tb_master_barang';
	private $tb_kategori_barang = 'tb_kategori_barang';
	private $tb_stok_barang = 'tb_stok_barang';
	private $tb_master_jual = 'tb_master_jual';

	public function tampilBarang()
	{
		$query = $this->db->get($this->tabel);
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return FALSE;
		}
	}

	public function tampilBarangById($id)
	{
		$this->db->where('id_barang', $id);
		$query = $this->db->get($this->tabel);
		if ($query->num_rows() > 0) {
			$q = $query->result_array();
			foreach($q as $key => $val){
				$data = $val;
			}
			return $data;
		} else {
			return FALSE;
		}
	}

	public function tampilBarangByKeyword($keyword,$id)
	{
		if($id == null){
			$this->db->like('nama_barang',$keyword, 'both');
			$this->db->group_by('nama_barang', 'ASC');
			$query = $this->db->get($this->tabel);
			if ($query->num_rows() > 0) {
				return $query->result_array();
			} else {
				return FALSE;
			}
		}else {
			$this->db->like('tb_master_barang.nama_barang',$keyword, 'both');
			$this->db->join('tb_master_jual','tb_master_jual.id_barang = tb_master_barang.id_barang');
			$this->db->where('tb_master_jual.id_pelanggan',$id);
			$this->db->group_by('tb_master_barang.nama_barang', 'ASC');
			$query = $this->db->get($this->tabel);
			if ($query->num_rows() > 0) {
				return $query->result_array();
			} else {
				return FALSE;
			}
		}
	}

	public function insertBarang($data)
	{
		$this->db->insert($this->tabel, $data);
		if ($this->db->affected_rows()>0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function updateBarang($id,$data)
	{
		$this->db->where('id_barang', $id)->update($this->tabel, $data);
		
		if ($this->db->affected_rows()>0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function deleteBarang($id)
	{
		$this->db->where('id_barang', $id)
		->delete($this->tabel);

		if ($this->db->affected_rows()>0) {
			return true;
		}else {
			return false;
		}
	}
	
	public function tampilKategori()
	{
		$query = $this->db->get($this->tb_kategori_barang);
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return FALSE;
		}
	}

	public function tampilStok()
	{
		$this->db->select('tb_stok_barang.*, tb_master_barang.*');
		$this->db->join('tb_master_barang','tb_master_barang.id_barang = tb_stok_barang.id_barang');
		$query = $this->db->get($this->tb_stok_barang);
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return FALSE;
		}
	}

	public function tampilStokById($id_barang)
	{
		$this->db->select('tb_stok_barang.*, tb_master_barang.*');
		$this->db->join('tb_master_barang','tb_master_barang.id_barang = tb_stok_barang.id_barang');
		$this->db->where('tb_stok_barang.id_barang', $id_barang);
		$query = $this->db->get($this->tb_stok_barang);
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return FALSE;
		}
	}
}