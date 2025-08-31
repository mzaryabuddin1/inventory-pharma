<?php
defined('BASEPATH') or exit('No direct script access allowed');


class App_model extends CI_Model
{
  public function login_submit($params)
  {
    return $this->db->select("*")->from('users')->where('email', $params['email'])->where('password', $params['password'])->get()->row_array();
  }

}
