<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property User_model $User_model
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_Form_validation $form_validation
 * @property CI_Pagination $pagination
 * @property CI_Uri $uri
 * @property CI_Output $output
 */

class Dashboard extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('pagination');
    }

    private function can_access_user_action($target_user_id, $action = 'view')
    {
        $current_user_id = $this->session->userdata('user_id');
        $current_role    = $this->session->userdata('role');

        switch ($current_role) {
            case 'admin':
                return true;

            case 'manager':
                if ($action === 'view') return true;
                if ($target_user_id == $current_user_id) return true;
                return false;

            case 'customer':
                if ($target_user_id == $current_user_id && in_array($action, ['view', 'edit', 'delete'])) return true;
                return false;

            default:
                return false;
        }
    }

    // Dashboard main page
    public function dashboard()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        $user_id = $this->session->userdata('user_id');
        $data['user'] = $this->User_model->get_user_by_id($user_id);
        $this->load->view('dashboard/dashboard', $data);
    }

    // Users table with pagination
    public function table()
    {
        $user_id = $this->session->userdata('user_id');
        $this->output->enable_profiler(TRUE);
        $current_role = $this->session->userdata('role');
        $data['user'] = $this->User_model->get_user_by_id($user_id);

        $config['base_url'] = base_url('dashboard/table');
        $config['total_rows'] = $this->User_model->get_total_users();
        $config['per_page'] = 10;
        $config['use_page_numbers'] = TRUE;

        // Pagination config (Bootstrap 5)
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

        $users = $this->User_model->get_users($config['per_page'], $offset);

        foreach ($users as &$u) {
            $u->can_view = $this->can_access_user_action($u->id, 'view');
            $u->can_edit = $this->can_access_user_action($u->id, 'edit');
            $u->can_delete = $this->can_access_user_action($u->id, 'delete');
        }

        $data['users'] = $users;
        $data['offset'] = $offset;
        $data['current_page'] = $page_number;
        $data['total_rows'] = $config['total_rows'];

        $this->load->view('dashboard/table', $data);
    }

    // View user
    public function view_user($id)
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        $data['user'] = $this->User_model->get_user_by_id($id);
        if (!$data['user']) show_404();
        $this->load->view('profile/profile', $data);
    }

    // Edit user
    public function edit_user($id)
    {
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
    public function delete_user($id)
    {
        if (!$this->session->userdata('logged_in')) redirect('auth/login');
        $this->User_model->delete_user($id);
        $this->session->set_flashdata('success', 'User deleted successfully!');
        redirect('dashboard/table');
    }

    public function create_admin()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        $data = [];

        if ($this->input->post('save') === 'create') {

            // Collect form inputs
            $fullname = $this->input->post('fullname', TRUE);
            $fname    = $this->input->post('fname', TRUE);
            $lname    = $this->input->post('lname', TRUE);
            $email    = $this->input->post('email', TRUE);
            $password = $this->input->post('password', TRUE);
            $role     = $this->input->post('role', TRUE) ?: 'customer';

            if (!$fullname || !$email || !$password) {
                $data['error'] = 'Full Name, Email, and Password are required!';
            } elseif ($this->User_model->email_exists($email)) {
                $data['error'] = 'Email already exists!';
            } else {
                $user_data = [
                    'fullname'    => $fullname,
                    'fname'       => $fname ?: NULL,
                    'lname'       => $lname ?: NULL,
                    'email'       => $email,
                    'password'    => $password,
                    'role'        => $role,
                    'status'      => 'active',
                    'agree_terms' => 1,
                    'created_at'  => date('Y-m-d H:i:s'),
                    'last_updated' => date('Y-m-d H:i:s')
                ];

                $this->User_model->register($user_data);
                $this->session->set_flashdata('success', 'User created successfully!');
                redirect('dashboard/table');
                return;
            }

            // Pass old input back to the view
            $data['fullname'] = $fullname;
            $data['fname']    = $fname;
            $data['lname']    = $lname;
            $data['email']    = $email;
            $data['role']     = $role;
        }

        $this->load->view('profile/create', $data);
    }
}
