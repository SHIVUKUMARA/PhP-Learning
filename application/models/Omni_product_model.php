<?php defined('BASEPATH') or exit('No direct script access allowed');

class Omni_product_model extends CI_Model
{
    protected $table = 'omni_products';

    public function get_all()
    {
        return $this->db->order_by('id', 'DESC')->get($this->table)->result();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    public function delete($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    public function search($keyword)
    {
        return $this->db
            ->group_start()
            ->like('product_name', $keyword)
            ->or_like('sku', $keyword)
            ->or_like('brand', $keyword)
            ->group_end()
            ->order_by('id', 'DESC')
            ->get($this->table)
            ->result();
    }
}
