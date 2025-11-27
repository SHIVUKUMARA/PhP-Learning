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
        if (!$this->input->is_ajax_request()) {
            show_error('No direct access allowed', 403);
        }

        $editing_user = $this->input->post('user_id');

        if (!$this->can_access($editing_user, 'edit')) {
            echo json_encode(['status' => 'error', 'message' => 'Access Denied']);
            return;
        }

        $data = [
            'fullname' => $this->input->post('fullname', TRUE),
            'fname'    => $this->input->post('fname', TRUE),
            'lname'    => $this->input->post('lname', TRUE),
            'status'   => $this->input->post('status', TRUE),
        ];

        $current_role = $this->session->userdata('role');
        if ($current_role === 'admin' && $this->input->post('role')) {
            $data['role'] = $this->input->post('role', TRUE);
        }

        if (!empty($_FILES['userfile']['name'])) {
            $upload_path = realpath(APPPATH . '../assets/uploads');
            $config = [
                'upload_path'   => $upload_path,
                'allowed_types' => 'jpg|jpeg|png|gif',
                'max_size'      => 5120,
                'encrypt_name'  => TRUE
            ];

            $this->load->library('upload');
            $this->upload->initialize($config);

            if (!$this->upload->do_upload('userfile')) {
                echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
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

        $updated_user = $this->User_model->get_user_by_id($editing_user);
        $this->set_avatar_url($updated_user);

        echo json_encode([
            'status' => 'success',
            'message' => 'Profile updated successfully!',
            'user' => [
                'fullname'   => $updated_user->fullname,
                'fname'      => $updated_user->fname,
                'lname'      => $updated_user->lname,
                'status'     => $updated_user->status,
                'role'       => $updated_user->role,
                'avatar_url' => $updated_user->avatar_url
            ]
        ]);
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
