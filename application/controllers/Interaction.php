<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property CI_Session session
 * @property CI_Lang lang
 */
class Interaction extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');

        $lang = $this->session->userdata('lang') ?? 'english';
        $this->lang->load('messages', $lang);
    }

    public function index()
    {
        $data = [
            'welcome' => $this->lang->line('welcome'),
            'description' => $this->lang->line('description'),
            'select_language' => $this->lang->line('select_language')
        ];
        $this->load->view('language/interaction', $data);
    }

    public function set_language($language = 'english')
    {
        $available = ['english', 'kannada', 'hindi'];
        if (in_array($language, $available)) {
            $this->session->set_userdata('lang', $language);
        }
        redirect('interaction');
    }
}
