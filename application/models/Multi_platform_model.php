<?php defined('BASEPATH') or exit('No direct script access allowed');

class Multi_platform_model extends CI_Model
{
    public function search_products($term = '')
    {
        $this->db->select('id, product_name')->from('omni_products');
        if ($term) $this->db->like('product_name', $term);
        return $this->db->get()->result_array();
    }

    public function already_published($product_id, $platform)
    {
        return $this->db->where([
            'product_id' => $product_id,
            'platform'   => $platform,
            'status'     => 'published'
        ])->get('omni_product_publish')->row();
    }

    public function update_result($id, $status, $platform_id = null, $error = null)
    {
        $this->db->where('id', $id)->update('omni_product_publish', [
            'status' => $status,
            'platform_product_id' => $platform_id,
            'error_message' => $error
        ]);
    }

    public function get_publish_status($product_ids)
    {
        $this->db->select('opp.*, op.product_name')
            ->from('omni_product_publish opp')
            ->join('omni_products op', 'op.id = opp.product_id');

        if ($product_ids) {
            $this->db->where_in('opp.product_id', $product_ids);
        }

        return $this->db->order_by('opp.created_at', 'DESC')->get()->result_array();
    }

    public function insert_pending($product_id, $platform)
    {
        $data = [
            'product_id' => $product_id,
            'platform'   => $platform,
            'status'     => 'processing',
            'error_message' => null,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Check if entry already exists to avoid primary key/unique issues
        $existing = $this->db->get_where('omni_product_publish', [
            'product_id' => $product_id,
            'platform' => $platform
        ])->row();

        if ($existing) {
            $this->db->where('id', $existing->id)->update('omni_product_publish', $data);
            return $existing->id;
        } else {
            $this->db->insert('omni_product_publish', $data);
            return $this->db->insert_id();
        }
    }

    public function delete_history($id)
    {
        return $this->db->where('id', $id)->delete('omni_product_publish');
    }
}
