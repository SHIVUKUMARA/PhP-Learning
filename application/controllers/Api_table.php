<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property CI_Session $session
 * @property User_model $User_model
 */
class Api_table extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->helper(['url', 'form']);
        $this->load->library(['session']);

        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        $this->load->model('User_model');

        $logged_user = $this->User_model->get_user_by_id($this->session->userdata('user_id'));
        $this->load->vars(['logged_user' => $logged_user]);
    }

    public function index()
    {
        $data = [];
        $data['title'] = "API Users Table";
        $this->load->view('dashboard/api_table', $data);
    }

    private function set_avatar_url(&$user)
    {
        $upload_dir = FCPATH . 'assets/uploads/';
        $user->avatar_url = (!empty($user->avatar) && file_exists($upload_dir . $user->avatar))
            ? base_url('assets/uploads/' . $user->avatar)
            : base_url('assets/uploads/user_default.png');
    }
}
