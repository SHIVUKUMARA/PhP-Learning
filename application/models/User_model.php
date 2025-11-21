<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // Register new user
    public function register($data)
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        return $this->db->insert('users', $data);
    }

    // Check if email exists
    public function email_exists($email)
    {
        $this->db->where('email', $email);
        $query = $this->db->get('users');
        return $query->num_rows() > 0;
    }

    // Login verification
    public function login($email, $password)
    {
        $this->db->where('email', $email);
        $query = $this->db->get('users');
        if ($query->num_rows() == 1) {
            $user = $query->row();
            if (password_verify($password, $user->password)) {
                $this->set_avatar_url($user);
                return $user;
            }
        }
        return false;
    }

    // Fetch single user by ID
    public function get_user($id)
    {
        $user = $this->db->get_where('users', ['id' => $id])->row();
        if ($user) $this->set_avatar_url($user);
        return $user;
    }

    public function get_user_by_id($id)
    {
        $user = $this->db->get_where('users', ['id' => $id])->row();
        if ($user) $this->set_avatar_url($user);
        return $user;
    }

    public function update_user($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }

    public function delete_user($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('users');
    }

    // Fetch users with limit and offset
    public function get_users($limit, $offset)
    {
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get('users', $limit, $offset);
        $users = $query->result();
        foreach ($users as $user) {
            $this->set_avatar_url($user);
        }
        return $users;
    }

    // Get total users count
    public function get_total_users()
    {
        return $this->db->count_all('users');
    }

    // Helper function to set avatar URL
    private function set_avatar_url(&$user)
    {
        $upload_dir = FCPATH . 'assets/uploads/';
        $user->avatar_url = (!empty($user->avatar) && file_exists($upload_dir . $user->avatar))
            ? base_url('assets/uploads/' . $user->avatar) . '?t=' . time()
            : base_url('assets/uploads/user_default.png');
    }
}
