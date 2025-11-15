<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Register new user
    public function register($data){
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        if($this->db->insert('users', $data)){
            return true;
        } else {
            log_message('error', 'DB Insert Error: ' . $this->db->last_query());
            return false;
        }
    }

    // Check if email already exists
    public function email_exists($email){
        $this->db->where('email', $email);
        $query = $this->db->get('users');
        return $query->num_rows() > 0;
    }

    // Verify email and password while login 
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
}
?>
