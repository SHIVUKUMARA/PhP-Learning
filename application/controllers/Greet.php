<?php
defined('BASEPATH') or exit('No direct script access allowed');

/** 
 * @property CI_Session session
 * @property User_model User_model
 */
class Greet extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->model('User_model');
    }

    public function index()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        $user_id = $this->session->userdata('user_id');
        $logged_user = $this->User_model->get_user($user_id);

        // Set avatar URL if needed
        $upload_dir = FCPATH . 'assets/uploads/';
        $logged_user->avatar_url = (!empty($logged_user->avatar) && file_exists($upload_dir . $logged_user->avatar))
            ? base_url('assets/uploads/' . $logged_user->avatar)
            : base_url('assets/uploads/user_default.png');

        $data['logged_user'] = $logged_user;
        $data['fullname'] = $logged_user->fullname;
        $data['body_class'] = 'hold-transition login-page';
        $this->load->view('dashboard/greet_message', $data);
    }
}
