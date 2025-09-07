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
    $uid = (int)$_SESSION['user_id'];
    // 1) Fetch base products. You can add simple product-level search here to pre-filter.
    $products = $this->db->select('id,image,product_name,generic,prices,created_at')
      ->from('products')
      ->where('created_by', $uid)
      ->get()->result_array();

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
    return $this->db->get_where('products', [
      'id' => (int)$id,
      'created_by' => (int)$_SESSION['user_id']
    ])->row_array();
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
    return $this->db->get_where('suppliers', [
      'id' => (int)$id,
      'created_by' => (int)$_SESSION['user_id']
    ])->row_array();
  }

  public function update_supplier_by_id($id, $data)
  {
    $this->db->where('id', (int)$id);
    return $this->db->update('suppliers', $data);
  }

  /* DataTables server-side for suppliers */
  public function datatable_suppliers($start, $length, $search, $order_by, $order_dir)
  {
    $uid = (int)$_SESSION['user_id'];
    $table = 'suppliers';

    // total
    $this->db->from($table)->where('created_by', $uid);
    $total = $this->db->count_all_results();

    // base + search (keep builder alive)
    $this->db->from($table)->where('created_by', $uid);
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
    return $this->db->get_where('customers', [
      'id' => (int)$id,
      'created_by' => (int)$_SESSION['user_id']
    ])->row_array();
  }

  public function update_customer_by_id($id, $data)
  {
    $this->db->where('id', (int)$id);
    return $this->db->update('customers', $data);
  }

  /* DataTables server-side for customers */
  public function datatable_customers($start, $length, $search, $order_by, $order_dir)
  {
    $uid = (int)$_SESSION['user_id'];
    $table = 'customers';

    // total
    $this->db->from($table)->where('created_by', $uid);
    $total = $this->db->count_all_results();

    // base + search (keep builder alive)
    $this->db->from($table)->where('created_by', $uid);
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
    $safe = ['id', 'avatar', 'name', 'email', 'phone', 'address', 'status', 'created_at'];
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

  /* ===================== LEDGER & STOCK HELPERS ===================== */

  private function ledger_post($party_type, $party_id, $ref_type, $ref_no, $amount, $direction)
  {
    $row = [
      'party_type' => $party_type,
      'party_id'   => (int)$party_id,
      'ref_type'   => $ref_type,
      'ref_no'     => $ref_no,
      'entry_date' => date('Y-m-d H:i:s'),
      'debit'      => $direction === 'debit'  ? (float)$amount : 0.0,
      'credit'     => $direction === 'credit' ? (float)$amount : 0.0,
      'created_at' => date('Y-m-d H:i:s'),
      'created_by' => (int)$_SESSION['user_id'],     // ⬅️ tenant
    ];
    return $this->db->insert('ledger', $row);
  }

  private function stock_adjust($product_id, $batch_no, $qty_delta, $last_cost = null)
  {
    $uid = (int)$_SESSION['user_id'];

    $this->db->set('qty', "qty + (" . (int)$qty_delta . ")", FALSE);
    if ($last_cost !== null) $this->db->set('last_cost', (float)$last_cost);
    $this->db->set('updated_at', date('Y-m-d H:i:s'));
    $this->db->where([
      'product_id' => (int)$product_id,
      'batch_no'   => (string)$batch_no,
      'created_by' => $uid
    ])->update('stock');

    if ($this->db->affected_rows() === 0) {
      $this->db->insert('stock', [
        'product_id' => (int)$product_id,
        'batch_no'   => (string)$batch_no,
        'qty'        => (int)$qty_delta,
        'last_cost'  => $last_cost !== null ? (float)$last_cost : null,
        'updated_at' => date('Y-m-d H:i:s'),
        'created_by' => $uid,                             // ⬅️ tenant
      ]);
    }
    return true;
  }

  /* ===================== PURCHASE ===================== */

  public function purchase_create($data, $items)
  {
    $this->db->trans_start();

    $this->db->insert('purchases', $data);
    $pid = $this->db->insert_id();

    // STOCK: increase per item
    foreach ($items as $it) {
      $this->stock_adjust((int)$it['product_id'], $it['batch_no'], + ((int)$it['qty']), (float)$it['price']);
    }

    // LEDGER: supplier debit (we owe supplier)
    $this->ledger_post('supplier', (int)$data['supplier_id'], 'purchase', $data['ref_no'], (float)$data['total_amount'], 'debit');

    $this->db->trans_complete();
    return $this->db->trans_status();
  }

  public function get_purchase($id)
  {
    return $this->db->get_where('purchases', [
      'id' => (int)$id,
      'created_by' => (int)$_SESSION['user_id']
    ])->row_array();
  }

  public function datatable_purchases($start, $length, $search, $order_by, $order_dir)
  {
    $uid = (int)$_SESSION['user_id'];

    $table = 'purchases';
    $this->db->from($table)->where('created_by', $uid);
    $total = $this->db->count_all_results();

    $this->db->from($table)->where('created_by', $uid);
    if ($search !== '') {
      $this->db->group_start()
        ->like('ref_no', $search)
        ->or_like('purchase_date', $search)
        ->or_like('total_amount', $search)
        ->group_end();
    }
    $filtered = $this->db->count_all_results('', FALSE);

    $this->db->select('id,ref_no,purchase_date,total_amount');
    $order_dir = strtolower($order_dir) === 'asc' ? 'asc' : 'desc';
    $safe = ['ref_no', 'purchase_date', 'total_amount'];
    if (!in_array($order_by, $safe, true)) $order_by = 'purchase_date';
    $this->db->order_by($order_by, $order_dir);
    if ($length > 0) $this->db->limit($length, $start);

    $rows = $this->db->get()->result_array();
    return ['total' => $total, 'filtered' => $filtered, 'rows' => $rows];
  }

  public function purchase_update($id, $old, $data, $items)
  {
    $this->db->trans_start();

    // REVERSE old stock & ledger
    $old_items = json_decode($old['items'], true) ?: [];
    foreach ($old_items as $it) {
      $this->stock_adjust((int)$it['product_id'], $it['batch_no'], - ((int)$it['qty']));
    }
    // reverse ledger: credit supplier (undo previous debit)
    $this->ledger_post('supplier', (int)$old['supplier_id'], 'purchase', $old['ref_no'], (float)$old['total_amount'], 'credit');

    // UPDATE header
    $this->db->where('id', (int)$id)->update('purchases', $data);

    // APPLY new stock & ledger
    foreach ($items as $it) {
      $this->stock_adjust((int)$it['product_id'], $it['batch_no'], + ((int)$it['qty']), (float)$it['price']);
    }
    $this->ledger_post('supplier', (int)$data['supplier_id'], 'purchase', $data['ref_no'], (float)$data['total_amount'], 'debit');

    $this->db->trans_complete();
    return $this->db->trans_status();
  }

  /* ===================== PURCHASE RETURN ===================== */

  public function purchase_return_create($data, $items)
  {
    $this->db->trans_start();

    $this->db->insert('purchase_returns', $data);
    // STOCK: decrease
    foreach ($items as $it) {
      $this->stock_adjust((int)$it['product_id'], $it['batch_no'], - ((int)$it['qty']));
    }
    // LEDGER: supplier credit (we owe less)
    $this->ledger_post('supplier', (int)$data['supplier_id'], 'purchase_return', $data['ref_no'], (float)$data['return_amount'], 'credit');

    $this->db->trans_complete();
    return $this->db->trans_status();
  }

  public function get_purchase_return($id)
  {
    return $this->db->get_where('purchase_returns', [
      'id' => (int)$id,
      'created_by' => (int)$_SESSION['user_id']
    ])->row_array();
  }

  public function datatable_purchase_returns($start, $length, $search, $order_by, $order_dir)
  {
    $uid = (int)$_SESSION['user_id'];
    $table = 'purchase_returns';
    $this->db->from($table)->where('created_by', $uid);
    $total = $this->db->count_all_results();

    $this->db->from($table)->where('created_by', $uid);
    if ($search !== '') {
      $this->db->group_start()
        ->like('ref_no', $search)
        ->or_like('return_date', $search)
        ->or_like('return_amount', $search)
        ->group_end();
    }
    $filtered = $this->db->count_all_results('', FALSE);

    $this->db->select('id,ref_no,return_date,return_amount');
    $order_dir = strtolower($order_dir) === 'asc' ? 'asc' : 'desc';
    $safe = ['ref_no', 'return_date', 'return_amount'];
    if (!in_array($order_by, $safe, true)) $order_by = 'return_date';
    $this->db->order_by($order_by, $order_dir);
    if ($length > 0) $this->db->limit($length, $start);
    $this->db->where('created_by', $_SESSION['user_id']);
    $rows = $this->db->get()->result_array();
    return ['total' => $total, 'filtered' => $filtered, 'rows' => $rows];
  }

  public function purchase_return_update($id, $old, $data, $items)
  {
    $this->db->trans_start();

    // REVERSE old: add back stock; reverse ledger (debit)
    $old_items = json_decode($old['items'], true) ?: [];
    foreach ($old_items as $it) {
      $this->stock_adjust((int)$it['product_id'], $it['batch_no'], + ((int)$it['qty']));
    }
    $this->ledger_post('supplier', (int)$old['supplier_id'], 'purchase_return', $old['ref_no'], (float)$old['return_amount'], 'debit');

    // UPDATE header
    $this->db->where('id', (int)$id)->update('purchase_returns', $data);

    // APPLY new: decrease stock; credit ledger
    foreach ($items as $it) {
      $this->stock_adjust((int)$it['product_id'], $it['batch_no'], - ((int)$it['qty']));
    }
    $this->ledger_post('supplier', (int)$data['supplier_id'], 'purchase_return', $data['ref_no'], (float)$data['return_amount'], 'credit');

    $this->db->trans_complete();
    return $this->db->trans_status();
  }

  /* ===================== SALES ===================== */

  public function sale_create($data, $items)
  {
    $this->db->trans_start();

    $this->db->insert('sales', $data);
    // STOCK: decrease
    foreach ($items as $it) {
      $this->stock_adjust((int)$it['product_id'], $it['batch_no'], - ((int)$it['qty']));
    }
    // LEDGER: customer credit (they owe us)
    $this->ledger_post('customer', (int)$data['customer_id'], 'sales', $data['invoice_no'], (float)$data['total_amount'], 'credit');

    $this->db->trans_complete();
    return $this->db->trans_status();
  }

  public function get_sale($id)
  {
    return $this->db->get_where('sales', [
      'id' => (int)$id,
      'created_by' => (int)$_SESSION['user_id']
    ])->row_array();
  }

  public function datatable_sales($start, $length, $search, $order_by, $order_dir)
  {
    $uid = (int)$_SESSION['user_id'];
    $table = 'sales';
    $this->db->from($table)->where('created_by', $uid);
    $total = $this->db->count_all_results();


    $this->db->from($table)->where('created_by', $uid);
    if ($search !== '') {
      $this->db->group_start()
        ->like('invoice_no', $search)
        ->or_like('sale_date', $search)
        ->or_like('total_amount', $search)
        ->group_end();
    }
    $filtered = $this->db->count_all_results('', FALSE);

    $this->db->select('id,invoice_no,sale_date,total_amount,customer_id');
    $order_dir = strtolower($order_dir) === 'asc' ? 'asc' : 'desc';
    $safe = ['invoice_no', 'sale_date', 'total_amount'];
    if (!in_array($order_by, $safe, true)) $order_by = 'sale_date';
    $this->db->order_by($order_by, $order_dir);
    if ($length > 0) $this->db->limit($length, $start);
    $this->db->where('created_by', $_SESSION['user_id']);
    $rows = $this->db->get()->result_array();
    return ['total' => $total, 'filtered' => $filtered, 'rows' => $rows];
  }

  public function sale_update($id, $old, $data, $items)
  {
    $this->db->trans_start();

    // REVERSE old: add back stock; reverse ledger (debit customer)
    $old_items = json_decode($old['items'], true) ?: [];
    foreach ($old_items as $it) {
      $this->stock_adjust((int)$it['product_id'], $it['batch_no'], + ((int)$it['qty']));
    }
    $this->ledger_post('customer', (int)$old['customer_id'], 'sales', $old['invoice_no'], (float)$old['total_amount'], 'debit');

    // UPDATE
    $this->db->where('id', (int)$id)->update('sales', $data);

    // APPLY new: decrease stock; credit ledger
    foreach ($items as $it) {
      $this->stock_adjust((int)$it['product_id'], $it['batch_no'], - ((int)$it['qty']));
    }
    $this->ledger_post('customer', (int)$data['customer_id'], 'sales', $data['invoice_no'], (float)$data['total_amount'], 'credit');

    $this->db->trans_complete();
    return $this->db->trans_status();
  }

  /* ===================== SALES RETURN ===================== */

  public function sales_return_create($data, $items)
  {
    $this->db->trans_start();

    $this->db->insert('sales_returns', $data);
    // STOCK: increase
    foreach ($items as $it) {
      $this->stock_adjust((int)$it['product_id'], $it['batch_no'], + ((int)$it['qty']));
    }
    // LEDGER: customer debit (we owe them / reduce receivable)
    $this->ledger_post('customer', (int)$data['customer_id'], 'sales_return', $data['ref_no'], (float)$data['return_amount'], 'debit');

    $this->db->trans_complete();
    return $this->db->trans_status();
  }

  public function get_sales_return($id)
  {
    return $this->db->get_where('sales_returns', [
      'id' => (int)$id,
      'created_by' => (int)$_SESSION['user_id']
    ])->row_array();
  }

  public function datatable_sales_returns($start, $length, $search, $order_by, $order_dir)
  {
    $uid = (int)$_SESSION['user_id'];
    $table = 'sales_returns';

    $this->db->from($table)->where('created_by', $uid);
    $total = $this->db->count_all_results();

    $this->db->from($table)->where('created_by', $uid);
    if ($search !== '') {
      $this->db->group_start()
        ->like('ref_no', $search)
        ->or_like('return_date', $search)
        ->or_like('return_amount', $search)
        ->group_end();
    }
    $filtered = $this->db->count_all_results('', FALSE);

    $this->db->select('id,ref_no,return_date,return_amount,customer_id');
    $order_dir = strtolower($order_dir) === 'asc' ? 'asc' : 'desc';
    $safe = ['ref_no', 'return_date', 'return_amount'];
    if (!in_array($order_by, $safe, true)) $order_by = 'return_date';
    $this->db->order_by($order_by, $order_dir);
    if ($length > 0) $this->db->limit($length, $start);
    $this->db->where('created_by', $_SESSION['user_id']);
    $rows = $this->db->get()->result_array();
    return ['total' => $total, 'filtered' => $filtered, 'rows' => $rows];
  }

  public function sales_return_update($id, $old, $data, $items)
  {
    $this->db->trans_start();

    // REVERSE old: decrease stock; reverse ledger (credit)
    $old_items = json_decode($old['items'], true) ?: [];
    foreach ($old_items as $it) {
      $this->stock_adjust((int)$it['product_id'], $it['batch_no'], - ((int)$it['qty']));
    }
    $this->ledger_post('customer', (int)$old['customer_id'], 'sales_return', $old['ref_no'], (float)$old['return_amount'], 'credit');

    // UPDATE
    $this->db->where('id', (int)$id)->update('sales_returns', $data);

    // APPLY new: increase stock; debit ledger
    foreach ($items as $it) {
      $this->stock_adjust((int)$it['product_id'], $it['batch_no'], + ((int)$it['qty']));
    }
    $this->ledger_post('customer', (int)$data['customer_id'], 'sales_return', $data['ref_no'], (float)$data['return_amount'], 'debit');

    $this->db->trans_complete();
    return $this->db->trans_status();
  }

  // --- Payments ---
  public function insert_payment($data)
  {
    $this->db->insert('payments', $data);
    return $this->db->insert_id();
  }

  public function update_payment_by_id($id, $data)
  {
    $this->db->where('id', (int)$id);
    return $this->db->update('payments', $data);
  }

  public function get_payment($id)
  {
    return $this->db->get_where('payments', [
      'id' => (int)$id,
      'created_by' => (int)$_SESSION['user_id']
    ])->row_array();
  }

  /**
   * Server-side list with basic search/order/pagination.
   * Searchable: ref_no, mode, amount, payment_date.
   * Party name is resolved outside (controller) to keep SQL simple.
   */
  public function datatable_payments($start, $length, $search, $order_by, $order_dir)
  {
    $uid = (int)$_SESSION['user_id'];
    $this->db->from('payments')->where('created_by', $uid);

    // total before filter
    $total = $this->db->count_all_results('', FALSE);

    // search
    if ($search !== '') {
      $this->db->group_start()
        ->like('ref_no', $search)
        ->or_like('mode', $search)
        ->or_like('amount', $search)
        ->or_like('payment_date', $search)
        ->group_end();
    }

    // filtered count
    $filtered = $this->db->count_all_results('', FALSE);

    // order safety map
    $safeMap = ['ref_no', 'payment_date', 'type', 'mode', 'amount', 'id'];
    $order_by = in_array($order_by, $safeMap, true) ? $order_by : 'payment_date';
    $order_dir = strtolower($order_dir) === 'asc' ? 'asc' : 'desc';
    $this->db->order_by($order_by, $order_dir);

    // page slice
    if ($length > 0) $this->db->limit($length, $start);

    $rows = $this->db->get()->result_array();

    return ['total' => $total, 'filtered' => $filtered, 'rows' => $rows];
  }

  // Resolve party label (helper): fetch name quickly
  public function get_supplier_name($id)
  {
    $row = $this->db->select('name')->from('suppliers')->where('id', (int)$id)->get()->row_array();
    return $row['name'] ?? null;
  }
  public function get_customer_name($id)
  {
    $row = $this->db->select('name')->from('customers')->where('id', (int)$id)->get()->row_array();
    return $row['name'] ?? null;
  }

  // Ledger insert (generic)
  public function insert_ledger($data)
  {
    $this->db->insert('ledger', $data);
    return $this->db->insert_id();
  }

  // On payment update, you may want to replace ledger row(s) for that payment:
  public function delete_ledger_by_payment($payment_id)
  {
    $this->db->where([
      'ref_type'   => 'payment',
      'ref_id'     => (int)$payment_id,
      'created_by' => (int)$_SESSION['user_id']
    ])->delete('ledger');
    return $this->db->affected_rows();
  }

  public function get_products()
  {
    return $this->db->get_where('products', array('created_by' => $_SESSION['user_id']))->result_array();
  }

  public function get_suppliers()
  {
    return $this->db->get_where('suppliers', array('created_by' => $_SESSION['user_id']))->result_array();
  }

  public function get_purchases()
  {
    return $this->db->get_where('purchases', array('created_by' => $_SESSION['user_id']))->result_array();
  }

  public function get_customers()
  {
    return $this->db->get_where('customers', array('created_by' => $_SESSION['user_id']))->result_array();
  }

  public function get_invoices()
  {
    return $this->db->get_where('sales', array('created_by' => $_SESSION['user_id']))->result_array();
  }

  public function datatable_stock($start, $length, $order_by, $order_dir, $filters)
  {
    $uid = (int)$_SESSION['user_id'];

    // --- Base: join products for product_name
    $this->db->from('stock s')
      ->join('products p', 'p.id = s.product_id', 'left')
      ->where('s.created_by', $uid);

    // Total (tenant-scoped)
    $total = $this->db->count_all_results();

    // Rebuild for filtered query
    $this->db->from('stock s')
      ->join('products p', 'p.id = s.product_id', 'left')
      ->where('s.created_by', $uid);

    // --- Filters ---
    if (!empty($filters['product_id'])) {
      $this->db->where('s.product_id', (int)$filters['product_id']);
    }
    if (!empty($filters['batch_no'])) {
      $this->db->like('s.batch_no', $filters['batch_no']);
    }
    if (!empty($filters['qty_min']) || $filters['qty_min'] === '0') {
      $this->db->where('s.qty >=', (int)$filters['qty_min']);
    }
    if (!empty($filters['qty_max']) || $filters['qty_max'] === '0') {
      $this->db->where('s.qty <=', (int)$filters['qty_max']);
    }
    if (!empty($filters['only_instock'])) {
      $this->db->where('s.qty >', 0);
    }
    // Date range on updated_at
    if (!empty($filters['date_from'])) {
      $this->db->where('DATE(s.updated_at) >=', $filters['date_from']);
    }
    if (!empty($filters['date_to'])) {
      $this->db->where('DATE(s.updated_at) <=', $filters['date_to']);
    }

    // Count after filters
    $filtered = $this->db->count_all_results('', FALSE);

    // Select + order + page
    $this->db->select('s.product_id, p.product_name, s.batch_no, s.qty, s.last_cost, s.updated_at');
    $safe = ['product_name', 'batch_no', 'qty', 'last_cost', 'updated_at'];
    $order_by  = in_array($order_by, $safe, true) ? $order_by : 'updated_at';
    $order_dir = strtolower($order_dir) === 'asc' ? 'asc' : 'desc';
    $this->db->order_by($order_by, $order_dir);

    if ($length > 0) $this->db->limit($length, $start);

    $rows = $this->db->get()->result_array();

    // Add computed value (qty * last_cost)
    foreach ($rows as &$r) {
      $q = is_numeric($r['qty']) ? (float)$r['qty'] : 0;
      $c = is_numeric($r['last_cost']) ? (float)$r['last_cost'] : 0;
      $r['value'] = round($q * $c, 2);
    }

    return ['total' => $total, 'filtered' => $filtered, 'rows' => $rows];
  }

  // Opening balance up to (but excluding) date_from
  private function ledger_opening_balance($party_type, $party_id, $date_from, $uid)
  {
    $this->db->reset_query();                 // <<— important

    $this->db->select('COALESCE(SUM(credit - debit),0) AS bal')
      ->from('ledger')
      ->where('created_by', (int)$uid);

    if (!empty($party_type)) $this->db->where('party_type', $party_type);
    if (!empty($party_id))   $this->db->where('party_id', (int)$party_id);
    if (!empty($date_from))  $this->db->where('entry_date <', $date_from . ' 00:00:00');

    return (float)$this->db->get()->row()->bal;
  }

  public function datatable_ledger($start, $length, $order_by, $order_dir, $filters)
  {
    $uid = (int)$_SESSION['user_id'];

    // --- TOTAL (tenant scoped) ---
    $this->db->reset_query();
    $total = $this->db->from('ledger')
      ->where('created_by', $uid)
      ->count_all_results();

    // --- FILTERED COUNT ---
    $this->db->reset_query();
    $this->db->from('ledger')->where('created_by', $uid);
    if (!empty($filters['party_type'])) $this->db->where('party_type', $filters['party_type']);
    if (!empty($filters['party_id']))   $this->db->where('party_id', (int)$filters['party_id']);
    if (!empty($filters['ref_type']))   $this->db->where('ref_type', $filters['ref_type']);
    if (!empty($filters['date_from']))  $this->db->where('DATE(entry_date) >=', $filters['date_from']);
    if (!empty($filters['date_to']))    $this->db->where('DATE(entry_date) <=', $filters['date_to']);
    if (!empty($filters['q'])) {
      $q = $filters['q'];
      $this->db->group_start()
        ->like('ref_no', $q)
        ->or_like('ref_type', $q)
        ->or_like('description', $q)
        ->group_end();
    }
    $filtered = $this->db->count_all_results();

    // --- OPENING (before date_from) ---
    $opening = $this->ledger_opening_balance(
      $filters['party_type'] ?? '',
      $filters['party_id']   ?? '',
      $filters['date_from']  ?? '',
      $uid
    );

    // --- PAGE DATA ---
    $this->db->reset_query();
    $this->db->select('id, entry_date, ref_type, ref_no, description, debit, credit')
      ->from('ledger')
      ->where('created_by', $uid);

    if (!empty($filters['party_type'])) $this->db->where('party_type', $filters['party_type']);
    if (!empty($filters['party_id']))   $this->db->where('party_id', (int)$filters['party_id']);
    if (!empty($filters['ref_type']))   $this->db->where('ref_type', $filters['ref_type']);
    if (!empty($filters['date_from']))  $this->db->where('DATE(entry_date) >=', $filters['date_from']);
    if (!empty($filters['date_to']))    $this->db->where('DATE(entry_date) <=', $filters['date_to']);
    if (!empty($filters['q'])) {
      $q = $filters['q'];
      $this->db->group_start()
        ->like('ref_no', $q)
        ->or_like('ref_type', $q)
        ->or_like('description', $q)
        ->group_end();
    }

    $safe = ['entry_date', 'ref_type', 'ref_no', 'description', 'debit', 'credit', 'id'];
    $order_by  = in_array($order_by, $safe, true) ? $order_by : 'entry_date';
    $order_dir = strtolower($order_dir) === 'desc' ? 'desc' : 'asc';
    $this->db->order_by($order_by, $order_dir);

    if ($length > 0) $this->db->limit($length, $start);

    $rows = $this->db->get()->result_array();

    // running/page totals
    $running = $opening;
    $sum_debit = 0;
    $sum_credit = 0;
    foreach ($rows as &$r) {
      $d = (float)$r['debit'];
      $c = (float)$r['credit'];
      $running += ($c - $d);
      $sum_debit  += $d;
      $sum_credit += $c;
      $r['balance'] = $running;
    }
    $closing = $running;

    return [
      'total'     => $total,
      'filtered'  => $filtered,
      'rows'      => $rows,
      'opening'   => $opening,
      'sum_debit' => $sum_debit,
      'sum_credit' => $sum_credit,
      'closing'   => $closing,
    ];
  }

  // ---- KPI sums ----
  public function sum_sales_total($uid, $date_from, $date_to)
  {
    $this->db->select('COALESCE(SUM(total_amount),0) AS t')
      ->from('sales')
      ->where('created_by', (int)$uid)
      ->where('DATE(sale_date) >=', $date_from)
      ->where('DATE(sale_date) <=', $date_to);
    return (float)$this->db->get()->row()->t;
  }

  public function sum_purchases_total($uid, $date_from, $date_to)
  {
    $this->db->select('COALESCE(SUM(total_amount),0) AS t')
      ->from('purchases')
      ->where('created_by', (int)$uid)
      ->where('DATE(purchase_date) >=', $date_from)
      ->where('DATE(purchase_date) <=', $date_to);
    return (float)$this->db->get()->row()->t;
  }

  // credit - debit for a party type
  public function ledger_balance_by_party_type($uid, $party_type)
  {
    $this->db->select('COALESCE(SUM(credit - debit),0) AS bal')
      ->from('ledger')
      ->where('created_by', (int)$uid)
      ->where('party_type', $party_type);
    return (float)$this->db->get()->row()->bal;
  }

  // ---- Stock info ----
  public function stock_distinct_products_count($uid)
  {
    $this->db->select('COUNT(DISTINCT product_id) AS c')
      ->from('stock')
      ->where('created_by', (int)$uid);
    $row = $this->db->get()->row_array();
    return (int)($row['c'] ?? 0);
  }

  public function stock_low_items_count($uid, $threshold = 10)
  {
    $this->db->select('COUNT(*) AS c')
      ->from('stock')
      ->where('created_by', (int)$uid)
      ->where('qty <=', (int)$threshold);
    $row = $this->db->get()->row_array();
    return (int)($row['c'] ?? 0);
  }

  // ---- Chart series ----
  public function series_sales_vs_purchases_last_12m($uid)
  {
    // Build month labels (YYYY-MM) and sums
    $labels = [];
    $sales  = [];
    $purch  = [];
    for ($i = 11; $i >= 0; $i--) {
      $ym   = date('Y-m', strtotime("-$i months"));
      $from = $ym . '-01';
      $to   = date('Y-m-t', strtotime($from));

      $labels[] = $ym;
      $sales[]  = $this->sum_sales_total($uid, $from, $to);
      $purch[]  = $this->sum_purchases_total($uid, $from, $to);
    }
    return ['labels' => $labels, 'sales' => $sales, 'purchases' => $purch];
  }

  public function series_payments_breakdown_6m($uid)
  {
    $labels = [];
    $customer = [];
    $supplier = [];
    for ($i = 5; $i >= 0; $i--) {
      $ym = date('Y-m', strtotime("-$i months"));
      $from = $ym . '-01';
      $to   = date('Y-m-t', strtotime($from));

      // customers (incoming = sum amount where type='customer')
      $this->db->select('COALESCE(SUM(amount),0) AS t')
        ->from('payments')
        ->where('created_by', (int)$uid)
        ->where('type', 'customer')
        ->where('DATE(payment_date) >=', $from)
        ->where('DATE(payment_date) <=', $to);
      $cin = (float)$this->db->get()->row()->t;

      // suppliers (outgoing)
      $this->db->select('COALESCE(SUM(amount),0) AS t')
        ->from('payments')
        ->where('created_by', (int)$uid)
        ->where('type', 'supplier')
        ->where('DATE(payment_date) >=', $from)
        ->where('DATE(payment_date) <=', $to);
      $sout = (float)$this->db->get()->row()->t;

      $labels[]   = $ym;
      $customer[] = $cin;
      $supplier[] = $sout;
    }
    return ['labels' => $labels, 'customer' => $customer, 'supplier' => $supplier];
  }

  public function series_top_products_sales($uid, $limit = 5)
  {
    // Parse sales.items JSON and aggregate qty * price by product_id
    // (Simple & portable approach)
    $this->db->select('id, items')
      ->from('sales')
      ->where('created_by', (int)$uid)
      ->order_by('id', 'DESC');
    $rows = $this->db->get()->result_array();

    $agg = []; // product_id => amount
    foreach ($rows as $r) {
      $items = json_decode($r['items'], true);
      if (!is_array($items)) continue;
      foreach ($items as $it) {
        $pid = (int)($it['product_id'] ?? 0);
        $qty = (int)($it['qty'] ?? 0);
        $prc = (float)($it['price'] ?? 0);
        if ($pid <= 0) continue;
        $agg[$pid] = ($agg[$pid] ?? 0) + ($qty * $prc);
      }
    }

    arsort($agg); // high to low
    $top = array_slice($agg, 0, $limit, true);

    // resolve product names
    $labels = [];
    $values = [];
    if ($top) {
      $ids = array_keys($top);
      $prows = $this->db->select('id, product_name')->from('products')
        ->where_in('id', $ids)->get()->result_array();
      $nameById = [];
      foreach ($prows as $p) $nameById[(int)$p['id']] = $p['product_name'];

      foreach ($top as $pid => $amt) {
        $labels[] = $nameById[$pid] ?? ('#' . $pid);
        $values[] = (float)$amt;
      }
    }
    return ['labels' => $labels, 'values' => $values];
  }

  // ---- Latest activity ----
  public function latest_purchases($uid, $limit = 5)
  {
    return $this->db->select('ref_no, purchase_date, total_amount')
      ->from('purchases')
      ->where('created_by', (int)$uid)
      ->order_by('id', 'DESC')
      ->limit($limit)->get()->result_array();
  }
  public function latest_sales($uid, $limit = 5)
  {
    return $this->db->select('invoice_no, sale_date, total_amount, customer_id')
      ->from('sales')
      ->where('created_by', (int)$uid)
      ->order_by('id', 'DESC')
      ->limit($limit)->get()->result_array();
  }
  public function latest_payments($uid, $limit = 5)
  {
    return $this->db->select('ref_no, payment_date, type, amount, mode')
      ->from('payments')
      ->where('created_by', (int)$uid)
      ->order_by('id', 'DESC')
      ->limit($limit)->get()->result_array();
  }

  
}
