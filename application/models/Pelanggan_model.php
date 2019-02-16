<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pelanggan_model extends CI_Model {

	private $tb_pelanggan = 'tb_pelanggan';
	private $tb_master_jual = 'tb_master_jual';
	
	public function tampilPelanggan()
	{
		$query = $this->db->get($this->tb_pelanggan);
		if ($query->num_rows() > 0) {
			
			$q = $query->result_array();
			foreach($q as $key => $val){
				$data [] = $val;
			}			
			return $data;
		} else {
			return FALSE;
		}
	}

	public function tampilPelangganById($id)
	{
		$this->db->where('id_pelanggan', $id);
		$query = $this->db->get($this->tb_pelanggan);
		if ($query->num_rows() > 0) {
			$q = $query->result_array();
			foreach($q as $key => $val){
				$this->load->model('jual_model');
				$q2 = $this->jual_model->tampilHargaJualByIdPelanggan($val['id_pelanggan']);

				$data = $val;
				$data += array('harga_jual'=> $q2);
			}
			return $data;
		} else {
			return FALSE;
		}
	}

	public function tampilPelangganByKeyword($keyword)
	{
		$this->db->like('nama_pelanggan',$keyword, 'both');
		$this->db->group_by('nama_pelanggan', 'ASC');
		$query = $this->db->get($this->tb_pelanggan);
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return FALSE;
		}
	}

	public function insertPelanggan($data)
	{
		$this->db->insert($this->tb_pelanggan, $data);
		if ($this->db->affected_rows()>0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function updatePelanggan($id,$data)
	{
		$this->db->where('id_pelanggan', $id)->update($this->tb_pelanggan, $data);
		
		if ($this->db->affected_rows()>0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function deletePelanggan($id)
	{
		$this->db->where('id_pelanggan', $id)
		->delete($this->tb_pelanggan);

		if ($this->db->affected_rows()>0) {
			return true;
		}else {
			return false;
		}
	}
}