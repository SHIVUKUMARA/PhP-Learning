<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property CI_Input $input
 * @property CI_Output $output
 * @property CI_Session $session
 * @property Product_model $Product_model
 * @property User_model $User_model
 * @property CI_Upload $upload
 * @property CI_Pagination $pagination
 * @property CI_Uri $uri
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
        $categoryFilter = null;
        $subCategoryFilter = null;

        if ($this->input->get('category_sub')) {
            $parts = explode('||', $this->input->get('category_sub'));
            $categoryFilter = $parts[0] ?? null;
            $subCategoryFilter = $parts[1] ?? null;
        }

        $categories = $this->Product_model->get_categories();
        $nested_categories = [];
        foreach ($categories as $c) {
            $nested_categories[$c['category']] = $this->Product_model->get_subcategories($c['category']);
        }

        $data['nested_categories'] = $nested_categories;
        $data['selected_category'] = $categoryFilter;
        $data['selected_subcategory'] = $subCategoryFilter;

        $this->load->library('pagination');

        $config['base_url'] = site_url('products');
        $config['total_rows'] = $this->Product_model->count_filtered($categoryFilter, $subCategoryFilter);
        $config['per_page'] = 12;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';

        $config['full_tag_open'] = '<nav><ul class="pagination justify-content-center">';
        $config['full_tag_close'] = '</ul></nav>';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = ['class' => 'page-link'];


        $this->pagination->initialize($config);

        $page = $this->input->get('page') ?? 0;

        $data['products'] = $this->Product_model->get_filtered(
            $categoryFilter,
            $subCategoryFilter,
            $config['per_page'],
            $page
        );

        $data['pagination'] = $this->pagination->create_links();

        $this->load->view('products/list', $data);
    }

    public function add()
    {
        if (!$this->is_admin()) show_error('Unauthorized', 403);

        if ($this->input->post()) {
            $image_name = null;

            if (!empty($_FILES['image']['name'])) {
                $image_name = $this->upload_image('image');
            }

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

            $inserted = $this->Product_model->insert($product);

            echo json_encode([
                'success' => $inserted ? true : false,
                'message' => $inserted ? 'Product added successfully!' : 'Failed to add product.'
            ]);
            return;
        }

        $this->load->view('products/add');
    }

    public function edit($id)
    {
        if (!$this->is_admin()) show_error('Unauthorized', 403);

        $data['product'] = $this->Product_model->get_by_id($id);
        if (!$data['product']) show_404();

        $data['categories'] = array_column($this->Product_model->get_categories(), 'category');

        if ($this->input->post()) {
            $image_name = null;

            if (!empty($_FILES['image']['name'])) {
                $image_name = $this->upload_image('image', $data['product']->image);
                if ($image_name === false) {
                    return $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'success' => false,
                            'message' => 'Image upload failed'
                        ]));
                }
            }

            if (empty($image_name) && $this->input->post('image_url')) {
                $image_name = $this->input->post('image_url');
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

            $updated = $this->Product_model->update($id, $update);

            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => $updated ? true : false,
                    'message' => $updated ? 'Product updated successfully!' : 'Failed to update product!'
                ]));
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
            'allowed_types' => 'jpg|jpeg|png|gif|webp',
            'max_size' => 2048,
            'encrypt_name' => TRUE
        ];

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ($this->upload->do_upload($field_name)) {
            $upload_data = $this->upload->data();

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

    public function table()
    {
        $categoryFilter = null;
        $subCategoryFilter = null;

        if ($this->input->get('category_sub')) {
            $parts = explode('||', $this->input->get('category_sub'));
            $categoryFilter = $parts[0] ?? null;
            $subCategoryFilter = $parts[1] ?? null;
        }

        $categories = $this->Product_model->get_categories();
        $nested_categories = [];
        foreach ($categories as $c) {
            $nested_categories[$c['category']] = $this->Product_model->get_subcategories($c['category']);
        }

        $data['nested_categories'] = $nested_categories;
        $data['selected_category'] = $categoryFilter;
        $data['selected_subcategory'] = $subCategoryFilter;

        $config['base_url'] = base_url('products/table');
        $config['per_page'] = 10;
        $config['use_page_numbers'] = TRUE;
        $config['total_rows'] = $this->Product_model->count_filtered($categoryFilter, $subCategoryFilter);

        $config['full_tag_open'] = '<nav><ul class="pagination justify-content-center">';
        $config['full_tag_close'] = '</ul></nav>';
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $config['prev_link'] = '&laquo';
        $config['next_link'] = '&raquo';
        $config['cur_tag_open'] = '<li class="page-item active"><span class="page-link">';
        $config['cur_tag_close'] = '</span></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = ['class' => 'page-link'];

        $this->pagination->initialize($config);

        $page_number = $this->uri->segment(3);
        $page_number = ($page_number && ctype_digit($page_number)) ? (int)$page_number : 1;
        $offset = ($page_number - 1) * $config['per_page'];

        $data['products'] = $this->Product_model->get_filtered($categoryFilter, $subCategoryFilter, $config['per_page'], $offset);
        $data['offset'] = $offset;
        $data['current_page'] = $page_number;
        $data['total_rows'] = $config['total_rows'];

        $this->load->view('products/table', $data);
    }
}
