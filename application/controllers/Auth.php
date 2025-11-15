<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property User_model $User_model
 * @property CI_Session $session
 * @property CI_Input $input
 */
class Auth extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('session');
        $this->load->helper('url');
    }

    // Register page
    public function register(){
        $this->load->view('auth/register');
    }

    // Handle registration form submission
    public function register_submit(){
        $fullname = $this->input->post('fullname', true);
        $email    = $this->input->post('email', true);
        $password = $this->input->post('password', true);
        $terms    = $this->input->post('agree_terms'); 
        // Terms must be agreed
       $terms = $this->input->post('agree_terms'); 
       if(!$terms){
       $this->session->set_flashdata('error', 'You must agree to the terms and conditions.');
       redirect('auth/register');
    }

        // Check if email exists
        if($this->User_model->email_exists($email)){
            $this->session->set_flashdata('error', 'Email already registered.');
            redirect('auth/register');
        }

        // Prepare data
        $data = [
            'fullname'    => $fullname,
            'email'       => $email,
            'password'    => $password,
            'agree_terms' => 1
        ];

        // Insert user
        if($this->User_model->register($data)){
            $this->session->set_flashdata('success', 'Registration successful. You can now login.');
            redirect('auth/login');
        } else {
            $this->session->set_flashdata('error', 'Something went wrong. Please try again.');
            redirect('auth/register');
        }
    }

    // Login Page
    public function login() {
        $this->load->view('auth/login');
    }

    // Handle login form submission
    public function login_submit(){
        $email = $this->input->post('email', true);
        $password = $this->input->post('password', true);

        $user = $this->User_model->login($email, $password);

        if($user){
            $this->session->sess_regenerate(TRUE);
            $this->session->set_userdata([
                'user_id' => $user->id,
                'fullname' => $user->fullname,
                'email' => $user->email,
                'logged_in' => true
            ]);

            redirect('greet'); 
        } else {
            $this->session->set_flashdata('error', 'Invalid email or password.');
            redirect('auth/login');
        }
    }

    // Forgot Password
    public function forgot_password(){
        $this->load->view('auth/forgot_password');
    }

    // Logout and destroy the session
    public function logout(){
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}
?>