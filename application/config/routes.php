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

$route["supplier"] = "app/supplier";
$route["add-supplier"] = "app/add_supplier";
$route["add-supplier-submit"] = "app/add_supplier_submit";
$route["suppliers-list"] = "app/suppliers_list";
$route["supplier/(:num)"] = "app/get_supplier/$1";
$route["supplier-update"] = "app/update_supplier_submit";

$route["customer"] = "app/customer";
$route["add-customer"] = "app/add_customer";
$route["add-customer-submit"] = "app/add_customer_submit";
$route["customers-list"] = "app/customers_list";
$route["customer/(:num)"] = "app/get_customer/$1";
$route["customer-update"] = "app/update_customer_submit";

// PURCHASE
$route["purchase"]            = "app/purchase";
$route["add-purchase"]        = "app/add_purchase";
$route["add-purchase-submit"] = "app/add_purchase_submit";
$route["purchases-list"]      = "app/purchases_list";
$route["purchase/(:num)"]     = "app/get_purchase/$1";
$route["purchase-update"]     = "app/update_purchase_submit";

// PURCHASE RETURN
$route["purchase-return"]            = "app/purchase_return";
$route["add-purchase-return"]        = "app/add_purchase_return";
$route["add-purchase-return-submit"] = "app/add_purchase_return_submit";
$route["purchase-returns-list"]      = "app/purchase_returns_list";
$route["purchase-return/(:num)"]     = "app/get_purchase_return/$1";
$route["purchase-return-update"]     = "app/update_purchase_return_submit";

// SALES
$route["sales"]            = "app/sales";
$route["add-sale"]         = "app/add_sale";
$route["add-sale-submit"]  = "app/add_sale_submit";
$route["sales-list"]       = "app/sales_list";
$route["sale/(:num)"]      = "app/get_sale/$1";
$route["sale-update"]      = "app/update_sale_submit";

// SALES RETURN
$route["sales-return"]            = "app/sales_return";
$route["add-sales-return"]        = "app/add_sales_return";
$route["add-sales-return-submit"] = "app/add_sales_return_submit";
$route["sales-returns-list"]      = "app/sales_returns_list";
$route["sales-return/(:num)"]     = "app/get_sales_return/$1";
$route["sales-return-update"]     = "app/update_sales_return_submit";

$route["payment"]           = "app/payment";
$route["add-payment"]       = "app/add_payment";
$route["add-payment-submit"]= "app/add_payment_submit";
$route["payments-list"]     = "app/payments_list";
$route["payment/(:num)"]    = "app/get_payment/$1";
$route["payment-update"]    = "app/update_payment_submit";

