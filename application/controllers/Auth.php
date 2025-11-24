<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property User_model $User_model
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_Form_validation $form_validation
 * @property CI_Email $email
 */
class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
    }

    // Register page
    public function register()
    {
        $this->redirect_if_logged_in();
        $data['body_class'] = 'hold-transition login-page';
        $this->load->view('auth/register', $data);
    }

    // Handling registration form submission
    public function register_submit()
    {
        $this->form_validation->set_rules('fullname', 'Full Name', 'required|min_length[3]|max_length[50]|trim|xss_clean');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]|trim|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('agree_terms', 'Terms', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('auth/register');
            return;
        }

        $data = [
            'fullname'    => $this->input->post('fullname', true),
            'email'       => $this->input->post('email', true),
            'password'    => $this->input->post('password', true),
            'agree_terms' => 1,
            'role'        => 'customer'
        ];

        if ($this->User_model->register($data)) {
            $this->session->set_flashdata('success', 'Registration successful. You can now login.');
            redirect('auth/login');
        } else {
            $this->session->set_flashdata('error', 'Something went wrong. Please try again.');
            redirect('auth/register');
        }
    }

    // Login page
    public function login()
    {
        $this->redirect_if_logged_in();
        $data['body_class'] = 'hold-transition login-page';
        $this->load->view('auth/login', $data);
    }

    // Handling login
    public function login_submit()
    {
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('auth/login');
            return;
        }

        $email = $this->input->post('email', true);
        $password = $this->input->post('password', true);

        $user = $this->User_model->login($email, $password);

        if ($user) {
            $this->session->sess_regenerate(TRUE);
            $this->session->set_userdata([
                'user_id'   => $user->id,
                'fullname'  => $user->fullname,
                'email'     => $user->email,
                'role'      => $user->role,
                'logged_in' => TRUE
            ]);
            redirect('greet');
        } else {
            $this->session->set_flashdata('error', 'Invalid email or password.');
            redirect('auth/login');
        }
    }

    // checking if user already logged-in or not
    private function redirect_if_logged_in()
    {
        if ($this->session->userdata('logged_in')) {
            redirect('greet');
        }
    }

    // Forgot Password page
    public function forgot_password()
    {
        $data['body_class'] = 'hold-transition login-page';
        $this->redirect_if_logged_in();
        $this->load->view('auth/forgot_password', $data);
    }

    // Logout
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth/login');
    }

    public function send_reset_link()
    {
        $email = $this->input->post('email', true);

        if (!$email) {
            $this->session->set_flashdata('error', 'Please enter your email.');
            redirect('auth/forgot_password');
        }

        $user = $this->User_model->get_user_by_email($email);

        if (!$user) {
            $this->session->set_flashdata('error', 'Email not found.');
            redirect('auth/forgot_password');
        }

        // Generate token & expiry
        $token = bin2hex(random_bytes(50));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $this->User_model->save_reset_token($user->id, $token, $expiry);

        $reset_link = base_url("auth/reset_password/$token");

        // HTML email
        $message = "
    <html>
    <body style='font-family: Arial, sans-serif;'>
        <h2>Password Reset Request</h2>
        <p>Hello {$user->fullname},</p>
        <p>Click the link below to reset your password:</p>
        <p><a href='{$reset_link}' style='padding: 10px 20px; background: #28a745; color: #fff; text-decoration: none;'>Reset Password</a></p>
        <p>This link will expire in 1 hour.</p>
    </body>
    </html>
    ";

        // Send email
        $this->load->library('email');
        $this->email->from('shivukumaraspatil01@gmail.com', 'AdminLTE');
        $this->email->to($email);
        $this->email->subject('Reset Password Request');
        $this->email->message($message);

        if ($this->email->send()) {
            $this->session->set_flashdata('success', 'Reset link sent to your email.');
        } else {
            $this->session->set_flashdata('error', 'Error sending email: ' . $this->email->print_debugger());
        }

        redirect('auth/forgot_password');
    }

    // Show Reset Password Form
    public function reset_password($token = null)
    {
        if (!$token) {
            show_404();
            return;
        }

        $user = $this->User_model->get_user_by_token($token);

        if (!$user) {
            $this->session->set_flashdata('error', 'Invalid or expired reset token.');
            redirect('auth/forgot_password');
        }

        $data['token'] = $token;
        $this->load->view('auth/reset_password', $data);
    }

    // Handle Update Password Form Submission
    public function update_password()
    {
        $token = $this->input->post('token', true);
        $password = $this->input->post('password', true);

        if (!$token || !$password) {
            $this->session->set_flashdata('error', 'Invalid request.');
            redirect('auth/forgot_password');
        }

        $user = $this->User_model->get_user_by_token($token);

        if (!$user) {
            $this->session->set_flashdata('error', 'Invalid or expired reset token.');
            redirect('auth/forgot_password');
        }

        // Update password
        $this->User_model->update_password($user->id, $password);
        $this->User_model->clear_reset_token($user->id);

        $this->session->set_flashdata('success', 'Password updated successfully. You can now login.');
        redirect('auth/login');
    }
}
