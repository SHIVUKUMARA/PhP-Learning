<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property CI_Upload $upload
 * @property CI_Session $session
 * @property CI_DB_query_builder $db
 */
class Upload extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function index()
    {
        $this->load->view('profile/upload', ['error' => '']);
    }

    public function do_upload()
    {
        $user_id = $this->session->userdata('user_id');

        if (!$user_id) {
            show_error('User not logged in.');
        }

        $upload_dir = FCPATH . 'assets/uploads/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, TRUE);
        }

        $absolute_path = realpath($upload_dir);
        $absolute_path = str_replace('\\', '/', $absolute_path) . '/';

        $file_ext = pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION);

        $new_filename = $user_id . '.' . $file_ext;

        foreach (glob($absolute_path . $user_id . '.*') as $oldfile) {
            if (file_exists($oldfile)) {
                unlink($oldfile);
            }
        }

        $config['upload_path']   = $absolute_path;
        $config['allowed_types'] = 'jpg|jpeg|png|gif';
        $config['max_size']      = 5120;
        $config['file_name']     = $new_filename;
        $config['overwrite']     = TRUE;

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('userfile')) {

            $error = ['error' => $this->upload->display_errors()];
            $this->load->view('profile/upload', $error);
        } else {

            $upload_data = $this->upload->data();

            $this->db->where('id', $user_id)
                ->update('users', ['avatar' => $upload_data['file_name']]);

            $this->load->view('profile/uploadsucc', ['upload_data' => $upload_data]);
        }
    }
}
