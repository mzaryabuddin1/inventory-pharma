<?php
defined('BASEPATH') or exit('No direct script access allowed');

class App extends CI_Controller
{
    public $file_config;

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Karachi');
        $this->load->model('App_model');
        $this->load->library('emailsender');

        $this->file_config['upload_path']   = 'uploads/';
        $this->file_config['allowed_types'] = 'jpg|jpeg|png|gif';
        $this->file_config['encrypt_name']  = TRUE;
        $this->file_config['max_size']      = 4096; // 4MB
        $this->load->library('upload', $this->file_config);
    }


    public function login_submit()
    {
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');
        if ($this->form_validation->run() == FALSE) {
            $errors = array('error' => validation_errors());
            print_r(json_encode($errors));
            exit;
        }

        $information = $this->security->xss_clean($this->input->post());
        $this->data['email'] = $information['email'];
        $this->data['password'] = md5($information['password']);


        $user = $this->App_model->login_submit($this->data);

        if (!$user) {
            $errors = array('error' => '<p>Incorrect Email Or Password.</p>');
            print_r(json_encode($errors));
            exit;
        }

        if (!$user['status']) {
            $errors = array('error' => '<p>Your account is blocked!.</p>');
            print_r(json_encode($errors));
            exit;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['profile_picture'] = $user['profile_picture'];

        $success = array('success' => 1);
        print_r(json_encode($success));
    }

    public function register()
    {
        $this->load->view('register');
    }

    private function get_expire_date($days = 30)
    {
        return date('Y-m-d H:i:s', strtotime("+$days days"));
    }

    private function check_login()
    {
        if (!isset($_SESSION['user_id'])) {
            redirect('welcome');
            exit;
        }
    }

    public function register_submit()
    {
        // Validation rules
        $this->form_validation->set_rules('first_name', 'First Name', 'required|trim');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim|is_unique[users.email]', array('is_unique' => 'Email already exists.'));
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|trim');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $errors = array('error' => validation_errors());
            print_r(json_encode($errors));
            exit;
        }

        // Clean input
        $information = $this->security->xss_clean($this->input->post());

        $data = array(
            'first_name'      => $information['first_name'],
            'last_name'       => $information['last_name'],
            'email'           => $information['email'],
            'password'        => md5($information['password']), // You may want to use password_hash instead of md5
            'phone'           => isset($information['phone']) ? $information['phone'] : null,
            'profile_picture' => 'https://img.freepik.com/free-vector/blue-circle-with-white-user_78370-4707.jpg?semt=ais_hybrid&w=740&q=80', // default empty (you can handle upload separately)
            'status'          => 1,  // active by default
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
            'expire_at'       => $this->get_expire_date(30)
        );

        $insert_id = $this->App_model->insert_user($data);

        if ($insert_id) {
            $_SESSION['user_id'] = $insert_id;
            $_SESSION['user_email'] = $data['email'];

            $success = array('success' => 1, 'message' => 'Registration successful!');
            print_r(json_encode($success));
        } else {
            $errors = array('error' => '<p>Something went wrong. Please try again.</p>');
            print_r(json_encode($errors));
        }
    }

    public function forgot_password()
    {
        $this->load->view('forgot_password');
    }

    public function forgot_password_submit()
    {
        // Validate input
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
        if ($this->form_validation->run() === FALSE) {
            $errors = array('error' => validation_errors());
            print_r(json_encode($errors));
            exit;
        }

        // Clean input
        $information = $this->security->xss_clean($this->input->post());
        $email = strtolower(trim($information['email']));

        // Find user
        $user = $this->App_model->get_user_by_email($email);
        if (!$user) {
            $errors = array('error' => '<p>Email does not exist.</p>');
            print_r(json_encode($errors));
            exit;
        }

        // Generate random 6-digit password (e.g., 034921)
        $plain_password = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $hashed_password = md5($plain_password); // keep consistent with your login_submit()

        // Prepare email
        $subject = "Reset Password";
        $message = "Hi " . ($user['first_name'] ?? 'there') . ",\n\n"
            . "Your new password is: {$plain_password}\n"
            . "Please log in and change it immediately from your profile/settings.\n\n"
            . "Regards,\nSupport Team";

        // Send email first; only update DB if email was sent
        $is_email_sent = $this->emailsender->sendEmail($email, $subject, nl2br($message));

        if (!$is_email_sent) {
            $errors = array('error' => '<p>Error while sending email!.</p>');
            print_r(json_encode($errors));
            exit;
        }

        // Update password in DB
        $update_ok = $this->App_model->update_user_by_id($user['id'], array(
            'password'   => $hashed_password,
            'updated_at' => date('Y-m-d H:i:s')
        ));

        if (!$update_ok) {
            $errors = array('error' => '<p>Could not update password. Please try again.</p>');
            print_r(json_encode($errors));
            exit;
        }

        $success = array('success' => 1, 'message' => 'A new password has been sent to your email.');
        print_r(json_encode($success));
    }

    public function dashboard()
    {
        $this->check_login();
        $this->load->view('dashboard');
    }

    public function logout()
    {
        // Destroy all session data
        session_unset();
        session_destroy();
        redirect('welcome');
        exit;
    }

    public function profile()
    {
        $this->check_login(); // makes sure user_id is in session
        $user    = $this->App_model->get_user_by_email($_SESSION['user_email']);
        if (!$user) {                                       // safety guard
            show_error('User not found', 404);
            return;
        }
        $data['user'] = $user;                              // pass to view
        $this->load->view('profile', $data);
    }

    public function profile_submit()
    {
        $this->check_login();

        // Base validation
        $this->form_validation->set_rules('first_name', 'First Name', 'required|trim');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim');
        $this->form_validation->set_rules('phone', 'Phone', 'required|trim');

        // Conditional password validation: only if user typed a new password
        if ($this->input->post('new_password') !== null && $this->input->post('new_password') !== '') {
            $this->form_validation->set_rules('new_password', 'New Password', 'trim|min_length[6]');
        }

        if ($this->form_validation->run() === FALSE) {
            $errors = array('error' => validation_errors());
            print_r(json_encode($errors));
            exit;
        }

        $user_id     = $_SESSION['user_id'];
        $information = $this->security->xss_clean($this->input->post());

        // Build update data
        $update = array(
            'first_name' => $information['first_name'],
            'last_name'  => $information['last_name'],
            'phone'      => isset($information['phone']) ? $information['phone'] : null,
            'updated_at' => date('Y-m-d H:i:s')
        );

        // Optional password update
        if (!empty($information['new_password'])) {
            // Keep consistent with your current login (MD5). If you move to password_hash, update login too.
            $update['password'] = md5($information['new_password']);
        }

        // Optional profile picture upload
        if (!empty($_FILES['profile_picture']['name'])) {
            if (!$this->upload->do_upload('profile_picture')) {
                $errors = array('error' => '<p>' . $this->upload->display_errors('', '') . '</p>');
                print_r(json_encode($errors));
                exit;
            } else {
                $uploadData = $this->upload->data();
                $update['profile_picture'] = base_url() . "uploads/"  . $uploadData['file_name'];
                $_SESSION['profile_picture'] = $update['profile_picture'];
            }
        }

        // Persist changes
        $ok = $this->App_model->update_user_by_id($user_id, $update);

        if (!$ok) {
            $errors = array('error' => '<p>Could not update profile. Please try again.</p>');
            print_r(json_encode($errors));
            exit;
        }

        $success = array('success' => 1, 'message' => 'Profile updated successfully.');
        print_r(json_encode($success));
    }

    public function product()
    {
        $this->check_login(); // makes sure user_id is in session
        $this->load->view('product');
    }

    public function add_product()
    {
        $this->check_login(); // makes sure user_id is in session
        $this->load->view('add_product');
    }

    public function add_product_submit()
    {
        $this->check_login();

        // Validation
        $this->form_validation->set_rules('product_name', 'Product Name', 'required|trim');
        $this->form_validation->set_rules('generic', 'Generic', 'trim');

        if ($this->form_validation->run() === FALSE) {
            $errors = array('error' => validation_errors());
            print_r(json_encode($errors));
            exit;
        }

        // Clean input
        $information = $this->security->xss_clean($this->input->post());
        $user_id     = $_SESSION['user_id'];

        // Decode prices JSON: [{"dated":"2025-09-02 14:00:00","mrp":100,"tp":30}]
        $prices = [];
        if (!empty($information['prices'])) {
            $decoded = json_decode($information['prices'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $prices = $decoded;
            } else {
                $errors = array('error' => '<p>Invalid prices payload.</p>');
                print_r(json_encode($errors));
                exit;
            }
        } else {
            $errors = array('error' => '<p>Please add at least one price row.</p>');
            print_r(json_encode($errors));
            exit;
        }

        // Optional product image upload (field name="image")
        $image_filename = null;
        if (!empty($_FILES['image']['name'])) {

            if (!$this->upload->do_upload('image')) {
                $errors = array('error' => '<p>' . $this->upload->display_errors('', '') . '</p>');
                print_r(json_encode($errors));
                exit;
            } else {
                $uploadData     = $this->upload->data();
                $image_filename = $uploadData['file_name'];
            }
        }

        // Prepare insert data
        $data = array(
            'product_name' => $information['product_name'],
            'generic'      => isset($information['generic']) ? $information['generic'] : null,
            'prices'       => json_encode($prices), // store as JSON
            'image'        => $image_filename ? base_url() . "uploads/" .  $image_filename : "https://static.vecteezy.com/system/resources/thumbnails/000/546/318/small/diamond_002.jpg",      // just filename; build URL when displaying
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
            'created_by'   => $user_id
        );

        // Save
        $insert_id = $this->App_model->insert_product($data);

        if (!$insert_id) {
            $errors = array('error' => '<p>Could not save product. Please try again.</p>');
            print_r(json_encode($errors));
            exit;
        }

        $success = array('success' => 1, 'message' => 'Product added successfully.', 'id' => $insert_id);
        print_r(json_encode($success));
    }

    public function products_list()
    {
        $this->check_login();

        $draw    = (int)$this->input->post('draw');
        $start   = (int)$this->input->post('start');
        $length  = (int)$this->input->post('length');
        $search  = $this->input->post('search')['value'] ?? '';

        $order     = $this->input->post('order')[0] ?? null;
        $columns   = $this->input->post('columns') ?? [];
        $order_by  = 'product_name';
        $order_dir = 'asc';

        if ($order) {
            $colIdx   = (int)$order['column'];
            $order_dir = strtolower($order['dir']) === 'desc' ? 'desc' : 'asc';
            // allow only known columns in the expanded shape
            $safeMap  = ['image', 'product_name', 'generic', 'dated', 'mrp', 'tp', 'created_at', 'id'];
            $colKey   = $columns[$colIdx]['data'] ?? 'product_name';
            $order_by = in_array($colKey, $safeMap, true) ? $colKey : 'product_name';
        }

        $result = $this->App_model->datatable_products($start, $length, $search, $order_by, $order_dir);

        echo json_encode([
            'draw'            => $draw,
            'recordsTotal'    => $result['total'],
            'recordsFiltered' => $result['filtered'],
            'data'            => $result['rows'],
        ]);
    }

    public function get_product($id)
    {
        $this->check_login();
        $id = (int)$id;
        if (!$id) {
            echo json_encode(['error' => '<p>Invalid id</p>']);
            return;
        }

        $row = $this->App_model->get_product($id);
        if (!$row) {
            echo json_encode(['error' => '<p>Product not found</p>']);
            return;
        }

        $row['image_url'] = (!empty($row['image']) && strpos($row['image'], 'http') !== 0)
            ? base_url() . 'uploads/' . $row['image']
            : ($row['image'] ?? null);

        $row['prices'] = $row['prices'] ? json_decode($row['prices'], true) : [];
        echo json_encode(['success' => 1, 'data' => $row]);
    }

    public function update_product_submit()
    {
        $this->check_login();

        $this->form_validation->set_rules('product_id', 'Product', 'required|integer');
        $this->form_validation->set_rules('product_name', 'Product Name', 'required|trim');
        $this->form_validation->set_rules('generic', 'Generic', 'trim');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['error' => validation_errors()]);
            return;
        }

        $p  = $this->security->xss_clean($this->input->post());
        $id = (int)$p['product_id'];

        $row = $this->App_model->get_product($id);
        if (!$row) {
            echo json_encode(['error' => '<p>Product not found</p>']);
            return;
        }

        // prices
        $prices = [];
        if (!empty($p['prices'])) {
            $decoded = json_decode($p['prices'], true);
            if (!is_array($decoded)) {
                echo json_encode(['error' => '<p>Invalid prices payload</p>']);
                return;
            }
            $prices = $decoded;
        }

        // image (optional)
        $image_filename = $row['image'];
        if (!empty($_FILES['image']['name'])) {

            if (!$this->upload->do_upload('image')) {
                echo json_encode(['error' => '<p>' . $this->upload->display_errors('', '') . '</p>']);
                return;
            }
            $image_filename = base_url() . "uploads/" . $this->upload->data('file_name');
        }

        $data = [
            'product_name' => $p['product_name'],
            'generic'      => $p['generic'] ?? null,
            'prices'       => json_encode($prices),
            'image'        => $image_filename,
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        $ok = $this->App_model->product_update_by_id($id, $data);
        if (!$ok) {
            echo json_encode(['error' => '<p>Could not update product</p>']);
            return;
        }

        echo json_encode(['success' => 1, 'message' => 'Product updated']);
    }

    public function supplier()
    {
        $this->check_login();
        $this->load->view('supplier'); // view below
    }

    public function add_supplier()
    {
        $this->check_login();
        $this->load->view('add_supplier'); // optional separate add page; you can skip if not needed
    }


    public function add_supplier_submit()
    {
        $this->check_login();

        $this->form_validation->set_rules('name',  'Name',  'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'trim|valid_email');
        $this->form_validation->set_rules('phone', 'Phone', 'trim');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['error' => validation_errors()]);
            return;
        }

        $p = $this->security->xss_clean($this->input->post());
        $user_id = $_SESSION['user_id'];

        // optional logo upload ("logo" input)
        $logo_url = null;
        if (!empty($_FILES['logo']['name'])) {
            // reuse constructor config
            $this->upload->initialize($this->file_config);
            if (!$this->upload->do_upload('logo')) {
                echo json_encode(['error' => '<p>' . $this->upload->display_errors('', '') . '</p>']);
                return;
            }
            $logo_url = base_url() . 'uploads/' . $this->upload->data('file_name'); // full URL (consistent with your product)
        }

        $data = [
            'name'       => $p['name'],
            'email'      => $p['email'] ?? null,
            'phone'      => $p['phone'] ?? null,
            'address'    => $p['address'] ?? null,
            'logo'       => $logo_url ?: 'https://static.vecteezy.com/system/resources/thumbnails/000/546/318/small/diamond_002.jpg',
            'status'     => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_by' => $user_id
        ];

        $insert_id = $this->App_model->insert_supplier($data);
        if (!$insert_id) {
            echo json_encode(['error' => '<p>Could not save supplier. Please try again.</p>']);
            return;
        }

        echo json_encode(['success' => 1, 'message' => 'Supplier added successfully.', 'id' => $insert_id]);
    }


    public function suppliers_list()
    {
        $this->check_login();

        $draw    = (int)$this->input->post('draw');
        $start   = (int)$this->input->post('start');
        $length  = (int)$this->input->post('length');
        $search  = $this->input->post('search')['value'] ?? '';

        $order     = $this->input->post('order')[0] ?? null;
        $columns   = $this->input->post('columns') ?? [];
        $order_by  = 'name';
        $order_dir = 'asc';

        if ($order) {
            $colIdx    = (int)$order['column'];
            $order_dir = strtolower($order['dir']) === 'desc' ? 'desc' : 'asc';
            $safeMap   = ['logo', 'name', 'email', 'phone', 'address', 'status', 'created_at', 'id'];
            $colKey    = $columns[$colIdx]['data'] ?? 'name';
            $order_by  = in_array($colKey, $safeMap, true) ? $colKey : 'name';
        }

        $result = $this->App_model->datatable_suppliers($start, $length, $search, $order_by, $order_dir);

        echo json_encode([
            'draw'            => $draw,
            'recordsTotal'    => $result['total'],
            'recordsFiltered' => $result['filtered'],
            'data'            => $result['rows'],
        ]);
    }


    public function get_supplier($id)
    {
        $this->check_login();
        $id = (int)$id;
        if (!$id) {
            echo json_encode(['error' => '<p>Invalid id</p>']);
            return;
        }

        $row = $this->App_model->get_supplier($id);
        if (!$row) {
            echo json_encode(['error' => '<p>Supplier not found</p>']);
            return;
        }

        echo json_encode(['success' => 1, 'data' => $row]); // logo is already a full URL
    }


    public function update_supplier_submit()
    {
        $this->check_login();

        $this->form_validation->set_rules('supplier_id', 'Supplier', 'required|integer');
        $this->form_validation->set_rules('name',        'Name',     'required|trim');
        $this->form_validation->set_rules('email',       'Email',    'trim|valid_email');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['error' => validation_errors()]);
            return;
        }

        $p  = $this->security->xss_clean($this->input->post());
        $id = (int)$p['supplier_id'];

        $row = $this->App_model->get_supplier($id);
        if (!$row) {
            echo json_encode(['error' => '<p>Supplier not found</p>']);
            return;
        }

        // optional new logo
        $logo_url = $row['logo'];
        if (!empty($_FILES['logo']['name'])) {
            $this->upload->initialize($this->file_config);
            if (!$this->upload->do_upload('logo')) {
                echo json_encode(['error' => '<p>' . $this->upload->display_errors('', '') . '</p>']);
                return;
            }
            $logo_url = base_url() . 'uploads/' . $this->upload->data('file_name');
        }

        $data = [
            'name'       => $p['name'],
            'email'      => $p['email'] ?? null,
            'phone'      => $p['phone'] ?? null,
            'address'    => $p['address'] ?? null,
            'logo'       => $logo_url,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $ok = $this->App_model->update_supplier_by_id($id, $data);
        if (!$ok) {
            echo json_encode(['error' => '<p>Could not update supplier</p>']);
            return;
        }

        echo json_encode(['success' => 1, 'message' => 'Supplier updated']);
    }


    public function customer()
    {
        $this->check_login();
        $this->load->view('customer'); // view below
    }

    public function add_customer()
    {
        $this->check_login();
        $this->load->view('add_customer'); // optional separate page (you can skip)
    }


    public function add_customer_submit()
    {
        $this->check_login();

        $this->form_validation->set_rules('name',  'Name',  'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'trim|valid_email');
        $this->form_validation->set_rules('phone', 'Phone', 'trim');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['error' => validation_errors()]);
            return;
        }

        $p = $this->security->xss_clean($this->input->post());
        $user_id = $_SESSION['user_id'];

        // optional avatar upload ("avatar" input)
        $avatar_url = null;
        if (!empty($_FILES['avatar']['name'])) {
            $this->upload->initialize($this->file_config);
            if (!$this->upload->do_upload('avatar')) {
                echo json_encode(['error' => '<p>' . $this->upload->display_errors('', '') . '</p>']);
                return;
            }
            $avatar_url = base_url() . 'uploads/' . $this->upload->data('file_name'); // full URL (consistent)
        }

        $data = [
            'name'       => $p['name'],
            'email'      => $p['email'] ?? null,
            'phone'      => $p['phone'] ?? null,
            'address'    => $p['address'] ?? null,
            'avatar'     => $avatar_url ?: 'https://static.vecteezy.com/system/resources/thumbnails/000/546/318/small/diamond_002.jpg',
            'status'     => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_by' => $user_id
        ];

        $insert_id = $this->App_model->insert_customer($data);
        if (!$insert_id) {
            echo json_encode(['error' => '<p>Could not save customer. Please try again.</p>']);
            return;
        }

        echo json_encode(['success' => 1, 'message' => 'Customer added successfully.', 'id' => $insert_id]);
    }


    public function customers_list()
    {
        $this->check_login();

        $draw    = (int)$this->input->post('draw');
        $start   = (int)$this->input->post('start');
        $length  = (int)$this->input->post('length');
        $search  = $this->input->post('search')['value'] ?? '';

        $order     = $this->input->post('order')[0] ?? null;
        $columns   = $this->input->post('columns') ?? [];
        $order_by  = 'name';
        $order_dir = 'asc';

        if ($order) {
            $colIdx    = (int)$order['column'];
            $order_dir = strtolower($order['dir']) === 'desc' ? 'desc' : 'asc';
            $safeMap   = ['avatar', 'name', 'email', 'phone', 'address', 'status', 'created_at', 'id'];
            $colKey    = $columns[$colIdx]['data'] ?? 'name';
            $order_by  = in_array($colKey, $safeMap, true) ? $colKey : 'name';
        }

        $result = $this->App_model->datatable_customers($start, $length, $search, $order_by, $order_dir);

        echo json_encode([
            'draw'            => $draw,
            'recordsTotal'    => $result['total'],
            'recordsFiltered' => $result['filtered'],
            'data'            => $result['rows'],
        ]);
    }


    public function get_customer($id)
    {
        $this->check_login();
        $id = (int)$id;
        if (!$id) {
            echo json_encode(['error' => '<p>Invalid id</p>']);
            return;
        }

        $row = $this->App_model->get_customer($id);
        if (!$row) {
            echo json_encode(['error' => '<p>Customer not found</p>']);
            return;
        }

        echo json_encode(['success' => 1, 'data' => $row]); // avatar already full URL
    }


    public function update_customer_submit()
    {
        $this->check_login();

        $this->form_validation->set_rules('customer_id', 'Customer', 'required|integer');
        $this->form_validation->set_rules('name',        'Name',     'required|trim');
        $this->form_validation->set_rules('email',       'Email',    'trim|valid_email');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['error' => validation_errors()]);
            return;
        }

        $p  = $this->security->xss_clean($this->input->post());
        $id = (int)$p['customer_id'];

        $row = $this->App_model->get_customer($id);
        if (!$row) {
            echo json_encode(['error' => '<p>Customer not found</p>']);
            return;
        }

        // optional new avatar
        $avatar_url = $row['avatar'];
        if (!empty($_FILES['avatar']['name'])) {
            $this->upload->initialize($this->file_config);
            if (!$this->upload->do_upload('avatar')) {
                echo json_encode(['error' => '<p>' . $this->upload->display_errors('', '') . '</p>']);
                return;
            }
            $avatar_url = base_url() . 'uploads/' . $this->upload->data('file_name');
        }

        $data = [
            'name'       => $p['name'],
            'email'      => $p['email'] ?? null,
            'phone'      => $p['phone'] ?? null,
            'address'    => $p['address'] ?? null,
            'avatar'     => $avatar_url,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $ok = $this->App_model->update_customer_by_id($id, $data);
        if (!$ok) {
            echo json_encode(['error' => '<p>Could not update customer</p>']);
            return;
        }

        echo json_encode(['success' => 1, 'message' => 'Customer updated']);
    }

    public function purchase()
    {
        $this->check_login();

        $products = $this->App_model->get_products();
        $suppliers = $this->App_model->get_suppliers();

        $data = [
            'products' => $products,
            'suppliers' => $suppliers
        ];

        $this->load->view('purchase', $data);
    }
    public function add_purchase()
    {
        $this->check_login();
        $this->load->view('add_purchase');
    }

    public function add_purchase_submit()
    {
        $this->check_login();

        $this->form_validation->set_rules('ref_no', 'Reference No', 'required|trim');
        $this->form_validation->set_rules('supplier_id', 'Supplier', 'required|integer');
        $this->form_validation->set_rules('purchase_date', 'Date', 'required|trim');
        $this->form_validation->set_rules('items', 'Items', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['error' => validation_errors()]);
            return;
        }

        $p = $this->security->xss_clean($this->input->post());
        $items = json_decode($p['items'], true);
        if (!is_array($items) || !count($items)) {
            echo json_encode(['error' => '<p>Invalid items.</p>']);
            return;
        }

        // compute total
        $total = 0;
        foreach ($items as $it) {
            $qty   = (int)($it['qty'] ?? 0);
            $price = (float)($it['price'] ?? 0);
            $total += $qty * $price;
        }

        $data = [
            'ref_no'        => $p['ref_no'],
            'supplier_id'   => (int)$p['supplier_id'],
            'purchase_date' => $p['purchase_date'],
            'items'         => json_encode($items),
            'total_amount'  => $total,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
            'created_by'    => $_SESSION['user_id']
        ];

        $ok = $this->App_model->purchase_create($data, $items); // handles stock+ledger in TX
        if (!$ok) {
            echo json_encode(['error' => '<p>Could not save purchase.</p>']);
            return;
        }

        echo json_encode(['success' => 1, 'message' => 'Purchase saved']);
    }

    public function purchases_list()
    {
        $this->check_login();
        $draw   = (int)$this->input->post('draw');
        $start  = (int)$this->input->post('start');
        $length = (int)$this->input->post('length');
        $search = $this->input->post('search')['value'] ?? '';

        $order     = $this->input->post('order')[0] ?? null;
        $columns   = $this->input->post('columns') ?? [];
        $order_by  = 'purchase_date';
        $order_dir = 'desc';
        if ($order) {
            $colIdx = (int)$order['column'];
            $order_dir = strtolower($order['dir']) === 'asc' ? 'asc' : 'desc';
            $safe = ['ref_no', 'purchase_date', 'total_amount'];
            $colKey = $columns[$colIdx]['data'] ?? 'purchase_date';
            $order_by = in_array($colKey, $safe, true) ? $colKey : 'purchase_date';
        }
        $result = $this->App_model->datatable_purchases($start, $length, $search, $order_by, $order_dir);

        echo json_encode([
            'draw' => $draw,
            'recordsTotal' => $result['total'],
            'recordsFiltered' => $result['filtered'],
            'data' => $result['rows']
        ]);
    }

    public function get_purchase($id)
    {
        $this->check_login();
        $row = $this->App_model->get_purchase((int)$id);
        if (!$row) {
            echo json_encode(['error' => '<p>Not found.</p>']);
            return;
        }
        $row['items'] = json_decode($row['items'], true);
        echo json_encode(['success' => 1, 'data' => $row]);
    }

    public function update_purchase_submit()
    {
        $this->check_login();

        $this->form_validation->set_rules('purchase_id', 'Purchase', 'required|integer');
        $this->form_validation->set_rules('ref_no', 'Reference No', 'required|trim');
        $this->form_validation->set_rules('supplier_id', 'Supplier', 'required|integer');
        $this->form_validation->set_rules('purchase_date', 'Date', 'required|trim');
        $this->form_validation->set_rules('items', 'Items', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['error' => validation_errors()]);
            return;
        }

        $p = $this->security->xss_clean($this->input->post());
        $id = (int)$p['purchase_id'];
        $old = $this->App_model->get_purchase($id);
        if (!$old) {
            echo json_encode(['error' => '<p>Not found.</p>']);
            return;
        }

        $items = json_decode($p['items'], true);
        if (!is_array($items) || !count($items)) {
            echo json_encode(['error' => '<p>Invalid items.</p>']);
            return;
        }

        $total = 0;
        foreach ($items as $it) {
            $total += ((int)$it['qty']) * ((float)$it['price']);
        }

        $data = [
            'ref_no'        => $p['ref_no'],
            'supplier_id'   => (int)$p['supplier_id'],
            'purchase_date' => $p['purchase_date'],
            'items'         => json_encode($items),
            'total_amount'  => $total,
            'updated_at'    => date('Y-m-d H:i:s')
        ];

        $ok = $this->App_model->purchase_update($id, $old, $data, $items); // reverses old stock/ledger, reapplies new
        if (!$ok) {
            echo json_encode(['error' => '<p>Could not update purchase.</p>']);
            return;
        }

        echo json_encode(['success' => 1, 'message' => 'Purchase updated']);
    }

    public function purchase_return()
    {
        $this->check_login();

        $products = $this->App_model->get_products();
        $suppliers = $this->App_model->get_suppliers();
        $purchases = $this->App_model->get_purchases();

        $data = [
            'products' => $products,
            'suppliers' => $suppliers,
            'purchases' => $purchases
        ];

        $this->load->view('purchase_return', $data);
    }
    public function add_purchase_return()
    {
        $this->check_login();
        $this->load->view('add_purchase_return');
    }

    public function add_purchase_return_submit()
    {
        $this->check_login();

        $this->form_validation->set_rules('ref_no', 'Reference No', 'required|trim');
        $this->form_validation->set_rules('purchase_id', 'Purchase', 'required|integer');
        $this->form_validation->set_rules('supplier_id', 'Supplier', 'required|integer');
        $this->form_validation->set_rules('return_date', 'Date', 'required|trim');
        $this->form_validation->set_rules('items', 'Items', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['error' => validation_errors()]);
            return;
        }

        $p = $this->security->xss_clean($this->input->post());
        $items = json_decode($p['items'], true);
        if (!is_array($items) || !count($items)) {
            echo json_encode(['error' => '<p>Invalid items.</p>']);
            return;
        }

        $amount = 0;
        foreach ($items as $it) {
            $amount += ((int)$it['qty']) * ((float)$it['price']);
        }

        $data = [
            'ref_no'        => $p['ref_no'],
            'purchase_id'   => (int)$p['purchase_id'],
            'supplier_id'   => (int)$p['supplier_id'],
            'return_date'   => $p['return_date'],
            'items'         => json_encode($items),
            'return_amount' => $amount,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
            'created_by'    => $_SESSION['user_id']
        ];

        $ok = $this->App_model->purchase_return_create($data, $items);
        if (!$ok) {
            echo json_encode(['error' => '<p>Could not save purchase return.</p>']);
            return;
        }

        echo json_encode(['success' => 1, 'message' => 'Purchase return saved']);
    }

    public function purchase_returns_list()
    {
        $this->check_login();
        $draw = (int)$this->input->post('draw');
        $start = (int)$this->input->post('start');
        $length = (int)$this->input->post('length');
        $search = $this->input->post('search')['value'] ?? '';

        $order = $this->input->post('order')[0] ?? null;
        $columns = $this->input->post('columns') ?? [];
        $order_by = 'return_date';
        $order_dir = 'desc';
        if ($order) {
            $colIdx = (int)$order['column'];
            $order_dir = strtolower($order['dir']) === 'asc' ? 'asc' : 'desc';
            $safe = ['ref_no', 'return_date', 'return_amount'];
            $colKey = $columns[$colIdx]['data'] ?? 'return_date';
            $order_by = in_array($colKey, $safe, true) ? $colKey : 'return_date';
        }

        $result = $this->App_model->datatable_purchase_returns($start, $length, $search, $order_by, $order_dir);
        echo json_encode(['draw' => $draw, 'recordsTotal' => $result['total'], 'recordsFiltered' => $result['filtered'], 'data' => $result['rows']]);
    }

    public function get_purchase_return($id)
    {
        $this->check_login();
        $row = $this->App_model->get_purchase_return((int)$id);
        if (!$row) {
            echo json_encode(['error' => '<p>Not found.</p>']);
            return;
        }
        $row['items'] = json_decode($row['items'], true);
        echo json_encode(['success' => 1, 'data' => $row]);
    }

    public function update_purchase_return_submit()
    {
        $this->check_login();

        $this->form_validation->set_rules('purchase_return_id', 'Purchase Return', 'required|integer');
        $this->form_validation->set_rules('ref_no', 'Reference No', 'required|trim');
        $this->form_validation->set_rules('purchase_id', 'Purchase', 'required|integer');
        $this->form_validation->set_rules('supplier_id', 'Supplier', 'required|integer');
        $this->form_validation->set_rules('return_date', 'Date', 'required|trim');
        $this->form_validation->set_rules('items', 'Items', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['error' => validation_errors()]);
            return;
        }

        $p = $this->security->xss_clean($this->input->post());
        $id = (int)$p['purchase_return_id'];
        $old = $this->App_model->get_purchase_return($id);
        if (!$old) {
            echo json_encode(['error' => '<p>Not found.</p>']);
            return;
        }

        $items = json_decode($p['items'], true);
        if (!is_array($items) || !count($items)) {
            echo json_encode(['error' => '<p>Invalid items.</p>']);
            return;
        }

        $amount = 0;
        foreach ($items as $it) {
            $amount += ((int)$it['qty']) * ((float)$it['price']);
        }

        $data = [
            'ref_no' => $p['ref_no'],
            'purchase_id' => (int)$p['purchase_id'],
            'supplier_id' => (int)$p['supplier_id'],
            'return_date' => $p['return_date'],
            'items' => json_encode($items),
            'return_amount' => $amount,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $ok = $this->App_model->purchase_return_update($id, $old, $data, $items);
        if (!$ok) {
            echo json_encode(['error' => '<p>Could not update purchase return.</p>']);
            return;
        }

        echo json_encode(['success' => 1, 'message' => 'Purchase return updated']);
    }

    public function sales()
    {
        $this->check_login();

        $products = $this->App_model->get_products();
        $customers = $this->App_model->get_customers();

        $data = [
            'products' => $products,
            'customers' => $customers
        ];

        $this->load->view('sales', $data);
    }
    public function add_sale()
    {
        $this->check_login();
        $this->load->view('add_sale');
    }

    public function add_sale_submit()
    {
        $this->check_login();

        $this->form_validation->set_rules('invoice_no', 'Invoice No', 'required|trim');
        $this->form_validation->set_rules('customer_id', 'Customer', 'required|integer');
        $this->form_validation->set_rules('sale_date', 'Date', 'required|trim');
        $this->form_validation->set_rules('items', 'Items', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['error' => validation_errors()]);
            return;
        }

        $p = $this->security->xss_clean($this->input->post());
        $items = json_decode($p['items'], true);
        if (!is_array($items) || !count($items)) {
            echo json_encode(['error' => '<p>Invalid items.</p>']);
            return;
        }

        $total = 0;
        foreach ($items as $it) {
            $total += ((int)$it['qty']) * ((float)$it['price']);
        }

        $data = [
            'invoice_no' => $p['invoice_no'],
            'customer_id' => (int)$p['customer_id'],
            'sale_date' => $p['sale_date'],
            'items' => json_encode($items),
            'total_amount' => $total,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_by' => $_SESSION['user_id']
        ];

        $ok = $this->App_model->sale_create($data, $items); // handles stock- & ledger+
        if (!$ok) {
            echo json_encode(['error' => '<p>Could not save sale.</p>']);
            return;
        }

        echo json_encode(['success' => 1, 'message' => 'Sale saved']);
    }

    public function sales_list()
    {
        $this->check_login();
        $draw = (int)$this->input->post('draw');
        $start = (int)$this->input->post('start');
        $length = (int)$this->input->post('length');
        $search = $this->input->post('search')['value'] ?? '';

        $order = $this->input->post('order')[0] ?? null;
        $columns = $this->input->post('columns') ?? [];
        $order_by = 'sale_date';
        $order_dir = 'desc';
        if ($order) {
            $colIdx = (int)$order['column'];
            $order_dir = strtolower($order['dir']) === 'asc' ? 'asc' : 'desc';
            $safe = ['invoice_no', 'sale_date', 'total_amount'];
            $colKey = $columns[$colIdx]['data'] ?? 'sale_date';
            $order_by = in_array($colKey, $safe, true) ? $colKey : 'sale_date';
        }
        $result = $this->App_model->datatable_sales($start, $length, $search, $order_by, $order_dir);
        echo json_encode(['draw' => $draw, 'recordsTotal' => $result['total'], 'recordsFiltered' => $result['filtered'], 'data' => $result['rows']]);
    }

    public function get_sale($id)
    {
        $this->check_login();
        $row = $this->App_model->get_sale((int)$id);
        if (!$row) {
            echo json_encode(['error' => '<p>Not found.</p>']);
            return;
        }
        $row['items'] = json_decode($row['items'], true);
        echo json_encode(['success' => 1, 'data' => $row]);
    }

    public function update_sale_submit()
    {
        $this->check_login();

        $this->form_validation->set_rules('sale_id', 'Sale', 'required|integer');
        $this->form_validation->set_rules('invoice_no', 'Invoice No', 'required|trim');
        $this->form_validation->set_rules('customer_id', 'Customer', 'required|integer');
        $this->form_validation->set_rules('sale_date', 'Date', 'required|trim');
        $this->form_validation->set_rules('items', 'Items', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['error' => validation_errors()]);
            return;
        }

        $p = $this->security->xss_clean($this->input->post());
        $id = (int)$p['sale_id'];
        $old = $this->App_model->get_sale($id);
        if (!$old) {
            echo json_encode(['error' => '<p>Not found.</p>']);
            return;
        }

        $items = json_decode($p['items'], true);
        if (!is_array($items) || !count($items)) {
            echo json_encode(['error' => '<p>Invalid items.</p>']);
            return;
        }

        $total = 0;
        foreach ($items as $it) {
            $total += ((int)$it['qty']) * ((float)$it['price']);
        }

        $data = [
            'invoice_no' => $p['invoice_no'],
            'customer_id' => (int)$p['customer_id'],
            'sale_date' => $p['sale_date'],
            'items' => json_encode($items),
            'total_amount' => $total,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $ok = $this->App_model->sale_update($id, $old, $data, $items);
        if (!$ok) {
            echo json_encode(['error' => '<p>Could not update sale.</p>']);
            return;
        }

        echo json_encode(['success' => 1, 'message' => 'Sale updated']);
    }

    public function sales_return()
    {
        $this->check_login();

        $products = $this->App_model->get_products();
        $customers = $this->App_model->get_customers();
        $invoices = $this->App_model->get_invoices();

        $data = [
            'products' => $products,
            'customers' => $customers,
            'invoices' => $invoices
        ];

        $this->load->view('sales_return', $data);
    }
    public function add_sales_return()
    {
        $this->check_login();
        $this->load->view('add_sales_return');
    }

    public function add_sales_return_submit()
    {
        $this->check_login();

        $this->form_validation->set_rules('ref_no', 'Reference No', 'required|trim');
        $this->form_validation->set_rules('sale_id', 'Sale', 'required|integer');
        $this->form_validation->set_rules('customer_id', 'Customer', 'required|integer');
        $this->form_validation->set_rules('return_date', 'Date', 'required|trim');
        $this->form_validation->set_rules('items', 'Items', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['error' => validation_errors()]);
            return;
        }

        $p = $this->security->xss_clean($this->input->post());
        $items = json_decode($p['items'], true);
        if (!is_array($items) || !count($items)) {
            echo json_encode(['error' => '<p>Invalid items.</p>']);
            return;
        }

        $amount = 0;
        foreach ($items as $it) {
            $amount += ((int)$it['qty']) * ((float)$it['price']);
        }

        $data = [
            'ref_no' => $p['ref_no'],
            'sale_id' => (int)$p['sale_id'],
            'customer_id' => (int)$p['customer_id'],
            'return_date' => $p['return_date'],
            'items' => json_encode($items),
            'return_amount' => $amount,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_by' => $_SESSION['user_id']
        ];

        $ok = $this->App_model->sales_return_create($data, $items);
        if (!$ok) {
            echo json_encode(['error' => '<p>Could not save sales return.</p>']);
            return;
        }

        echo json_encode(['success' => 1, 'message' => 'Sales return saved']);
    }

    public function sales_returns_list()
    {
        $this->check_login();
        $draw = (int)$this->input->post('draw');
        $start = (int)$this->input->post('start');
        $length = (int)$this->input->post('length');
        $search = $this->input->post('search')['value'] ?? '';

        $order = $this->input->post('order')[0] ?? null;
        $columns = $this->input->post('columns') ?? [];
        $order_by = 'return_date';
        $order_dir = 'desc';
        if ($order) {
            $colIdx = (int)$order['column'];
            $order_dir = strtolower($order['dir']) === 'asc' ? 'asc' : 'desc';
            $safe = ['ref_no', 'return_date', 'return_amount'];
            $colKey = $columns[$colIdx]['data'] ?? 'return_date';
            $order_by = in_array($colKey, $safe, true) ? $colKey : 'return_date';
        }

        $result = $this->App_model->datatable_sales_returns($start, $length, $search, $order_by, $order_dir);
        echo json_encode(['draw' => $draw, 'recordsTotal' => $result['total'], 'recordsFiltered' => $result['filtered'], 'data' => $result['rows']]);
    }

    public function get_sales_return($id)
    {
        $this->check_login();
        $row = $this->App_model->get_sales_return((int)$id);
        if (!$row) {
            echo json_encode(['error' => '<p>Not found.</p>']);
            return;
        }
        $row['items'] = json_decode($row['items'], true);
        echo json_encode(['success' => 1, 'data' => $row]);
    }

    public function update_sales_return_submit()
    {
        $this->check_login();

        $this->form_validation->set_rules('sales_return_id', 'Sales Return', 'required|integer');
        $this->form_validation->set_rules('ref_no', 'Reference No', 'required|trim');
        $this->form_validation->set_rules('sale_id', 'Sale', 'required|integer');
        $this->form_validation->set_rules('customer_id', 'Customer', 'required|integer');
        $this->form_validation->set_rules('return_date', 'Date', 'required|trim');
        $this->form_validation->set_rules('items', 'Items', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['error' => validation_errors()]);
            return;
        }

        $p = $this->security->xss_clean($this->input->post());
        $id = (int)$p['sales_return_id'];
        $old = $this->App_model->get_sales_return($id);
        if (!$old) {
            echo json_encode(['error' => '<p>Not found.</p>']);
            return;
        }

        $items = json_decode($p['items'], true);
        if (!is_array($items) || !count($items)) {
            echo json_encode(['error' => '<p>Invalid items.</p>']);
            return;
        }

        $amount = 0;
        foreach ($items as $it) {
            $amount += ((int)$it['qty']) * ((float)$it['price']);
        }

        $data = [
            'ref_no' => $p['ref_no'],
            'sale_id' => (int)$p['sale_id'],
            'customer_id' => (int)$p['customer_id'],
            'return_date' => $p['return_date'],
            'items' => json_encode($items),
            'return_amount' => $amount,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $ok = $this->App_model->sales_return_update($id, $old, $data, $items);
        if (!$ok) {
            echo json_encode(['error' => '<p>Could not update sales return.</p>']);
            return;
        }

        echo json_encode(['success' => 1, 'message' => 'Sales return updated']);
    }

    // --- Payments pages ---
    public function payment()
    {
        $this->check_login();
        $this->load->view('payments');
    }
    public function add_payment()
    {
        $this->check_login();
        $this->load->view('payments'); // same page (add panel on top)
    }

    // Add payment submit
    public function add_payment_submit()
    {
        $this->check_login();

        // rules
        $this->form_validation->set_rules('ref_no', 'Reference No', 'required|trim');
        $this->form_validation->set_rules('payment_date', 'Date', 'required|trim');
        $this->form_validation->set_rules('type', 'Type', 'required|in_list[customer,supplier]');
        $this->form_validation->set_rules('party_id', 'Party', 'required|integer');
        $this->form_validation->set_rules('mode', 'Mode', 'required|in_list[cash,cheque]');
        $this->form_validation->set_rules('amount', 'Amount', 'required|numeric|greater_than[0]');
        if ($this->input->post('mode') === 'cheque') {
            $this->form_validation->set_rules('cheque_no', 'Cheque No', 'required|trim');
            $this->form_validation->set_rules('cheque_date', 'Cheque Date', 'required|trim');
        }

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['error' => validation_errors()]);
            return;
        }

        $p = $this->security->xss_clean($this->input->post());
        $user_id = $_SESSION['user_id'];

        $data = [
            'ref_no'       => $p['ref_no'],
            'payment_date' => $p['payment_date'], // 'Y-m-d H:i:s' from <input type="datetime-local">
            'type'         => $p['type'],         // customer|supplier
            'party_id'     => (int)$p['party_id'],
            'mode'         => $p['mode'],         // cash|cheque
            'cheque_no'    => $p['mode'] === 'cheque' ? ($p['cheque_no'] ?? null) : null,
            'cheque_date'  => $p['mode'] === 'cheque' ? ($p['cheque_date'] ?? null) : null,
            'amount'       => (float)$p['amount'],
            'note'         => $p['note'] ?? null,
            'created_by'   => $user_id,
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        $payment_id = $this->App_model->insert_payment($data);
        if (!$payment_id) {
            echo json_encode(['error' => '<p>Could not save payment.</p>']);
            return;
        }

        // Ledger: receive from customer = CREDIT (we got money), pay to supplier = DEBIT (we paid out)
        $credit = 0;
        $debit = 0;
        $desc = strtoupper($data['mode']) . ' Payment ' . $data['ref_no'];
        if ($data['type'] === 'customer') {
            $credit = $data['amount']; // incoming
        } else {
            $debit  = $data['amount']; // outgoing
        }

        $this->App_model->insert_ledger([
            'entry_date' => $data['payment_date'],
            'ref_type'   => 'payment',
            'ref_id'     => $payment_id,
            'party_type' => $data['type'],
            'party_id'   => $data['party_id'],
            'description' => $desc,
            'debit'      => $debit,
            'credit'     => $credit,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        echo json_encode(['success' => 1, 'message' => 'Payment saved.', 'id' => $payment_id]);
    }

    // Server-side list
    public function payments_list()
    {
        $this->check_login();

        $draw    = (int)$this->input->post('draw');
        $start   = (int)$this->input->post('start');
        $length  = (int)$this->input->post('length');
        $search  = $this->input->post('search')['value'] ?? '';

        $order   = $this->input->post('order')[0] ?? null;
        $columns = $this->input->post('columns') ?? [];
        $order_by  = 'payment_date';
        $order_dir = 'desc';
        if ($order) {
            $idx = (int)$order['column'];
            $order_dir = strtolower($order['dir']) === 'asc' ? 'asc' : 'desc';
            $safeMap = ['ref_no', 'payment_date', 'type', 'mode', 'amount', 'id'];
            $colKey  = $columns[$idx]['data'] ?? 'payment_date';
            $order_by = in_array($colKey, $safeMap, true) ? $colKey : 'payment_date';
        }

        $result = $this->App_model->datatable_payments($start, $length, $search, $order_by, $order_dir);

        // add party_label for each row
        foreach ($result['rows'] as &$r) {
            if ($r['type'] === 'supplier') {
                $r['party_label'] = $this->App_model->get_supplier_name((int)$r['party_id']) ?? 'Supplier #' . $r['party_id'];
            } else {
                $r['party_label'] = $this->App_model->get_customer_name((int)$r['party_id']) ?? 'Customer #' . $r['party_id'];
            }
        }

        echo json_encode([
            'draw'            => $draw,
            'recordsTotal'    => $result['total'],
            'recordsFiltered' => $result['filtered'],
            'data'            => $result['rows'],
        ]);
    }

    // Single record
    public function get_payment($id)
    {
        $this->check_login();
        $id = (int)$id;
        if (!$id) {
            echo json_encode(['error' => '<p>Invalid id</p>']);
            return;
        }
        $row = $this->App_model->get_payment($id);
        if (!$row) {
            echo json_encode(['error' => '<p>Payment not found</p>']);
            return;
        }

        $row['party_label'] = $row['type'] === 'supplier'
            ? ($this->App_model->get_supplier_name((int)$row['party_id']) ?? null)
            : ($this->App_model->get_customer_name((int)$row['party_id']) ?? null);

        echo json_encode(['success' => 1, 'data' => $row]);
    }

    // Update payment
    public function update_payment_submit()
    {
        $this->check_login();

        $this->form_validation->set_rules('payment_id', 'Payment', 'required|integer');
        $this->form_validation->set_rules('ref_no', 'Reference No', 'required|trim');
        $this->form_validation->set_rules('payment_date', 'Date', 'required|trim');
        $this->form_validation->set_rules('type', 'Type', 'required|in_list[customer,supplier]');
        $this->form_validation->set_rules('party_id', 'Party', 'required|integer');
        $this->form_validation->set_rules('mode', 'Mode', 'required|in_list[cash,cheque]');
        $this->form_validation->set_rules('amount', 'Amount', 'required|numeric|greater_than[0]');
        if ($this->input->post('mode') === 'cheque') {
            $this->form_validation->set_rules('cheque_no', 'Cheque No', 'required|trim');
            $this->form_validation->set_rules('cheque_date', 'Cheque Date', 'required|trim');
        }

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['error' => validation_errors()]);
            return;
        }

        $p = $this->security->xss_clean($this->input->post());
        $id = (int)$p['payment_id'];

        $row = $this->App_model->get_payment($id);
        if (!$row) {
            echo json_encode(['error' => '<p>Payment not found</p>']);
            return;
        }

        $data = [
            'ref_no'       => $p['ref_no'],
            'payment_date' => $p['payment_date'],
            'type'         => $p['type'],
            'party_id'     => (int)$p['party_id'],
            'mode'         => $p['mode'],
            'cheque_no'    => $p['mode'] === 'cheque' ? ($p['cheque_no'] ?? null) : null,
            'cheque_date'  => $p['mode'] === 'cheque' ? ($p['cheque_date'] ?? null) : null,
            'amount'       => (float)$p['amount'],
            'note'         => $p['note'] ?? null,
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        $ok = $this->App_model->update_payment_by_id($id, $data);
        if (!$ok) {
            echo json_encode(['error' => '<p>Could not update payment</p>']);
            return;
        }

        // Refresh ledger rows for this payment: delete & insert new
        $this->App_model->delete_ledger_by_payment($id);

        $credit = 0;
        $debit = 0;
        $desc = strtoupper($data['mode']) . ' Payment ' . $data['ref_no'];
        if ($data['type'] === 'customer') $credit = $data['amount'];
        else $debit = $data['amount'];

        $this->App_model->insert_ledger([
            'entry_date' => $data['payment_date'],
            'ref_type'   => 'payment',
            'ref_id'     => $id,
            'party_type' => $data['type'],
            'party_id'   => $data['party_id'],
            'description' => $desc,
            'debit'      => $debit,
            'credit'     => $credit,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        echo json_encode(['success' => 1, 'message' => 'Payment updated']);
    }

    // Show page
    public function stock_report()
    {
        $this->check_login();
        // for filter dropdown
        $data['products'] = $this->App_model->get_products();
        $this->load->view('stock_report', $data);
    }

    // DataTables server-side
    public function stock_report_data()
    {
        $this->check_login();

        $draw    = (int)$this->input->post('draw');
        $start   = (int)$this->input->post('start');
        $length  = (int)$this->input->post('length');

        // ordering
        $order     = $this->input->post('order')[0] ?? null;
        $columns   = $this->input->post('columns') ?? [];
        $order_by  = 'updated_at';
        $order_dir = 'desc';
        if ($order) {
            $idx = (int)$order['column'];
            $order_dir = strtolower($order['dir']) === 'asc' ? 'asc' : 'desc';
            $safe = ['product_name', 'batch_no', 'qty', 'last_cost', 'updated_at', 'value'];
            $col  = $columns[$idx]['data'] ?? 'updated_at';
            $order_by = in_array($col, $safe, true) ? $col : 'updated_at';
        }

        // filters
        $filters = [
            'product_id' => $this->input->post('product_id', TRUE),
            'batch_no'   => trim((string)$this->input->post('batch_no', TRUE)),
            'qty_min'    => $this->input->post('qty_min', TRUE),
            'qty_max'    => $this->input->post('qty_max', TRUE),
            'only_instock' => (int)($this->input->post('only_instock') ?? 0),
            'date_from'  => $this->input->post('date_from', TRUE), // Y-m-d
            'date_to'    => $this->input->post('date_to', TRUE),   // Y-m-d
        ];

        $result = $this->App_model->datatable_stock($start, $length, $order_by, $order_dir, $filters);

        echo json_encode([
            'draw'            => $draw,
            'recordsTotal'    => $result['total'],
            'recordsFiltered' => $result['filtered'],
            'data'            => $result['rows'],
        ]);
    }

    // Page
public function ledger_report()
{
    $this->check_login();
    $data['customers'] = $this->App_model->get_customers();
    $data['suppliers'] = $this->App_model->get_suppliers();
    $this->load->view('ledger_report', $data);
}

// Data for DataTables
public function ledger_report_data()
{
    $this->check_login();

    $draw   = (int)$this->input->post('draw');
    $start  = (int)$this->input->post('start');
    $length = (int)$this->input->post('length');

    // order
    $order     = $this->input->post('order')[0] ?? null;
    $columns   = $this->input->post('columns') ?? [];
    $order_by  = 'entry_date';
    $order_dir = 'asc';
    if ($order) {
        $idx = (int)$order['column'];
        $order_dir = strtolower($order['dir']) === 'desc' ? 'desc' : 'asc';
        $safe = ['entry_date','ref_type','ref_no','description','debit','credit'];
        $col  = $columns[$idx]['data'] ?? 'entry_date';
        $order_by = in_array($col, $safe, true) ? $col : 'entry_date';
    }

    // filters
    $filters = [
        'party_type' => $this->input->post('party_type', TRUE),   // customer|supplier|''(all)
        'party_id'   => $this->input->post('party_id', TRUE),     // int or ''
        'ref_type'   => $this->input->post('ref_type', TRUE),     // purchase|sales|...|payment|''(all)
        'date_from'  => $this->input->post('date_from', TRUE),    // Y-m-d
        'date_to'    => $this->input->post('date_to', TRUE),      // Y-m-d
        'q'          => $this->input->post('search')['value'] ?? ''
    ];

    $result = $this->App_model->datatable_ledger($start, $length, $order_by, $order_dir, $filters);

    echo json_encode([
        'draw'            => $draw,
        'recordsTotal'    => $result['total'],
        'recordsFiltered' => $result['filtered'],
        'data'            => $result['rows'],
        // helpful summary numbers for footer
        'opening'         => $result['opening'],
        'sum_debit'       => $result['sum_debit'],
        'sum_credit'      => $result['sum_credit'],
        'closing'         => $result['closing'],
    ]);
}

// --- DASHBOARD DATA: KPIs + Charts ---
public function dashboard_stats()
{
    $this->check_login();
    $uid = (int)$_SESSION['user_id'];

    // date helpers
    $today      = date('Y-m-d');
    $monthStart = date('Y-m-01');
    $yearStart  = date('Y-01-01');

    $out = [
        // KPI cards
        'today_sales'        => $this->App_model->sum_sales_total($uid, $today, $today),
        'today_purchases'    => $this->App_model->sum_purchases_total($uid, $today, $today),
        'mtd_sales'          => $this->App_model->sum_sales_total($uid, $monthStart, $today),
        'mtd_purchases'      => $this->App_model->sum_purchases_total($uid, $monthStart, $today),
        'receivables'        => $this->App_model->ledger_balance_by_party_type($uid, 'customer'), // credit - debit
        'payables'           => $this->App_model->ledger_balance_by_party_type($uid, 'supplier'), // credit - debit (likely negative)
        'stock_items'        => $this->App_model->stock_distinct_products_count($uid),
        'low_stock'          => $this->App_model->stock_low_items_count($uid, 10), // threshold=10 (adjust)

        // Charts
        'sales_vs_purchases_12m' => $this->App_model->series_sales_vs_purchases_last_12m($uid),
        'payments_breakdown_6m'  => $this->App_model->series_payments_breakdown_6m($uid),
        'top_5_products'         => $this->App_model->series_top_products_sales($uid, 5),
    ];

    echo json_encode(['success' => 1, 'data' => $out]);
}

// --- DASHBOARD DATA: Latest activity table ---
public function dashboard_latest()
{
    $this->check_login();
    $uid = (int)$_SESSION['user_id'];

    $out = [
        'purchases' => $this->App_model->latest_purchases($uid, 5),
        'sales'     => $this->App_model->latest_sales($uid, 5),
        'payments'  => $this->App_model->latest_payments($uid, 5),
    ];

    echo json_encode(['success' => 1, 'data' => $out]);
}


}
