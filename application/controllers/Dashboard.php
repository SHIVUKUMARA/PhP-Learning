<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property User_model $User_model
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_Form_validation $form_validation
 * @property CI_Pagination $pagination
 * @property CI_Uri $uri
 */

class Dashboard extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('pagination');
    }

    // Dashboard main page
    public function dashboard() {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        $user_id = $this->session->userdata('user_id');
        $data['user'] = $this->User_model->get_user_by_id($user_id);
        $this->load->view('dashboard/dashboard', $data);
    }

    // Users table with pagination
    public function table() {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        $user_id = $this->session->userdata('user_id');
        $data['user'] = $this->User_model->get_user_by_id($user_id);
        $config['base_url'] = base_url('dashboard/table');
        $config['total_rows'] = $this->User_model->get_total_users();
        $config['per_page'] = 10;
        $config['use_page_numbers'] = TRUE;

        // Bootstrap 5 styling
        $config['full_tag_open'] = '<nav><ul class="pagination justify-content-center">';
        $config['full_tag_close'] = '</ul></nav>';
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '&raquo';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><span class="page-link">';
        $config['cur_tag_close'] = '</span></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = ['class' => 'page-link'];

        $this->pagination->initialize($config);

        $page_number = $this->uri->segment(3);
        $page_number = ($page_number && ctype_digit($page_number)) ? (int)$page_number : 1;
        $offset = ($page_number - 1) * $config['per_page'];

        $data['users'] = $this->User_model->get_users($config['per_page'], $offset);
        $data['offset'] = $offset;
        $data['current_page'] = $page_number;
        $data['total_rows'] = $config['total_rows'];

        $this->load->view('dashboard/table', $data);
    }

    // View user
    public function view_user($id){
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        $data['user'] = $this->User_model->get_user_by_id($id);
        if (!$data['user']) show_404();
        $this->load->view('profile/profile', $data);
    }

    // Edit user
    public function edit_user($id){
        if (!$this->session->userdata('logged_in')) redirect('auth/login');

        $data['user'] = $this->User_model->get_user_by_id($id);
        if (!$data['user']) show_404();

        if ($this->input->post()) {
            $update_data = [
                'fullname'  => $this->input->post('fullname', TRUE),
                'fname' => $this->input->post('fname', TRUE),
                'lname' => $this->input->post('lname', TRUE),
                'status' => $this->input->post('status', TRUE),
                'last_updated' => date('Y-m-d H:i:s')
            ];
            $this->User_model->update_user($id, $update_data);
            $this->session->set_flashdata('success', 'User updated successfully!');
            redirect('dashboard/table');
        }

        $this->load->view('profile/update', $data);
    }

    // Delete user
    public function delete_user($id){
        if (!$this->session->userdata('logged_in')) redirect('auth/login');
        $this->User_model->delete_user($id);
        $this->session->set_flashdata('success', 'User deleted successfully!');
        redirect('dashboard/table');
    }

    public function create_admin() {
    if (!$this->session->userdata('logged_in')) {
        redirect('auth/login');
    }

    if ($this->input->post('save') === 'create') {

        // Collect form inputs
        $fullname = $this->input->post('fullname', TRUE);
        $fname    = $this->input->post('fname', TRUE);
        $lname    = $this->input->post('lname', TRUE);
        $email    = $this->input->post('email', TRUE);
        $password = $this->input->post('password', TRUE);

        // Validate required fields
        if (!$fullname || !$email || !$password) {
            $this->session->set_flashdata('error', 'Full Name, Email, and Password are required!');
            redirect('dashboard/create_admin');
        }

        // Check if email already exists
        if ($this->User_model->email_exists($email)) {
            $this->session->set_flashdata('error', 'Email already exists!');
            redirect('dashboard/create_admin');
        }

        // Prepare data
        $data = [
            'fullname'    => $fullname,
            'fname'       => $fname ?: NULL,
            'lname'       => $lname ?: NULL,
            'email'       => $email,
            'password'    => password_hash($password, PASSWORD_BCRYPT),
            'status'      => 'active',
            'agree_terms' => 1,
            'created_at'  => date('Y-m-d H:i:s'),
            'last_updated'=> date('Y-m-d H:i:s')
        ];

        // Insert into database
        $this->User_model->register($data);
        $this->session->set_flashdata('success', 'Admin user created successfully!');
        redirect('dashboard/table');
    }

    // Load the create view
    $this->load->view('profile/create');
    }

}
?>
