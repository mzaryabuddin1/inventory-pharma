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
}
