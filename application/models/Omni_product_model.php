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

    public function get_facebook_products()
    {
        return $this->db
            ->where('facebook_status', 1)
            ->order_by('id', 'DESC')
            ->get($this->table)
            ->result();
    }

    public function get_facebook_published_products()
    {
        $this->db->select('
        id AS product_id,
        sku,
        product_name,
        category_name,
        description,
        price,
        sale_price,
        stock,
        currency,
        brand,
        gtin,
        mpn,
        main_image_url,
        extra_image_urls,
        facebook_status
        published_on,
        updated_at
        ');
        $this->db->from('omni_products');
        $this->db->where('facebook_status', 1);
        $this->db->order_by('published_on', 'DESC');

        $query = $this->db->get();

        if (!$query) {
            log_message('error', 'Facebook products DB error: ' . $this->db->last_query());
            return false;
        }

        return $query->result_array();
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
