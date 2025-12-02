<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property CI_Input $input
 * @property CI_Session $session
 * @property Product_model $Product_model
 * @property User_model $User_model
 * @property CI_Upload $upload
 */
class Products extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url', 'form']);
        $this->load->library(['session', 'upload']);
        $this->load->database();

        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        $this->load->model('Product_model');
        $this->load->model('User_model');

        $logged_user = $this->User_model->get_user_by_id($this->session->userdata('user_id'));
        $this->load->vars(['logged_user' => $logged_user]);
    }

    private function is_admin()
    {
        return $this->session->userdata('role') === 'admin';
    }

    public function index()
    {
        $category = $this->input->get('category');
        $sub_category = $this->input->get('sub_category');

        $data['products'] = $this->Product_model->get_all($category, $sub_category);
        $data['categories'] = $this->Product_model->get_categories();
        $data['subcategories'] = $this->Product_model->get_subcategories($category);
        $data['selected_category'] = $category;
        $data['selected_subcategory'] = $sub_category;

        $this->load->view('products/list', $data);
    }

    public function add()
    {
        if (!$this->is_admin()) show_error('Unauthorized', 403);

        if ($this->input->post()) {
            $image_name = null;

            // File upload
            if (!empty($_FILES['image']['name'])) {
                $image_name = $this->upload_image('image');
            }

            // Online image URL
            if (empty($image_name) && $this->input->post('image_url')) {
                $image_name = $this->input->post('image_url');
            }

            $product = [
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'category' => $this->input->post('category'),
                'sub_category' => $this->input->post('sub_category'),
                'stock' => $this->input->post('stock'),
                'availability' => $this->input->post('availability'),
                'price' => $this->input->post('price'),
                'image' => $image_name
            ];

            $this->Product_model->insert($product);
            redirect('products');
        }

        $this->load->view('products/add');
    }

    public function edit($id)
    {
        if (!$this->is_admin()) show_error('Unauthorized', 403);

        $data['product'] = $this->Product_model->get_by_id($id);
        if (!$data['product']) show_404();

        if ($this->input->post()) {
            $image_name = null;

            // File upload
            if (!empty($_FILES['image']['name'])) {
                $image_name = $this->upload_image('image', $data['product']->image);
            }

            // Online image URL
            if (empty($image_name) && $this->input->post('image_url')) {
                $image_name = $this->input->post('image_url');

                // Delete old local file if exists
                if (!empty($data['product']->image) && filter_var($data['product']->image, FILTER_VALIDATE_URL) === false) {
                    $old_path = FCPATH . 'assets/uploads/products/' . $data['product']->image;
                    if (file_exists($old_path)) unlink($old_path);
                }
            }

            $update = [
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'category' => $this->input->post('category'),
                'sub_category' => $this->input->post('sub_category'),
                'stock' => $this->input->post('stock'),
                'availability' => $this->input->post('availability'),
                'price' => $this->input->post('price'),
                'image' => $image_name ?? $data['product']->image,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->Product_model->update($id, $update);
            redirect('products');
        }

        $this->load->view('products/edit', $data);
    }

    public function delete($id)
    {
        if (!$this->is_admin()) show_error('Unauthorized', 403);

        $product = $this->Product_model->get_by_id($id);
        if ($product && !empty($product->image) && filter_var($product->image, FILTER_VALIDATE_URL) === false) {
            $upload_path = FCPATH . 'assets/uploads/products/' . $product->image;
            if (file_exists($upload_path)) unlink($upload_path);
        }

        $this->Product_model->delete($id);
        redirect('products');
    }

    private function upload_image($field_name, $old_image = null)
    {
        if (empty($_FILES[$field_name]['name'])) return $old_image;

        $upload_path = realpath(APPPATH . '../assets/uploads/products');
        if (!is_dir($upload_path)) mkdir($upload_path, 0755, true);

        $config = [
            'upload_path' => $upload_path,
            'allowed_types' => 'jpg|jpeg|png|gif',
            'max_size' => 2048,
            'encrypt_name' => TRUE
        ];

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ($this->upload->do_upload($field_name)) {
            $upload_data = $this->upload->data();

            // Delete old local image if exists
            if (!empty($old_image) && filter_var($old_image, FILTER_VALIDATE_URL) === false && file_exists($upload_path . '/' . $old_image)) {
                unlink($upload_path . '/' . $old_image);
            }

            return $upload_data['file_name'];
        }

        return false;
    }

    public function view($id)
    {
        $data['product'] = $this->Product_model->get_by_id($id);
        if (!$data['product']) show_404();
        $this->load->view('products/view', $data);
    }
}
