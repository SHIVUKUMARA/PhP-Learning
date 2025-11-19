<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**     
 * @property User_model $User_model
 * @property CI_Session $session
 * @property CI_Input $input
 */
class Profile extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');

        if (!$this->session->userdata('user_id')) {
            redirect('login');
        }
    }

    // Show Profile Page
    public function profile() {
        $user_id = $this->session->userdata('user_id');
        $data['user'] = $this->User_model->get_user_by_id($user_id);

        $this->load->view('profile/profile', $data);
    }

    /// Show Edit Form
public function edit() {
    $user_id = $this->session->userdata('user_id');
    $data['user'] = $this->User_model->get_user_by_id($user_id);

    $this->load->view('profile/edit', $data);
}

// Handle Form Submission
public function update() {
    $user_id = $this->session->userdata('user_id');

    $data = [
        'fullname' => $this->input->post('fullname'),
    ];

    if ($this->User_model->update_user($user_id, $data)) {
        $this->session->set_flashdata('success', 'Profile updated successfully.');
    } else {
        $this->session->set_flashdata('error', 'Failed to update profile.');
    }

    redirect('profile/profile');
}

    // Delete Profile
    public function delete() {
        $user_id = $this->session->userdata('user_id');

        if ($this->User_model->delete_user($user_id)) {
            $this->session->sess_destroy();
            redirect('register');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete account.');
            redirect('profile');
        }
    }
}
