<?php $pagename = "customer"; ?>
<?php require_once("layout/header.php") ?>
<?php require_once("layout/sidebar.php") ?>
<?php require_once("layout/navbar.php") ?>
<link href="<?= base_url() ?>theme/css/plugins/dataTables/datatables.min.css" rel="stylesheet" />

<div class="wrapper wrapper-content">
  <!-- ADD CUSTOMER -->
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox">
        <div class="ibox-title">
          <button class="btn btn-primary collapse-link" type="button">Click To Add Customer</button>
          <div class="ibox-tools"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></div>
        </div>
        <div class="ibox-content">
          <form method="post" id="customerAddForm" enctype="multipart/form-data">
            <div class="alert alert-danger d-none" id="custError"></div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Name</label>
              <div class="col-sm-10"><input type="text" name="name" class="form-control" required></div>
            </div>
            <div class="hr-line-dashed"></div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Email</label>
              <div class="col-sm-10"><input type="email" name="email" class="form-control"></div>
            </div>
            <div class="hr-line-dashed"></div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Phone</label>
              <div class="col-sm-10"><input type="text" name="phone" class="form-control"></div>
            </div>
            <div class="hr-line-dashed"></div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Address</label>
              <div class="col-sm-10"><textarea name="address" class="form-control" rows="3"></textarea></div>
            </div>
            <div class="hr-line-dashed"></div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Avatar</label>
              <div class="col-sm-10"><input type="file" name="avatar" class="form-control"></div>
            </div>

            <div class="sk-spinner sk-spinner-wave d-none" id="custSpinner">
              <div class="sk-rect1"></div>
              <div class="sk-rect2"></div>
              <div class="sk-rect3"></div>
              <div class="sk-rect4"></div>
              <div class="sk-rect5"></div>
            </div>

            <div class="form-group row">
              <div class="col-sm-4 col-sm-offset-2">
                <button type="reset" class="btn btn-white" id="custResetBtn">Reset</button>
                <button type="submit" class="btn btn-primary">Save Customer</button>
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
      <div class="ibox" id="iboxCust">
        <div class="ibox-title">
          <h5>Customers</h5>
          <div class="ibox-tools"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></div>
        </div>
        <div class="ibox-content">
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" id="customersTable">
              <thead>
                <tr>
                  <th>Avatar</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>Address</th>
                  <th>Status</th>
                  <th>Created</th>
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
  <div class="modal fade" id="editCustomerModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <form id="customerEditForm" enctype="multipart/form-data" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit customer</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
        </div>

        <div class="modal-body">
          <div class="alert alert-danger d-none" id="custEditError"></div>
          <input type="hidden" name="customer_id" id="edit_customer_id">

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">Name</label>
            <div class="col-sm-9"><input type="text" name="name" id="edit_name" class="form-control" required></div>
          </div>
          <div class="hr-line-dashed"></div>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">Email</label>
            <div class="col-sm-9"><input type="email" name="email" id="edit_email" class="form-control"></div>
          </div>
          <div class="hr-line-dashed"></div>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">Phone</label>
            <div class="col-sm-9"><input type="text" name="phone" id="edit_phone" class="form-control"></div>
          </div>
          <div class="hr-line-dashed"></div>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">Address</label>
            <div class="col-sm-9"><textarea name="address" id="edit_address" class="form-control" rows="3"></textarea></div>
          </div>
          <div class="hr-line-dashed"></div>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">Avatar</label>
            <div class="col-sm-9">
              <div id="edit_avatarPreview" class="mb-2"></div>
              <input type="file" name="avatar" id="edit_avatar" class="form-control">
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <div class="sk-spinner sk-spinner-wave d-none" id="custEditSpinner">
            <div class="sk-rect1"></div>
            <div class="sk-rect2"></div>
            <div class="sk-rect3"></div>
            <div class="sk-rect4"></div>
            <div class="sk-rect5"></div>
          </div>
          <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="custEditSubmitBtn">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once("layout/footer.php") ?>
<script src="<?= base_url() ?>theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?= base_url() ?>theme/js/plugins/dataTables/dataTables.bootstrap4.min.js"></script>

<script>
  var customersDT;

  $(document).ready(function () {
    customersDT = $("#customersTable").DataTable({
      processing: true,
      serverSide: true,
      pageLength: 25,
      responsive: true,
      searchDelay: 500,
      dom: '<"html5buttons"B>lTfgitp',
      buttons: [
        { extend: "copy" },
        { extend: "csv" },
        { extend: "excel", title: "Customers" },
        { extend: "pdf", title: "Customers" },
        {
          extend: "print",
          customize: function (win) {
            $(win.document.body).addClass("white-bg").css("font-size", "10px");
            $(win.document.body).find("table").addClass("compact").css("font-size", "inherit");
          },
        },
      ],
      ajax: { url: "<?= base_url() . 'customers-list'; ?>", type: "POST" },
      columns: [
        {
          data: "avatar",
          orderable: false,
          searchable: false,
          render: function(d){ return d ? `<img src="${d}" style="height:40px">` : ""; }
        },
        { data: "name",    title: "Name" },
        { data: "email",   title: "Email" },
        { data: "phone",   title: "Phone" },
        { data: "address", title: "Address" },
        {
          data: "status",  title: "Status",
          render: function(s){
            return (Number(s)===1)
              ? '<span class="badge badge-success">Active</span>'
              : '<span class="badge badge-secondary">Inactive</span>';
          }
        },
        { data: "created_at", title: "Created" },
        {
          data: "id", orderable:false, searchable:false,
          render: function(id, t, row){
            return `<div class="btn-group btn-group-sm">
                      <button type="button" class="btn btn-warning btn-edit-cust" data-id="${id}">Edit</button>
                    </div>`;
          }
        }
      ],
      order: [[1, "asc"]]
    });
  });

  function reloadCustomersDT(){ if (customersDT) customersDT.ajax.reload(null, false); }
</script>

<script>
  // ADD customer submit
  $("#customerAddForm").on("submit", function(e){
    e.preventDefault();
    const fd = new FormData(this);

    $.ajax({
      url: "<?= base_url() . 'add-customer-submit'; ?>",
      type: "POST",
      data: fd,
      processData: false,
      contentType: false,
      cache: false,
      beforeSend: function(){
        $(":submit", "#customerAddForm").prop("disabled", true).addClass("d-none");
        $("#custSpinner").removeClass("d-none");
        $("#custError").addClass("d-none").empty();
      },
      success: function(res){
        $("#custSpinner").addClass("d-none");
        $(":submit", "#customerAddForm").prop("disabled", false).removeClass("d-none");

        let obj={}; try{ obj=JSON.parse(res);}catch(e){}
        if (obj.error){
          $("#custError").html(obj.error).removeClass("d-none");
          toastr.error("Please check errors list!", "Error");
          return;
        }
        if (obj.success){
          toastr.success("Customer Saved", "Success");
          $("#custResetBtn").trigger("click");
          reloadCustomersDT();
          return;
        }
        toastr.error("Unexpected response", "Error");
      },
      error: function(){
        $("#custSpinner").addClass("d-none");
        $(":submit", "#customerAddForm").prop("disabled", false).removeClass("d-none");
        toastr.error("Error while sending request!", "Error");
      }
    });
  });

  // open Edit modal
  $(document).on('click', '.btn-edit-cust', function(){
    const id = $(this).data('id');
    $("#custEditError").addClass('d-none').empty();
    $("#customerEditForm")[0].reset();
    $("#edit_avatarPreview").empty();

    $.ajax({
      url: "<?= base_url() . 'customer/'; ?>" + id,
      type: "GET",
      beforeSend: function(){
        $("#custEditSpinner").removeClass('d-none');
        $('#iboxCust').children(".ibox-content").addClass("sk-loading");
      },
      success: function(res){
        $("#custEditSpinner").addClass('d-none');
        $('#iboxCust').children(".ibox-content").removeClass("sk-loading");

        let obj={}; try{ obj=JSON.parse(res);}catch(e){}
        if (obj.error){ toastr.error(obj.error.replace(/<[^>]*>?/gm,''),"Error"); return; }

        const c = obj.data;
        $('#edit_customer_id').val(c.id);
        $('#edit_name').val(c.name);
        $('#edit_email').val(c.email||'');
        $('#edit_phone').val(c.phone||'');
        $('#edit_address').val(c.address||'');
        if (c.avatar) $('#edit_avatarPreview').html(`<img src="${c.avatar}" style="height:60px">`);

        $('#editCustomerModal').modal('show');
      },
      error: function(){
        $("#custEditSpinner").addClass('d-none');
        $('#iboxCust').children(".ibox-content").removeClass("sk-loading");
        toastr.error("Failed to load customer","Error");
      }
    });
  });

  // submit Edit
  $("#customerEditForm").on("submit", function(e){
    e.preventDefault();
    const fd = new FormData(this);

    $.ajax({
      url: "<?= base_url() . 'customer-update'; ?>",
      type: "POST",
      data: fd,
      processData: false,
      contentType: false,
      cache: false,
      beforeSend: function(){
        $("#custEditSubmitBtn").prop("disabled", true);
        $("#custEditSpinner").removeClass('d-none');
        $("#custEditError").addClass('d-none').empty();
      },
      success: function(res){
        $("#custEditSpinner").addClass('d-none');
        $("#custEditSubmitBtn").prop("disabled", false);

        let obj={}; try{ obj=JSON.parse(res);}catch(e){}
        if (obj.error){
          $("#custEditError").html(obj.error).removeClass('d-none');
          toastr.error("Please check errors list!","Error");
          return;
        }
        if (obj.success){
          toastr.success("Customer updated","Success");
          $('#editCustomerModal').modal('hide');
          reloadCustomersDT();
          return;
        }
        toastr.error("Unexpected response","Error");
      },
      error: function(){
        $("#custEditSpinner").addClass('d-none');
        $("#custEditSubmitBtn").prop("disabled", false);
        toastr.error("Error while sending request!","Error");
      }
    });
  });
</script>
