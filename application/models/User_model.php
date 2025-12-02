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

    public function get_user_by_email($email)
    {
        return $this->db->get_where('users', ['email' => $email])->row();
    }

    public function save_reset_token($user_id, $token, $expiry)
    {
        $this->db->update('users', ['reset_token' => $token, 'token_expiry' => $expiry], ['id' => $user_id]);
    }

    public function get_user_by_token($token)
    {
        return $this->db->where('reset_token', $token)
            ->where('token_expiry >=', date('Y-m-d H:i:s'))
            ->get('users')->row();
    }

    public function update_password($user_id, $password)
    {
        $this->db->update('users', ['password' => password_hash($password, PASSWORD_BCRYPT)], ['id' => $user_id]);
    }

    public function clear_reset_token($user_id)
    {
        $this->db->update('users', ['reset_token' => null, 'token_expiry' => null], ['id' => $user_id]);
    }

    public function clear_expired_tokens()
    {
        $this->db->where('token_expiry <', date('Y-m-d H:i:s'));
        $this->db->update('users', [
            'reset_token' => null,
            'token_expiry' => null
        ]);
    }

    // Count users by role
    public function count_users_by_role($role)
    {
        return $this->db->where('role', $role)
            ->count_all_results('users');
    }

    public function get_users_by_role($role, $limit, $offset)
    {
        $this->db->where('role', $role)
            ->order_by('id', 'ASC')
            ->limit($limit, $offset);
        $query = $this->db->get('users');
        $users = $query->result();
        foreach ($users as $user) {
            $this->set_avatar_url($user);
        }
        return $users;
    }

    public function search_users($column, $operator, $value, $limit, $offset)
    {
        $this->db->limit($limit, $offset);

        switch ($operator) {
            case 'equals':
                $this->db->where($column, $value);
                break;
            case 'not_equals':
                $this->db->where("$column !=", $value);
                break;
            case 'contains':
                $this->db->like($column, $value);
                break;
            default:
                $this->db->like($column, $value);
                break;
        }

        $query = $this->db->get('users');
        return $query->result();
    }

    public function count_search_users($column, $operator, $value)
    {
        switch ($operator) {
            case 'equals':
                $this->db->where($column, $value);
                break;
            case 'not_equals':
                $this->db->where("$column !=", $value);
                break;
            case 'contains':
                $this->db->like($column, $value);
                break;
            default:
                $this->db->like($column, $value);
                break;
        }

        return $this->db->count_all_results('users');
    }
}
