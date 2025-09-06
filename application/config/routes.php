<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


$route["login-submit"] = "app/login_submit";
$route["register"] = "app/register";
$route["register-submit"] = "app/register_submit";
$route["forgot-password"] = "app/forgot_password";
$route["forgot-password-submit"] = "app/forgot_password_submit";
$route["logout"] = "app/logout";
$route["dashboard"] = "app/dashboard";
$route["profile"] = "app/profile";
$route["profile-submit"] = "app/profile_submit";
$route["product"] = "app/product";
$route["add-product"] = "app/add_product";
$route["add-product-submit"] = "app/add_product_submit";
$route["products-list"] = "app/products_list";
$route["product/(:num)"] = "app/get_product/$1";
$route["product-update"] = "app/update_product_submit";
