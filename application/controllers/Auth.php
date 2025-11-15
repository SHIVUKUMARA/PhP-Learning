<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property User_model $User_model
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_Form_validation $form_validation
 */
class Auth extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper(['url', 'form', 'security']);
    }

    // Register page
    public function register(){
        $this->load->view('auth/register');
    }

    // Handling registration form submission
    public function register_submit(){
        $this->form_validation->set_rules('fullname', 'Full Name', 'required|min_length[3]|max_length[50]|trim|xss_clean');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]|trim|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('agree_terms', 'Terms', 'required');

        if($this->form_validation->run() === FALSE){
            $this->load->view('auth/register');
            return;
        }

        $data = [
            'fullname'    => $this->input->post('fullname', true),
            'email'       => $this->input->post('email', true),
            'password'    => $this->input->post('password', true),
            'agree_terms' => 1
        ];

        if($this->User_model->register($data)){
            $this->session->set_flashdata('success', 'Registration successful. You can now login.');
            redirect('auth/login');
        } else {
            $this->session->set_flashdata('error', 'Something went wrong. Please try again.');
            redirect('auth/register');
        }
    }

    // Login page
    public function login(){
        $this->load->view('auth/login');
    }

    // Handling login
    public function login_submit(){
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if($this->form_validation->run() === FALSE){
            $this->load->view('auth/login');
            return;
        }

        $email = $this->input->post('email', true);
        $password = $this->input->post('password', true);

        $user = $this->User_model->login($email, $password);

        if($user){
            $this->session->sess_regenerate(TRUE);
            $this->session->set_userdata([
                'user_id'   => $user->id,
                'fullname'  => $user->fullname,
                'email'     => $user->email,
                'logged_in' => TRUE
            ]);
            redirect('greet'); 
        } else {
            $this->session->set_flashdata('error', 'Invalid email or password.');
            redirect('auth/login');
        }
    }

    // Logout
    public function logout(){
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}
?>
