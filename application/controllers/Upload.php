<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**     
 * @property CI_Upload $upload
 */
class Upload extends CI_Controller {

     public function index()
    {
        $this->load->view('upload');
    }

    public function upload_file(){
        $config['upload_path'] = './assets/uploads/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 10240;

        if (empty($_FILES['file']['name'])) {
            echo "<b>NO FILE RECEIVED!</b><br>";
            print_r($_FILES);
            return;
        }

        if(!$this->upload->do_upload('file')){
            $error = array('error' => $this->upload->display_errors());
            print_r($error);        
        }else{
            $data = $this->upload->data();
            echo "<br>File uploaded successfully!</br>";
            echo "File name : " . $data['file_name'] . "<br>";
        }
    }
}