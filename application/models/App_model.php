<?php
defined('BASEPATH') or exit('No direct script access allowed');


class App_model extends CI_Model
{
  public function login_submit($params)
  {
    return $this->db->select("*")->from('users')->where('email', $params['email'])->where('password', $params['password'])->get()->row_array();
  }

  public function insert_user($data)
  {
    $this->db->insert('users', $data);
    return $this->db->insert_id();
  }

  public function get_user_by_email($email)
  {
    return $this->db->get_where('users', array('email' => $email))->row_array();
  }

  public function update_user_by_id($id, $data)
  {
    $this->db->where('id', $id);
    return $this->db->update('users', $data);
  }
}
