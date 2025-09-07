<?php $pagetab = "report"; ?>
<?php $pagename = "ledger_report"; ?>
<?php require_once("layout/header.php") ?>
<?php require_once("layout/sidebar.php") ?>
<?php require_once("layout/navbar.php") ?>
<link href="<?= base_url() ?>theme/css/plugins/dataTables/datatables.min.css" rel="stylesheet" />

<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox">
        <div class="ibox-title">
          <h5>Ledger Report</h5>
          <div class="ibox-tools"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></div>
        </div>

        <div class="ibox-content">
          <!-- Filters -->
          <form id="filters" class="mb-3">
            <div class="row">
              <div class="col-md-3 mb-2">
                <label>Party Type</label>
                <select class="form-control" name="party_type" id="party_type">
                  <option value="">All</option>
                  <option value="customer">Customer</option>
                  <option value="supplier">Supplier</option>
                </select>
              </div>
              <div class="col-md-3 mb-2">
                <label>Party</label>
                <select class="form-control" name="party_id" id="party_id">
                  <option value="">All</option>
                  <!-- dynamically refilled by JS depending on type -->
                </select>
              </div>
              <div class="col-md-3 mb-2">
                <label>Ref Type</label>
                <select class="form-control" name="ref_type">
                  <option value="">All</option>
                  <option value="purchase">Purchase</option>
                  <option value="purchase_return">Purchase Return</option>
                  <option value="sales">Sales</option>
                  <option value="sales_return">Sales Return</option>
                  <option value="payment">Payment</option>
                </select>
              </div>
              <div class="col-md-3 mb-2">
                <label>Date From</label>
                <input type="date" class="form-control" name="date_from">
              </div>
            </div>
            <div class="row mt-2">
              <div class="col-md-3 mb-2">
                <label>Date To</label>
                <input type="date" class="form-control" name="date_to">
              </div>
              <div class="col-md-9 d-flex align-items-end mb-2">
                <button type="button" id="btnFilter" class="btn btn-primary mr-2">Apply</button>
                <button type="button" id="btnReset" class="btn btn-white">Reset</button>
              </div>
            </div>
          </form>

          <div class="mb-2">
            <strong>Opening Balance:</strong> <span id="openingBal">0.00</span> &nbsp; |
            <strong>Total Debit:</strong> <span id="sumDebit">0.00</span> &nbsp; |
            <strong>Total Credit:</strong> <span id="sumCredit">0.00</span> &nbsp; |
            <strong>Closing Balance:</strong> <span id="closingBal">0.00</span>
          </div>

          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" id="ledgerTable">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Ref Type</th>
                  <th>Ref #</th>
                  <th>Description</th>
                  <th>Debit</th>
                  <th>Credit</th>
                  <th>Balance</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once("layout/footer.php") ?>
<script src="<?= base_url() ?>theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?= base_url() ?>theme/js/plugins/dataTables/dataTables.bootstrap4.min.js"></script>

<script>
// preload lists from PHP for party selector
const SUPPLIERS = <?= json_encode($suppliers ?? []) ?>;
const CUSTOMERS = <?= json_encode($customers ?? []) ?>;

function fillPartyOptions(type) {
  const $sel = $('#party_id');
  $sel.empty().append('<option value="">All</option>');
  const list = type === 'supplier' ? SUPPLIERS : (type === 'customer' ? CUSTOMERS : []);
  for (const it of list) {
    $sel.append(`<option value="${it.id}">${it.name}</option>`);
  }
}

$('#party_type').on('change', function(){
  fillPartyOptions(this.value);
});

function currentFilters() {
  const f = $('#filters').serializeArray(), data = {};
  for (const {name,value} of f) data[name] = value;
  return data;
}

let ledgerDT;

$(function(){
  // default: no party list until type chosen (optional)
  fillPartyOptions('');

  ledgerDT = $('#ledgerTable').DataTable({
    processing: true,
    serverSide: true,
    pageLength: 25,
    responsive: true,
    order: [[0,'asc']], // entry_date asc to see running balance properly
    ajax: {
      url: "<?= base_url() . 'ledger-report-data'; ?>",
      type: "POST",
      data: function(d){ return Object.assign(d, currentFilters()); },
      dataSrc: function(json){
        // update header summary
        $('#openingBal').text(parseFloat(json.opening||0).toFixed(2));
        $('#sumDebit').text(parseFloat(json.sum_debit||0).toFixed(2));
        $('#sumCredit').text(parseFloat(json.sum_credit||0).toFixed(2));
        $('#closingBal').text(parseFloat(json.closing||0).toFixed(2));
        return json.data || [];
      }
    },
    columns: [
      { data: 'entry_date', title: 'Date' },
      { data: 'ref_type',   title: 'Ref Type' },
      { data: 'ref_no',     title: 'Ref #' },
      { data: 'description',title: 'Description' },
      { data: 'debit',      title: 'Debit',  className:'text-right',
        render: d => parseFloat(d||0).toFixed(2) },
      { data: 'credit',     title: 'Credit', className:'text-right',
        render: d => parseFloat(d||0).toFixed(2) },
      { data: 'balance',    title: 'Balance',className:'text-right',
        render: d => parseFloat(d||0).toFixed(2) }
    ],
    dom: '<"html5buttons"B>lTfgitp',
    buttons: [
      { extend: "copy" },
      { extend: "csv",  title: "LedgerReport" },
      { extend: "excel",title: "LedgerReport" },
      { extend: "pdf",  title: "LedgerReport" },
      { extend: "print",
        customize: function(win){
          $(win.document.body).addClass("white-bg").css("font-size","10px");
          $(win.document.body).find("table").addClass("compact").css("font-size","inherit");
        }
      }
    ],
    searchDelay: 500
  });

  $('#btnFilter').on('click', function(){ ledgerDT.ajax.reload(); });
  $('#btnReset').on('click', function(){
    $('#filters')[0].reset();
    fillPartyOptions('');
    ledgerDT.ajax.reload();
  });
});
</script>
