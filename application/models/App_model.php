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
    // direction: 'debit' or 'credit'
    $row = [
      'party_type' => $party_type,
      'party_id'   => (int)$party_id,
      'ref_type'   => $ref_type,
      'ref_no'     => $ref_no,
      'entry_date' => date('Y-m-d H:i:s'),
      'debit'      => $direction === 'debit'  ? $amount : 0,
      'credit'     => $direction === 'credit' ? $amount : 0,
      'created_at' => date('Y-m-d H:i:s'),
    ];
    return $this->db->insert('ledger', $row);
  }

  private function stock_adjust($product_id, $batch_no, $qty_delta, $last_cost = null)
  {
    // upsert stock row by (product_id, batch_no)
    $product_id = (int)$product_id;
    $batch_no   = (string)$batch_no;

    // try update
    $this->db->set('qty', "qty + ({$qty_delta})", FALSE);
    if ($last_cost !== null) $this->db->set('last_cost', $last_cost);
    $this->db->set('updated_at', date('Y-m-d H:i:s'));
    $this->db->where(['product_id' => $product_id, 'batch_no' => $batch_no]);
    $this->db->update('stock');

    if ($this->db->affected_rows() === 0) {
      // insert
      $data = [
        'product_id' => $product_id,
        'batch_no'   => $batch_no,
        'qty'        => $qty_delta,
        'last_cost'  => $last_cost,
        'updated_at' => date('Y-m-d H:i:s')
      ];
      return $this->db->insert('stock', $data);
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
    return $this->db->get_where('purchases', ['id' => (int)$id])->row_array();
  }

  public function datatable_purchases($start, $length, $search, $order_by, $order_dir)
  {
    $table = 'purchases';
    $total = $this->db->count_all($table);

    $this->db->from($table);
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
    return $this->db->get_where('purchase_returns', ['id' => (int)$id])->row_array();
  }

  public function datatable_purchase_returns($start, $length, $search, $order_by, $order_dir)
  {
    $table = 'purchase_returns';
    $total = $this->db->count_all($table);

    $this->db->from($table);
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
    return $this->db->get_where('sales', ['id' => (int)$id])->row_array();
  }

  public function datatable_sales($start, $length, $search, $order_by, $order_dir)
  {
    $table = 'sales';
    $total = $this->db->count_all($table);

    $this->db->from($table);
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
    return $this->db->get_where('sales_returns', ['id' => (int)$id])->row_array();
  }

  public function datatable_sales_returns($start, $length, $search, $order_by, $order_dir)
  {
    $table = 'sales_returns';
    $total = $this->db->count_all($table);

    $this->db->from($table);
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
public function insert_payment($data) {
  $this->db->insert('payments', $data);
  return $this->db->insert_id();
}

public function update_payment_by_id($id, $data) {
  $this->db->where('id', (int)$id);
  return $this->db->update('payments', $data);
}

public function get_payment($id) {
  return $this->db->get_where('payments', ['id' => (int)$id])->row_array();
}

/**
 * Server-side list with basic search/order/pagination.
 * Searchable: ref_no, mode, amount, payment_date.
 * Party name is resolved outside (controller) to keep SQL simple.
 */
public function datatable_payments($start, $length, $search, $order_by, $order_dir) {
  $this->db->from('payments');

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
  $safeMap = ['ref_no','payment_date','type','mode','amount','id'];
  $order_by = in_array($order_by, $safeMap, true) ? $order_by : 'payment_date';
  $order_dir = strtolower($order_dir) === 'asc' ? 'asc' : 'desc';
  $this->db->order_by($order_by, $order_dir);

  // page slice
  if ($length > 0) $this->db->limit($length, $start);

  $rows = $this->db->get()->result_array();

  return ['total'=>$total, 'filtered'=>$filtered, 'rows'=>$rows];
}

// Resolve party label (helper): fetch name quickly
public function get_supplier_name($id) {
  $row = $this->db->select('name')->from('suppliers')->where('id', (int)$id)->get()->row_array();
  return $row['name'] ?? null;
}
public function get_customer_name($id) {
  $row = $this->db->select('name')->from('customers')->where('id', (int)$id)->get()->row_array();
  return $row['name'] ?? null;
}

// Ledger insert (generic)
public function insert_ledger($data) {
  $this->db->insert('ledger', $data);
  return $this->db->insert_id();
}

// On payment update, you may want to replace ledger row(s) for that payment:
public function delete_ledger_by_payment($payment_id) {
  $this->db->where(['ref_type'=>'payment','ref_id'=>(int)$payment_id])->delete('ledger');
  return $this->db->affected_rows();
}

}
