<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_Session $session
 */
class Greet extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->library('session'); 
        $this->load->helper('url'); 
    }

    public function index(){
        // Check if user is logged in
        if(!$this->session->userdata('logged_in')){
            redirect('auth/login');
        }

        $data['fullname'] = $this->session->userdata('fullname');
        $data['body_class'] = 'hold-transition login-page';
        $this->load->view('dashboard/greet_message', $data);
    }
}
