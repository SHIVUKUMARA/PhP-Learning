<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    // Register new user
    public function register($data){
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        return $this->db->insert('users', $data);
    }

    // Check if email exists
    public function email_exists($email){
        $this->db->where('email', $email);
        $query = $this->db->get('users');
        return $query->num_rows() > 0;
    }

    // Login verification
    public function login($email, $password){
        $this->db->where('email', $email);
        $query = $this->db->get('users');
        if($query->num_rows() == 1){
            $user = $query->row();
            if(password_verify($password, $user->password)){
                return $user;
            }
        }
        return false;
    }

    public function get_user($id){
        return $this->db->get_where('users', ['id' => $id])->row();
    }

    public function get_user_by_id($id){
        return $this->db->get_where('users', ['id' => $id])->row();
    }

    public function update_user($id, $data){
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }

    public function delete_user($id){
        $this->db->where('id', $id);
        return $this->db->delete('users');
    }

    // Fetch users with limit and offset
    public function get_users($limit, $offset){
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get('users', $limit, $offset);
        return $query->result();
    }

    // Get total users count
    public function get_total_users(){
        return $this->db->count_all('users');
    }
}
?>
