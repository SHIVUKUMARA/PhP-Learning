<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property User_model $User_model
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_Upload $upload
 */
class Profile extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');

        if (!$this->session->userdata('user_id')) {
            redirect('login');
        }

        $logged_user_id = $this->session->userdata('user_id');
        $logged_user = $this->User_model->get_user_by_id($logged_user_id);

        $this->set_avatar_url($logged_user);

        $this->load->vars(['logged_user' => $logged_user]);
    }

    private function can_access($target_user_id, $action)
    {
        $current_user_id = $this->session->userdata('user_id');
        $role = $this->session->userdata('role');

        if ($role === 'admin') return true;

        if ($role === 'manager') {
            if ($target_user_id == $current_user_id) return true;
            if ($action === 'view') return true;
            return false;
        }

        if ($role === 'customer') {
            return $target_user_id == $current_user_id;
        }

        return false;
    }

    public function profile($user_id = null)
    {
        if (!$user_id) $user_id = $this->session->userdata('user_id');

        if (!$this->can_access($user_id, 'view')) {
            show_error('Access Denied', 403);
        }

        $data['user'] = $this->User_model->get_user_by_id($user_id);
        $this->set_avatar_url($data['user']);

        $data['can_edit']   = $this->can_access($user_id, 'edit');
        $data['can_delete'] = $this->can_access($user_id, 'delete');

        $this->load->view('profile/profile', $data);
    }

    public function edit($user_id = null)
    {
        if (!$user_id) $user_id = $this->session->userdata('user_id');

        if (!$this->can_access($user_id, 'edit')) {
            show_error('Access Denied', 403);
        }

        $data['user'] = $this->User_model->get_user_by_id($user_id);
        if (!$data['user']) show_404();

        $this->set_avatar_url($data['user']);

        $this->load->view('profile/update', $data);
    }

    public function update()
    {
        $editing_user = $this->input->post('user_id');

        if (!$this->can_access($editing_user, 'edit')) {
            show_error('Access Denied', 403);
        }

        $data = [
            'fullname' => $this->input->post('fullname'),
            'fname'    => $this->input->post('fname'),
            'lname'    => $this->input->post('lname'),
            'status'   => $this->input->post('status'),
        ];

        // Only admin can update role
        $current_role = $this->session->userdata('role');
        if ($current_role === 'admin' && $this->input->post('role')) {
            $data['role'] = $this->input->post('role');
        }

        if (!empty($_FILES['userfile']['name'])) {

            $upload_path = realpath(APPPATH . '../assets/uploads');

            if (!$upload_path) {
                show_error('Upload directory not found');
            }

            $config['upload_path']   = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size']      = 5120;
            $config['encrypt_name']  = TRUE;

            $this->load->library('upload');
            $this->upload->initialize($config);

            if (!$this->upload->do_upload('userfile')) {
                $error = $this->upload->display_errors();
                $this->session->set_flashdata('error', $error);
                redirect('profile/update/' . $editing_user);
                return;
            }

            $upload_data = $this->upload->data();
            $data['avatar'] = $upload_data['file_name'];

            $old = $this->User_model->get_user_by_id($editing_user);
            if (!empty($old->avatar) && file_exists($upload_path . '/' . $old->avatar)) {
                unlink($upload_path . '/' . $old->avatar);
            }
        }

        $this->User_model->update_user($editing_user, $data);
        redirect('profile/profile/' . $editing_user);
    }

    public function delete($user_id = null)
    {
        if (!$user_id) $user_id = $this->session->userdata('user_id');

        if (!$this->can_access($user_id, 'delete')) {
            show_error('Access Denied', 403);
        }

        $this->User_model->delete_user($user_id);

        if ($user_id == $this->session->userdata('user_id')) {
            $this->session->sess_destroy();
            redirect('login');
        } else {
            redirect('dashboard/table');
        }
    }

    private function set_avatar_url(&$user)
    {
        $upload_dir = FCPATH . 'assets/uploads/';
        $user->avatar_url = (!empty($user->avatar) && file_exists($upload_dir . $user->avatar))
            ? base_url('assets/uploads/' . $user->avatar)
            : base_url('assets/uploads/user_default.png');
    }
}
