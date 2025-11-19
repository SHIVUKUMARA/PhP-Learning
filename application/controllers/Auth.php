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
    }

    // Register page
    public function register(){
        $this->redirect_if_logged_in();
        $data['body_class'] = 'hold-transition login-page';
        $this->load->view('auth/register', $data);
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
        $this->redirect_if_logged_in();
        $data['body_class'] = 'hold-transition login-page';
        $this->load->view('auth/login', $data);
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

    // checking if user already logged-in or not
    private function redirect_if_logged_in() {
    if ($this->session->userdata('logged_in')) {
        redirect('greet');
    }
    }

    // Forgot Password page
    public function forgot_password(){
        $data['body_class'] = 'hold-transition login-page';
        $this->redirect_if_logged_in();
        $this->load->view('auth/forgot_password', $data);
    }

    // Logout
    public function logout(){
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}
?>
