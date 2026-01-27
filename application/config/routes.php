<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['login'] = 'auth/login';
$route['register'] = 'auth/register';
$route['forgot_password'] = 'auth/forgot_password';
$route['greet'] = 'greet';
$route['dashboard'] = 'dashboard/dashboard';
$route['profile'] = 'profile/profile';
$route['profile/edit'] = 'profile/edit';
$route['upload'] = 'upload/index';
$route['upload/upload_file'] = 'upload/upload_file';
$route['dashboard/table'] = 'dashboard/table';
$route['dashboard/view_user/(:num)'] = 'dashboard/view_user/$1';
$route['dashboard/edit_user/(:num)'] = 'dashboard/edit_user/$1';
$route['dashboard/delete_user/(:num)'] = 'dashboard/delete_user/$1';
$route['dashboard/create_users'] = 'dashboard/create_users';
$route['upload'] = 'upload/index';
$route['upload/do_upload'] = 'upload/do_upload';
$route['email'] = 'Email/index';
$route['auth/reset_password/(:any)'] = 'auth/reset_password/$1';
$route['interaction'] = 'interaction/index';
$route['interaction/lang/(:any)'] = 'interaction/set_language/$1';
$route['dashboard/api_table'] = 'Api_table/index';

// Products
$route['products'] = 'products/index';
$route['products/add'] = 'products/add';
$route['products/edit/(:num)'] = 'products/edit/$1';
$route['products/delete/(:num)'] = 'products/delete/$1';
$route['products/table'] = 'products/table';
$route['products/category'] = 'products/category';
$route['products/subcategory'] = 'products/subcategory';

// Omni Products upload using .xml file
$route['omni'] = 'Omni_products/index';
$route['omni/create'] = 'Omni_products/create';
$route['omni/store'] = 'Omni_products/store';
$route['omni/get_product/(:num)'] = 'Omni_products/get_product/$1';
$route['omni/edit/(:num)'] = 'Omni_products/edit/$1';
$route['omni/update/(:num)'] = 'Omni_products/update/$1';
$route['omni/delete/(:num)'] = 'Omni_products/delete/$1';


$route['omni/publish-view']        = 'Omni_publish/index';
$route['omni/publish/store']  = 'Omni_publish/publish';
$route['omni/published'] = 'Omni_publish/published_view';
$route['omni/published/api'] = 'Omni_publish/get_published_products';
$route['omni/published/api/delete/(:any)'] = 'Omni_publish/delete_published/$1';
$route['omni/published/api/update/(:any)'] = 'Omni_publish/update_published/$1';

$route['facebook-feed.xml'] = 'facebook_feed/index';


// Omni Product Upload using Graph API
$route['multi-platform'] = 'Multi_platform/index';
