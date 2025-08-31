<?php
defined('BASEPATH') or exit('No direct script access allowed');

class App extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('App_model');
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

        // print_r($this->data['password']);
        // exit; 

        $user = $this->Model->login_submit($this->data);

        if (!$user) {
            $errors = array('error' => '<p>Combination Does Not Exists!.</p>');
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

        $success = array('success' => 1);
        print_r(json_encode($success));
    }



}
