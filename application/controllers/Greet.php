<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** 
 * @property CI_Session session
 * @property User_model User_model
 */
class Greet extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->library('session'); 
        $this->load->helper('url'); 
        $this->load->model('User_model'); 
    }

    public function index(){
        if(!$this->session->userdata('logged_in')){
            redirect('auth/login');
        }

        $user_id = $this->session->userdata('user_id'); 
        $user = $this->User_model->get_user($user_id);

        $data['user'] = $user;             
        $data['fullname'] = $user->fullname; 
        $data['body_class'] = 'hold-transition login-page';
        $this->load->view('dashboard/greet_message', $data);
    }
}
