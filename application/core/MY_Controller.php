<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property User_model $User_model
 * @property CI_Session $session
 */
class MY_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');

        $user_id = $this->session->userdata('user_id');

        if ($user_id) {
            $current_user = $this->User_model->get_user_by_id($user_id);

            if ($current_user) {
                $upload_dir = FCPATH . 'assets/uploads/';
                $current_user->avatar_url = (!empty($current_user->avatar) && file_exists($upload_dir . $current_user->avatar))
                    ? base_url('assets/uploads/' . $current_user->avatar)
                    : base_url('assets/uploads/user_default.png');

                // Make available to ALL views globally
                $this->load->vars(['current_user' => $current_user]);
            }
        }
    }
}
