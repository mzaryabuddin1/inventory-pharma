<?php $pagename = "payments"; ?>
<?php require_once("layout/header.php") ?>
<?php require_once("layout/sidebar.php") ?>
<?php require_once("layout/navbar.php") ?>
<link href="<?= base_url() ?>theme/css/plugins/dataTables/datatables.min.css" rel="stylesheet" />

<div class="wrapper wrapper-content">
  <!-- ADD PAYMENT -->
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox">
        <div class="ibox-title">
          <button class="btn btn-primary collapse-link" type="button">Click To Add Payment</button>
          <div class="ibox-tools"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></div>
        </div>
        <div class="ibox-content">
          <form id="payAddForm">
            <div class="alert alert-danger d-none" id="payError"></div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Reference No</label>
              <div class="col-sm-4"><input type="text" name="ref_no" class="form-control" required></div>

              <label class="col-sm-2 col-form-label">Date</label>
              <div class="col-sm-4"><input type="datetime-local" name="payment_date" class="form-control" required></div>
            </div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Type</label>
              <div class="col-sm-4">
                <select name="type" id="type" class="form-control" required>
                  <option value="">Select</option>
                  <option value="customer">Customer (Receive)</option>
                  <option value="supplier">Supplier (Pay)</option>
                </select>
              </div>

              <label class="col-sm-2 col-form-label">Party</label>
              <div class="col-sm-4">
                <!-- You can replace these with AJAX-selects -->
                <select name="party_id" id="party_id" class="form-control" required>
                  <option value="1">Select party</option>
                  <!-- TODO: Populate by selected type -->
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Mode</label>
              <div class="col-sm-4">
                <select name="mode" id="mode" class="form-control" required>
                  <option value="">Select</option>
                  <option value="cash">Cash</option>
                  <option value="cheque">Cheque</option>
                </select>
              </div>

              <label class="col-sm-2 col-form-label">Amount</label>
              <div class="col-sm-4">
                <input type="number" step="0.01" min="0.01" name="amount" class="form-control" required>
              </div>
            </div>

            <div id="chequeFields" class="d-none">
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Cheque No</label>
                <div class="col-sm-4"><input type="text" name="cheque_no" class="form-control"></div>

                <label class="col-sm-2 col-form-label">Cheque Date</label>
                <div class="col-sm-4"><input type="date" name="cheque_date" class="form-control"></div>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Note</label>
              <div class="col-sm-10"><textarea name="note" class="form-control" rows="2"></textarea></div>
            </div>

            <div class="sk-spinner sk-spinner-wave d-none" id="paySpinner">
              <div class="sk-rect1"></div><div class="sk-rect2"></div><div class="sk-rect3"></div><div class="sk-rect4"></div><div class="sk-rect5"></div>
            </div>

            <div class="form-group row">
              <div class="col-sm-4 col-sm-offset-2">
                <button type="reset" class="btn btn-white" id="payResetBtn">Reset</button>
                <button type="submit" class="btn btn-primary">Save Payment</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- LIST -->
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox" id="iboxPay">
        <div class="ibox-title">
          <h5>Payments</h5>
          <div class="ibox-tools"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></div>
        </div>
        <div class="ibox-content">
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" id="paymentsTable">
              <thead>
                <tr>
                  <th>Ref No</th>
                  <th>Date</th>
                  <th>Type</th>
                  <th>Party</th>
                  <th>Mode</th>
                  <th>Amount</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- EDIT MODAL -->
  <div class="modal fade" id="editPaymentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form id="payEditForm" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Payment</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger d-none" id="payEditError"></div>
          <input type="hidden" name="payment_id" id="edit_payment_id">

          <div class="form-group row">
            <label class="col-sm-4 col-form-label">Reference No</label>
            <div class="col-sm-8"><input type="text" name="ref_no" id="edit_ref_no" class="form-control" required></div>
          </div>

          <div class="form-group row">
            <label class="col-sm-4 col-form-label">Date</label>
            <div class="col-sm-8"><input type="datetime-local" name="payment_date" id="edit_payment_date" class="form-control" required></div>
          </div>

          <div class="form-group row">
            <label class="col-sm-4 col-form-label">Type</label>
            <div class="col-sm-8">
              <select name="type" id="edit_type" class="form-control" required>
                <option value="customer">Customer (Receive)</option>
                <option value="supplier">Supplier (Pay)</option>
              </select>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-4 col-form-label">Party</label>
            <div class="col-sm-8">
              <select name="party_id" id="edit_party_id" class="form-control" required>
                <option value="">Select party</option>
                <!-- TODO: populate based on type -->
              </select>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-4 col-form-label">Mode</label>
            <div class="col-sm-8">
              <select name="mode" id="edit_mode" class="form-control" required>
                <option value="cash">Cash</option>
                <option value="cheque">Cheque</option>
              </select>
            </div>
          </div>

          <div id="editChequeFields" class="d-none">
            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Cheque No</label>
              <div class="col-sm-8"><input type="text" name="cheque_no" id="edit_cheque_no" class="form-control"></div>
            </div>
            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Cheque Date</label>
              <div class="col-sm-8"><input type="date" name="cheque_date" id="edit_cheque_date" class="form-control"></div>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-4 col-form-label">Amount</label>
            <div class="col-sm-8"><input type="number" step="0.01" min="0.01" name="amount" id="edit_amount" class="form-control" required></div>
          </div>

          <div class="form-group row">
            <label class="col-sm-4 col-form-label">Note</label>
            <div class="col-sm-8"><textarea name="note" id="edit_note" class="form-control" rows="2"></textarea></div>
          </div>
        </div>
        <div class="modal-footer">
          <div class="sk-spinner sk-spinner-wave d-none" id="payEditSpinner">
            <div class="sk-rect1"></div><div class="sk-rect2"></div><div class="sk-rect3"></div><div class="sk-rect4"></div><div class="sk-rect5"></div>
          </div>
          <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="payEditSubmitBtn">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once("layout/footer.php") ?>
<script src="<?= base_url() ?>theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?= base_url() ?>theme/js/plugins/dataTables/dataTables.bootstrap4.min.js"></script>

<script>
  var paymentsDT;

  // Toggle cheque fields
  function toggleCheque(mode, $wrap) {
    if (mode === 'cheque') $wrap.removeClass('d-none'); else $wrap.addClass('d-none');
  }
  $("#mode").on('change', function(){ toggleCheque(this.value, $("#chequeFields")); });
  $("#edit_mode").on('change', function(){ toggleCheque(this.value, $("#editChequeFields")); });

  // TODO: optionally load parties based on type via AJAX
  // Example stub:
  // $("#type").on('change', function(){ loadParties(this.value, $("#party_id")); });
  // $("#edit_type").on('change', function(){ loadParties(this.value, $("#edit_party_id")); });

  // Add submit
  $("#payAddForm").on("submit", function(e){
    e.preventDefault();
    const fd = new FormData(this);

    $.ajax({
      url: "<?= base_url() . 'add-payment-submit'; ?>",
      type: "POST",
      data: fd,
      processData: false,
      contentType: false,
      cache: false,
      beforeSend: function(){
        $("#paySpinner").removeClass("d-none");
        $("#payError").addClass("d-none").empty();
        $("#payAddForm :submit").prop("disabled", true).addClass("d-none");
      },
      success: function(res){
        $("#paySpinner").addClass("d-none");
        $("#payAddForm :submit").prop("disabled", false).removeClass("d-none");
        let obj={}; try{ obj=JSON.parse(res);}catch(e){}
        if(obj.error){
          $("#payError").html(obj.error).removeClass("d-none");
          toastr.error("Please check errors list!","Error");
          return;
        }
        if(obj.success){
          toastr.success("Payment saved","Success");
          $("#payResetBtn").trigger("click");
          if (paymentsDT) paymentsDT.ajax.reload(null,false);
          return;
        }
        toastr.error("Unexpected response","Error");
      },
      error: function(){
        $("#paySpinner").addClass("d-none");
        $("#payAddForm :submit").prop("disabled", false).removeClass("d-none");
        toastr.error("Error while sending request!","Error");
      }
    });
  });

  // DataTable
  $(document).ready(function(){
    paymentsDT = $("#paymentsTable").DataTable({
      processing:true, serverSide:true, pageLength:25, responsive:true, searchDelay:500,
      ajax:{ url:"<?= base_url() . 'payments-list'; ?>", type:"POST" },
      columns:[
        { data:"ref_no", title:"Ref No" },
        { data:"payment_date", title:"Date" },
        { data:"type", title:"Type",
          render: t => t==='customer' ? 'Customer (Receive)' : 'Supplier (Pay)' },
        { data:"party_label", title:"Party", defaultContent: "" },
        { data:"mode", title:"Mode", render: m => m ? m.toUpperCase() : "" },
        { data:"amount", title:"Amount" },
        { data:"id", orderable:false, searchable:false, render: id => `
          <div class="btn-group btn-group-sm">
            <button class="btn btn-warning btn-edit-pay" data-id="${id}">Edit</button>
          </div>`
        }
      ],
      order:[[1,"desc"]]
    });
  });

  // Open edit modal
  $(document).on('click', '.btn-edit-pay', function(){
    const id=$(this).data('id');
    $("#payEditError").addClass('d-none').empty();

    $.ajax({
      url:"<?= base_url() . 'payment/'; ?>"+id,
      type:"GET",
      beforeSend:function(){ $("#payEditSpinner").removeClass('d-none'); $('#iboxPay .ibox-content').addClass('sk-loading'); },
      success:function(res){
        $("#payEditSpinner").addClass('d-none'); $('#iboxPay .ibox-content').removeClass('sk-loading');
        let obj={}; try{ obj=JSON.parse(res);}catch(e){}
        if(obj.error){ toastr.error(obj.error.replace(/<[^>]*>?/gm,''),"Error"); return; }
        const r=obj.data;

        $("#edit_payment_id").val(r.id);
        $("#edit_ref_no").val(r.ref_no);
        $("#edit_payment_date").val(r.payment_date.replace(' ','T'));
        $("#edit_type").val(r.type);
        // TODO: load parties based on r.type then set:
        // $("#edit_party_id").val(r.party_id);
        $("#edit_mode").val(r.mode).trigger('change');
        $("#edit_cheque_no").val(r.cheque_no || '');
        $("#edit_cheque_date").val(r.cheque_date || '');
        $("#edit_amount").val(r.amount);
        $("#edit_note").val(r.note || '');

        $("#editPaymentModal").modal('show');
      },
      error:function(){
        $("#payEditSpinner").addClass('d-none'); $('#iboxPay .ibox-content').removeClass('sk-loading');
        toastr.error("Failed to load payment","Error");
      }
    });
  });

  // Submit edit
  $("#payEditForm").on("submit", function(e){
    e.preventDefault();
    const fd = new FormData(this);

    $.ajax({
      url:"<?= base_url() . 'payment-update'; ?>",
      type:"POST", data:fd, processData:false, contentType:false, cache:false,
      beforeSend:function(){ $("#payEditSubmitBtn").prop("disabled",true); $("#payEditSpinner").removeClass('d-none'); },
      success:function(res){
        $("#payEditSpinner").addClass('d-none'); $("#payEditSubmitBtn").prop("disabled",false);
        let obj={}; try{ obj=JSON.parse(res);}catch(e){}
        if(obj.error){
          $("#payEditError").html(obj.error).removeClass('d-none');
          toastr.error("Please check errors list!","Error");
          return;
        }
        if(obj.success){
          toastr.success("Payment updated","Success");
          $("#editPaymentModal").modal('hide');
          if (paymentsDT) paymentsDT.ajax.reload(null,false);
          return;
        }
        toastr.error("Unexpected response","Error");
      },
      error:function(){
        $("#payEditSpinner").addClass('d-none'); $("#payEditSubmitBtn").prop("disabled",false);
        toastr.error("Error while sending request!","Error");
      }
    });
  });
</script>
