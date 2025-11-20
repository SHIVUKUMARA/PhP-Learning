<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**     
 * @property User_model $User_model
 * @property CI_Session $session
 * @property CI_Input $input
 */
class Profile extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');

        if (!$this->session->userdata('user_id')) {
            redirect('login');
        }
    }

    // Show Profile Page of any user
    public function profile($user_id = null)
    {
        // If no user_id provided, show logged-in user
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }

        $data['user'] = $this->User_model->get_user_by_id($user_id);

        if (!$data['user']) {
            show_404();
        }

        $this->load->view('profile/profile', $data);
    }

    // Show Edit Form
    public function edit($user_id = null)
    {
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }

        $data['user'] = $this->User_model->get_user_by_id($user_id);

        if (!$data['user']) {
            show_404();
        }

        $this->load->view('profile/edit', $data);
    }

    // Handle Update
    public function update()
    {
        $logged_in_user = $this->session->userdata('user_id');
        $editing_user   = $this->input->post('user_id');

        $data = [
            'fullname' => $this->input->post('fullname'),
            'fname'    => $this->input->post('fname'),
            'lname'    => $this->input->post('lname'),
            'status'   => $this->input->post('status')
        ];

        if ($this->User_model->update_user($editing_user, $data)) {

            if ($logged_in_user == $editing_user) {
                $this->session->set_userdata('fullname', $this->input->post('fullname'));
            }

            $this->session->set_flashdata('success', 'Profile updated successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to update profile.');
        }

        redirect('profile/profile/' . $editing_user);
    }

    // Delete Profile
    public function delete($user_id = null)
    {
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }

        if ($this->User_model->delete_user($user_id)) {
            if ($user_id == $this->session->userdata('user_id')) {
                $this->session->sess_destroy();
                redirect('register');
            } else {
                $this->session->set_flashdata('success', 'User deleted successfully.');
                redirect('dashboard/table'); // redirect to user list
            }
        } else {
            $this->session->set_flashdata('error', 'Failed to delete user.');
            redirect('profile/profile/' . $user_id);
        }
    }
}
