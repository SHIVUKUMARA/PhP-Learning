<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property CI_Upload $upload
 */
class Upload extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['form', 'url']);
        $this->load->library('upload');
    }

    public function index()
    {
        $this->load->view('profile/upload', ['error' => '']);
    }

    public function do_upload()
    {
        // Upload directory
        $upload_dir = FCPATH . 'uploads/';

        // Create folder if not exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, TRUE);
        }

        // Fix Windows path
        $absolute_path = realpath($upload_dir);
        $absolute_path = str_replace('\\', '/', $absolute_path) . '/';

        // CI Upload Config
        $config['upload_path']   = $absolute_path;
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size']      = 5120; // 5 MB

        $this->upload->initialize($config);

        // Debug
        echo "<pre>";
        echo "Resolved Path Used: " . $config['upload_path'] . "\n";
        echo "Is directory: " . (is_dir($config['upload_path']) ? 'TRUE' : 'FALSE') . "\n";
        echo "Is writable: " . (is_writable($config['upload_path']) ? 'TRUE' : 'FALSE') . "\n";
        echo "</pre>";

        // Upload now
        if (!$this->upload->do_upload('userfile')) {
            $error = ['error' => $this->upload->display_errors()];
            $this->load->view('profile/upload', $error);
        } else {
            $data = ['upload_data' => $this->upload->data()];
            $this->load->view('profile/uploadsucc', $data);
        }
    }
}
