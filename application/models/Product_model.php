<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Product_model extends CI_Model
{

    public function get_all($category = null, $sub_category = null)
    {
        $this->db->select('*')->from('products');

        if ($category) {
            $this->db->where('category', $category);
        }

        if ($sub_category) {
            $this->db->where('sub_category', $sub_category);
        }

        $this->db->order_by('created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function get_categories()
    {
        $this->db->select('DISTINCT category', FALSE);
        return $this->db->get('products')->result_array();
    }

    public function get_subcategories($category = null)
    {
        $this->db->select('DISTINCT sub_category', FALSE);

        if (!empty($category)) {
            $this->db->where('category', $category);
        }
        return $this->db->get('products')->result_array();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where('products', ['id' => $id])->row();
    }

    public function insert($data)
    {
        return $this->db->insert('products', $data);
    }

    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update('products', $data);
    }

    public function delete($id)
    {
        return $this->db->where('id', $id)->delete('products');
    }

    public function count_all()
    {
        return $this->db->count_all('products');
    }

    public function get_filtered($category = null, $sub_category = null, $limit = 10, $offset = 0)
    {
        $this->db->from('products');

        if ($category) {
            $this->db->where('category', $category);
        }

        if ($sub_category) {
            $this->db->where('sub_category', $sub_category);
        }

        $this->db->order_by('created_at', 'DESC');

        return $this->db->get('', $limit, $offset)->result();
    }

    public function count_filtered($category = null, $sub_category = null)
    {
        $this->db->from('products');

        if ($category) {
            $this->db->where('category', $category);
        }

        if ($sub_category) {
            $this->db->where('sub_category', $sub_category);
        }

        return $this->db->count_all_results();
    }

    public function category_exists($cat_name)
    {
        $this->db->from('categories');
        $this->db->where('cat_name', $cat_name);
        return $this->db->count_all_results() > 0;
    }

    public function insert_category($data)
    {
        return $this->db->insert('categories', $data);
    }

    public function get_all_categories()
    {
        return $this->db->get('categories')->result_array();
    }

    public function subcategory_exists($sub_cat_name, $parent_cat_id)
    {
        $this->db->from('subcategories');
        $this->db->where('sub_cat_name', $sub_cat_name);
        $this->db->where('parent_cat_id', $parent_cat_id);
        return $this->db->count_all_results() > 0;
    }

    public function insert_subcategory($data)
    {
        return $this->db->insert('subcategories', $data);
    }

    public function get_subcategories_by_parent($parent_cat_id = null)
    {
        $this->db->from('subcategories');
        if ($parent_cat_id) {
            $this->db->where('parent_cat_id', $parent_cat_id);
        }
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_by_id_with_names($id)
    {
        $this->db->select('p.*, c.cat_name AS category_name, sc.sub_cat_name AS sub_category_name');
        $this->db->from('products p');
        $this->db->join('categories c', 'p.category = c.cat_id', 'left');
        $this->db->join('subcategories sc', 'p.sub_category = sc.sub_cat_id', 'left');
        $this->db->where('p.id', $id);
        return $this->db->get()->row();
    }
}
