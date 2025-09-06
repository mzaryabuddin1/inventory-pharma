<?php $pagename = "sales_return"; ?>
<?php require_once("layout/header.php") ?>
<?php require_once("layout/sidebar.php") ?>
<?php require_once("layout/navbar.php") ?>
<link href="<?= base_url() ?>theme/css/plugins/dataTables/datatables.min.css" rel="stylesheet" />

<div class="wrapper wrapper-content">
  <!-- ADD SALES RETURN -->
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox">
        <div class="ibox-title">
          <button class="btn btn-primary collapse-link" type="button">Click To Add Sales Return</button>
          <div class="ibox-tools"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></div>
        </div>
        <div class="ibox-content">
          <form id="srAddForm">
            <div class="alert alert-danger d-none" id="srError"></div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Reference No</label>
              <div class="col-sm-4"><input type="text" name="ref_no" class="form-control" required></div>

              <label class="col-sm-2 col-form-label">Return Date</label>
              <div class="col-sm-4"><input type="datetime-local" name="return_date" class="form-control" required></div>
            </div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Original Invoice</label>
              <div class="col-sm-4">
                <select name="sale_id" class="form-control" required>
                  <option value="1">Select invoice</option>
                     <?php foreach ($invoices as $invoice) : ?>
                      <option value="<?= $invoice['id'] ?>"><?= $invoice['invoice_no'] ?></option>  
                    <?php endforeach; ?>
                  <!-- TODO -->
                </select>
              </div>
              <label class="col-sm-2 col-form-label">Customer</label>
              <div class="col-sm-4">
                <select name="customer_id" class="form-control" required>
                  <option value="1">Select customer</option>
                  <?php foreach ($customers as $customer) : ?>
                    <option value="<?= $customer['id'] ?>"><?= $customer['name'] ?></option>  
                  <?php endforeach; ?>
                  <!-- TODO -->
                </select>
              </div>
            </div>

            <div class="hr-line-dashed"></div>

            <div class="form-group">
              <label>Items</label>
              <div id="sr-items-wrapper">
                <div class="row item-row align-items-center mb-2">
                  <div class="col-md-4">
                    <select name="product_id[]" class="form-control" required>
                      <option value="1">Select product</option>
                       <?php foreach ($products as $product) : ?>
                      <option value="<?= $product['id'] ?>"><?= $product['product_name'] ?></option>  
                    <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-2"><input type="text" name="batch_no[]" class="form-control" placeholder="Batch No" required></div>
                  <div class="col-md-2"><input type="number" name="qty[]" class="form-control" placeholder="Qty" min="1" required></div>
                  <div class="col-md-2"><input type="number" step="0.01" name="price[]" class="form-control" placeholder="Price" min="0" required></div>
                  <div class="col-md-2"><button type="button" class="btn btn-block btn-primary add-item">+</button></div>
                </div>
              </div>
              <template id="sr-item-row-tpl">
                <div class="row item-row align-items-center mb-2">
                  <div class="col-md-4">
                    <select name="product_id[]" class="form-control" required>
                      <option value="">Select product</option>
                            <?php foreach ($products as $product) : ?>
                      <option value="<?= $product['id'] ?>"><?= $product['product_name'] ?></option>  
                    <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-2"><input type="text" name="batch_no[]" class="form-control" placeholder="Batch No" required></div>
                  <div class="col-md-2"><input type="number" name="qty[]" class="form-control" placeholder="Qty" min="1" required></div>
                  <div class="col-md-2"><input type="number" step="0.01" name="price[]" class="form-control" placeholder="Price" min="0" required></div>
                  <div class="col-md-2"><button type="button" class="btn btn-block btn-outline-danger remove-item">−</button></div>
                </div>
              </template>
            </div>

            <div class="sk-spinner sk-spinner-wave d-none" id="srSpinner">
              <div class="sk-rect1"></div><div class="sk-rect2"></div><div class="sk-rect3"></div><div class="sk-rect4"></div><div class="sk-rect5"></div>
            </div>

            <div class="form-group row">
              <div class="col-sm-4 col-sm-offset-2">
                <button type="reset" class="btn btn-white" id="srResetBtn">Reset</button>
                <button type="submit" class="btn btn-primary">Save Sales Return</button>
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
      <div class="ibox" id="iboxSR">
        <div class="ibox-title">
          <h5>Sales Returns</h5>
          <div class="ibox-tools"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></div>
        </div>
        <div class="ibox-content">
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" id="srTable">
              <thead>
                <tr>
                  <th>Ref No</th>
                  <th>Date</th>
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
  <div class="modal fade" id="editSRModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <form id="srEditForm" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Sales Return</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger d-none" id="srEditError"></div>
          <input type="hidden" name="sales_return_id" id="edit_sr_id">

          <div class="form-group row">
            <label class="col-sm-2 col-form-label">Reference No</label>
            <div class="col-sm-4"><input type="text" name="ref_no" id="edit_sr_ref_no" class="form-control" required></div>

            <label class="col-sm-2 col-form-label">Return Date</label>
            <div class="col-sm-4"><input type="datetime-local" name="return_date" id="edit_sr_date" class="form-control" required></div>
          </div>

          <div class="form-group row">
            <label class="col-sm-2 col-form-label">Original Invoice</label>
            <div class="col-sm-4">
              <select name="sale_id" id="edit_sr_sale_id" class="form-control" required>
                <option value="">Select invoice</option>
                 <?php foreach ($invoices as $invoice) : ?>
                      <option value="<?= $invoice['id'] ?>"><?= $invoice['invoice_no'] ?></option>  
                    <?php endforeach; ?>
              </select>
            </div>
            <label class="col-sm-2 col-form-label">Customer</label>
            <div class="col-sm-4">
              <select name="customer_id" id="edit_sr_customer_id" class="form-control" required>
                <option value="">Select customer</option>
                  <?php foreach ($customers as $customer) : ?>
                    <option value="<?= $customer['id'] ?>"><?= $customer['name'] ?></option>  
                  <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="hr-line-dashed"></div>

          <div class="form-group">
            <label>Items</label>
            <div id="sr-edit-items-wrapper"></div>
            <template id="sr-edit-item-row-tpl">
              <div class="row item-row align-items-center mb-2">
                <div class="col-md-4">
                  <select name="product_id[]" class="form-control" required>
                    <option value="">Select product</option>
                          <?php foreach ($products as $product) : ?>
                      <option value="<?= $product['id'] ?>"><?= $product['product_name'] ?></option>  
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-2"><input type="text" name="batch_no[]" class="form-control" placeholder="Batch No" required></div>
                <div class="col-md-2"><input type="number" name="qty[]" class="form-control" placeholder="Qty" min="1" required></div>
                <div class="col-md-2"><input type="number" step="0.01" name="price[]" class="form-control" placeholder="Price" min="0" required></div>
                <div class="col-md-2"><button type="button" class="btn btn-block btn-outline-danger remove-item">−</button></div>
              </div>
            </template>
            <button type="button" class="btn btn-sm btn-primary mt-2" id="srEditAddItem">+ Add Row</button>
          </div>
        </div>
        <div class="modal-footer">
          <div class="sk-spinner sk-spinner-wave d-none" id="srEditSpinner">
            <div class="sk-rect1"></div><div class="sk-rect2"></div><div class="sk-rect3"></div><div class="sk-rect4"></div><div class="sk-rect5"></div>
          </div>
          <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="srEditSubmitBtn">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once("layout/footer.php") ?>
<script src="<?= base_url() ?>theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?= base_url() ?>theme/js/plugins/dataTables/dataTables.bootstrap4.min.js"></script>

<script>
  var srDT;

  // add/remove rows (Add form)
  $(document).on('click', '#sr-items-wrapper .add-item', function(){
    const tpl=document.getElementById('sr-item-row-tpl').content.cloneNode(true);
    $("#sr-items-wrapper").append(tpl);
  });
  $(document).on('click', '#sr-items-wrapper .remove-item', function(){
    $(this).closest('.item-row').remove();
  });

  // submit Add
  $("#srAddForm").on("submit", function(e){
    e.preventDefault();
    const items=[];
    $("#sr-items-wrapper .item-row").each(function(){
      const product_id=$(this).find('[name="product_id[]"]').val();
      const batch_no=$(this).find('[name="batch_no[]"]').val();
      const qty=$(this).find('[name="qty[]"]').val();
      const price=$(this).find('[name="price[]"]').val();
      if(product_id && batch_no && qty){
        items.push({product_id:parseInt(product_id,10),batch_no,qty:parseInt(qty,10),price:parseFloat(price||0)});
      }
    });
    const fd=new FormData(this);
    fd.append('items', JSON.stringify(items));

    $.ajax({
      url:"<?= base_url() . 'add-sales-return-submit'; ?>",
      type:"POST", data:fd, processData:false, contentType:false, cache:false,
      beforeSend:function(){
        $("#srSpinner").removeClass("d-none");
        $("#srError").addClass("d-none").empty();
        $("#srAddForm :submit").prop("disabled", true).addClass("d-none");
      },
      success:function(res){
        $("#srSpinner").addClass("d-none");
        $("#srAddForm :submit").prop("disabled", false).removeClass("d-none");
        let obj={}; try{ obj=JSON.parse(res);}catch(e){}
        if(obj.error){
          $("#srError").html(obj.error).removeClass("d-none");
          toastr.error("Please check errors list!","Error");
          return;
        }
        if(obj.success){
          toastr.success("Sales return saved","Success");
          $("#srResetBtn").trigger("click");
          if (srDT) srDT.ajax.reload(null,false);
          return;
        }
        toastr.error("Unexpected response","Error");
      },
      error:function(){
        $("#srSpinner").addClass("d-none");
        $("#srAddForm :submit").prop("disabled", false).removeClass("d-none");
        toastr.error("Error while sending request!","Error");
      }
    });
  });

  // DataTable
  $(document).ready(function(){
    srDT = $("#srTable").DataTable({
      processing:true, serverSide:true, pageLength:25, responsive:true, searchDelay:500,
      ajax:{ url:"<?= base_url() . 'sales-returns-list'; ?>", type:"POST" },
      columns:[
        { data:"ref_no", title:"Ref No" },
        { data:"return_date", title:"Date" },
        { data:"return_amount", title:"Amount" },
        { data:"id", orderable:false, searchable:false, render: id => `
          <div class="btn-group btn-group-sm">
            <button class="btn btn-warning btn-edit-sr" data-id="${id}">Edit</button>
          </div>`
        }
      ],
      order:[[1,"desc"]]
    });
  });

  // load edit modal
  $(document).on('click', '.btn-edit-sr', function(){
    const id=$(this).data('id');
    $("#srEditError").addClass('d-none').empty();
    $("#sr-edit-items-wrapper").empty();

    $.ajax({
      url:"<?= base_url() . 'sales-return/'; ?>"+id,
      type:"GET",
      beforeSend:function(){
        $("#srEditSpinner").removeClass('d-none');
        $('#iboxSR .ibox-content').addClass('sk-loading');
      },
      success:function(res){
        $("#srEditSpinner").addClass('d-none');
        $('#iboxSR .ibox-content').removeClass('sk-loading');
        let obj={}; try{ obj=JSON.parse(res);}catch(e){}
        if(obj.error){ toastr.error(obj.error.replace(/<[^>]*>?/gm,''),"Error"); return; }

        const r=obj.data;
        $("#edit_sr_id").val(r.id);
        $("#edit_sr_ref_no").val(r.ref_no);
        $("#edit_sr_date").val(r.return_date.replace(' ','T'));
        $("#edit_sr_sale_id").val(r.sale_id);
        $("#edit_sr_customer_id").val(r.customer_id);

        const items = Array.isArray(r.items)? r.items : [];
        if(!items.length){
          const tpl=document.getElementById('sr-edit-item-row-tpl').content.cloneNode(true);
          $("#sr-edit-items-wrapper").append(tpl);
        }else{
          items.forEach(it=>{
            const tpl=document.getElementById('sr-edit-item-row-tpl').content.cloneNode(true);
            $(tpl).find('[name="product_id[]"]').val(it.product_id);
            $(tpl).find('[name="batch_no[]"]').val(it.batch_no);
            $(tpl).find('[name="qty[]"]').val(it.qty);
            $(tpl).find('[name="price[]"]').val(it.price);
            $("#sr-edit-items-wrapper").append(tpl);
          });
        }

        $("#editSRModal").modal('show');
      },
      error:function(){
        $("#srEditSpinner").addClass('d-none');
        $('#iboxSR .ibox-content').removeClass('sk-loading');
        toastr.error("Failed to load sales return","Error");
      }
    });
  });

  // add/remove in edit modal
  $("#srEditAddItem").on('click', function(){
    const tpl=document.getElementById('sr-edit-item-row-tpl').content.cloneNode(true);
    $("#sr-edit-items-wrapper").append(tpl);
  });
  $(document).on('click', '#sr-edit-items-wrapper .remove-item', function(){
    $(this).closest('.item-row').remove();
  });

  // submit edit
  $("#srEditForm").on("submit", function(e){
    e.preventDefault();
    const items=[];
    $("#sr-edit-items-wrapper .item-row").each(function(){
      const product_id=$(this).find('[name="product_id[]"]').val();
      const batch_no=$(this).find('[name="batch_no[]"]').val();
      const qty=$(this).find('[name="qty[]"]').val();
      const price=$(this).find('[name="price[]"]').val();
      if(product_id && batch_no && qty){
        items.push({product_id:parseInt(product_id,10),batch_no,qty:parseInt(qty,10),price:parseFloat(price||0)});
      }
    });
    const fd=new FormData(this);
    fd.append('items', JSON.stringify(items));

    $.ajax({
      url:"<?= base_url() . 'sales-return-update'; ?>",
      type:"POST", data:fd, processData:false, contentType:false, cache:false,
      beforeSend:function(){ $("#srEditSubmitBtn").prop("disabled",true); $("#srEditSpinner").removeClass('d-none'); },
      success:function(res){
        $("#srEditSpinner").addClass('d-none'); $("#srEditSubmitBtn").prop("disabled",false);
        let obj={}; try{ obj=JSON.parse(res);}catch(e){}
        if(obj.error){
          $("#srEditError").html(obj.error).removeClass('d-none');
          toastr.error("Please check errors list!","Error");
          return;
        }
        if(obj.success){
          toastr.success("Sales return updated","Success");
          $("#editSRModal").modal('hide');
          if (srDT) srDT.ajax.reload(null,false);
          return;
        }
        toastr.error("Unexpected response","Error");
      },
      error:function(){
        $("#srEditSpinner").addClass('d-none'); $("#srEditSubmitBtn").prop("disabled",false);
        toastr.error("Error while sending request!","Error");
      }
    });
  });
</script>
