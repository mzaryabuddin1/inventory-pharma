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

  public function insert_product($data)
  {
    $this->db->insert('products', $data);
    return $this->db->insert_id();
  }

  // Products_model.php
  public function datatable_products($start, $length, $search, $order_by, $order_dir)
  {
    // 1) Fetch base products. You can add simple product-level search here to pre-filter.
    $this->db->select('id,image,product_name,generic,prices,created_at');
    $products = $this->db->get('products')->result_array();

    // 2) Explode products into 1 row per price entry
    $expanded = [];
    foreach ($products as $p) {
      $prices = [];
      if (!empty($p['prices'])) {
        $decoded = json_decode($p['prices'], true);
        if (is_array($decoded)) $prices = $decoded;
      }

      if (!$prices) {
        // If no prices, still show a row (optional; remove if you don't want empties)
        $expanded[] = [
          'id'           => $p['id'],
          'image'        => $p['image'],
          'product_name' => $p['product_name'],
          'generic'      => $p['generic'],
          'dated'        => null,
          'mrp'          => null,
          'tp'           => null,
          'created_at'   => $p['created_at'],
        ];
        continue;
      }

      foreach ($prices as $pr) {
        $expanded[] = [
          'id'           => $p['id'],
          'image'        => $p['image'],
          'product_name' => $p['product_name'],
          'generic'      => $p['generic'],
          'dated'        => isset($pr['dated']) ? $pr['dated'] : null,
          'mrp'          => isset($pr['mrp'])   ? $pr['mrp']   : null,
          'tp'           => isset($pr['tp'])    ? $pr['tp']    : null,
          'created_at'   => $p['created_at'],
        ];
      }
    }

    $total = count($expanded);

    // 3) Filter (search across name, generic, created_at, dated, mrp, tp)
    if ($search !== '') {
      $searchLower = mb_strtolower($search, 'UTF-8');
      $expanded = array_values(array_filter($expanded, function ($r) use ($searchLower) {
        $haystack = [
          $r['product_name'],
          $r['generic'],
          $r['created_at'],
          $r['dated'],
          (string)$r['mrp'],
          (string)$r['tp'],
        ];
        foreach ($haystack as $h) {
          if ($h !== null && mb_stripos((string)$h, $searchLower, 0, 'UTF-8') !== false) {
            return true;
          }
        }
        return false;
      }));
    }

    $filtered = count($expanded);

    // 4) Order
    $order_dir = ($order_dir === 'desc') ? 'desc' : 'asc';
    $cmp = function ($a, $b) use ($order_by, $order_dir) {
      $va = $a[$order_by] ?? null;
      $vb = $b[$order_by] ?? null;

      // numeric compare for mrp/tp
      if (in_array($order_by, ['mrp', 'tp'], true)) {
        $va = is_numeric($va) ? (float)$va : null;
        $vb = is_numeric($vb) ? (float)$vb : null;
      }

      if ($va == $vb) return 0;
      if ($order_dir === 'asc')  return ($va < $vb) ? -1 : 1;
      else                       return ($va > $vb) ? -1 : 1;
    };
    usort($expanded, $cmp);

    // 5) Page slice
    if ($length > 0) {
      $expanded = array_slice($expanded, $start, $length);
    }

    return [
      'total'    => $total,
      'filtered' => $filtered,
      'rows'     => $expanded,
    ];
  }

  public function get_product($id)
  {
    return $this->db->get_where('products', ['id' => (int)$id])->row_array();
  }

  public function product_update_by_id($id, $data)
  {
    $this->db->where('id', (int)$id);
    return $this->db->update('products', $data);
  }


  public function insert_supplier($data)
  {
    $this->db->insert('suppliers', $data);
    return $this->db->insert_id();
  }

  public function get_supplier($id)
  {
    return $this->db->get_where('suppliers', ['id' => (int)$id])->row_array();
  }

  public function update_supplier_by_id($id, $data)
  {
    $this->db->where('id', (int)$id);
    return $this->db->update('suppliers', $data);
  }

  /* DataTables server-side for suppliers */
  public function datatable_suppliers($start, $length, $search, $order_by, $order_dir)
  {
    $table = 'suppliers';

    // total
    $total = $this->db->count_all($table);

    // base + search (keep builder alive)
    $this->db->from($table);
    if ($search !== '') {
      $this->db->group_start()
        ->like('name', $search)
        ->or_like('email', $search)
        ->or_like('phone', $search)
        ->or_like('address', $search)
        ->or_like('created_at', $search)
        ->group_end();
    }

    $filtered = $this->db->count_all_results('', FALSE); // don't reset builder

    // select + order + limit
    $this->db->select('id, logo, name, email, phone, address, status, created_at');
    $safe = ['id', 'logo', 'name', 'email', 'phone', 'address', 'status', 'created_at'];
    if (!in_array($order_by, $safe, true)) $order_by = 'name';
    $order_dir = strtolower($order_dir) === 'desc' ? 'desc' : 'asc';
    $this->db->order_by($order_by, $order_dir);

    if ($length > 0) $this->db->limit($length, $start);

    $rows = $this->db->get()->result_array();

    return [
      'total'    => $total,
      'filtered' => $filtered,
      'rows'     => $rows,
    ];
  }


public function insert_customer($data)
{
    $this->db->insert('customers', $data);
    return $this->db->insert_id();
}

public function get_customer($id)
{
    return $this->db->get_where('customers', ['id' => (int)$id])->row_array();
}

public function update_customer_by_id($id, $data)
{
    $this->db->where('id', (int)$id);
    return $this->db->update('customers', $data);
}

/* DataTables server-side for customers */
public function datatable_customers($start, $length, $search, $order_by, $order_dir)
{
    $table = 'customers';

    // total
    $total = $this->db->count_all($table);

    // base + search (keep builder alive)
    $this->db->from($table);
    if ($search !== '') {
        $this->db->group_start()
                 ->like('name', $search)
                 ->or_like('email', $search)
                 ->or_like('phone', $search)
                 ->or_like('address', $search)
                 ->or_like('created_at', $search)
                 ->group_end();
    }

    $filtered = $this->db->count_all_results('', FALSE); // don't reset

    // select + order + limit
    $this->db->select('id, avatar, name, email, phone, address, status, created_at');
    $safe = ['id','avatar','name','email','phone','address','status','created_at'];
    if (!in_array($order_by, $safe, true)) $order_by = 'name';
    $order_dir = strtolower($order_dir) === 'desc' ? 'desc' : 'asc';
    $this->db->order_by($order_by, $order_dir);

    if ($length > 0) $this->db->limit($length, $start);

    $rows = $this->db->get()->result_array();

    return [
        'total'    => $total,
        'filtered' => $filtered,
        'rows'     => $rows,
    ];
}

}
