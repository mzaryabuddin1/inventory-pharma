<?php
defined('BASEPATH') or exit('No direct script access allowed');

class App extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Karachi');
        $this->load->model('App_model');
        $this->load->library('emailsender');
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
        $this->check_login();
        $this->load->view('profile');
    }
}
