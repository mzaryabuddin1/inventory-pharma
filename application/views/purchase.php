<?php $pagename = "purchase"; ?>
<?php require_once("layout/header.php") ?>
<?php require_once("layout/sidebar.php") ?>
<?php require_once("layout/navbar.php") ?>
<link href="<?= base_url() ?>theme/css/plugins/dataTables/datatables.min.css" rel="stylesheet" />

<div class="wrapper wrapper-content">
  <!-- ADD PURCHASE -->
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox">
        <div class="ibox-title">
          <button class="btn btn-primary collapse-link" type="button">Click To Add Purchase</button>
          <div class="ibox-tools"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></div>
        </div>
        <div class="ibox-content">
          <form id="purchaseAddForm">
            <div class="alert alert-danger d-none" id="purError"></div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Reference No</label>
              <div class="col-sm-4"><input type="text" name="ref_no" class="form-control" required></div>

              <label class="col-sm-2 col-form-label">Date</label>
              <div class="col-sm-4"><input type="datetime-local" name="purchase_date" class="form-control" required></div>
            </div>
            <div class="hr-line-dashed"></div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Supplier</label>
              <div class="col-sm-10">
                <select name="supplier_id" class="form-control" required>
                  <option value="1">Select supplier</option>
                  <!-- TODO: populate with your suppliers -->
                    <?php foreach ($suppliers as $supplier) : ?>
                      <option value="<?= $supplier['id'] ?>"><?= $supplier['name'] ?></option>  
                    <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="hr-line-dashed"></div>

            <div class="form-group">
              <label>Items</label>
              <div id="pur-items-wrapper">
                <!-- first row with + -->
                <div class="row item-row align-items-center mb-2">
                  <div class="col-md-4">
                    <select name="product_id[]" class="form-control" required>
                      <option value="1">Select product</option>
                      <!-- TODO: populate products -->
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
              <template id="pur-item-row-tpl">
                <div class="row item-row align-items-center mb-2">
                  <div class="col-md-4">
                    <select name="product_id[]" class="form-control" required>
                      <option value="1">Select product</option>
                      <!-- TODO: populate products -->
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

            <div class="sk-spinner sk-spinner-wave d-none" id="purSpinner">
              <div class="sk-rect1"></div><div class="sk-rect2"></div><div class="sk-rect3"></div><div class="sk-rect4"></div><div class="sk-rect5"></div>
            </div>

            <div class="form-group row">
              <div class="col-sm-4 col-sm-offset-2">
                <button type="reset" class="btn btn-white" id="purResetBtn">Reset</button>
                <button type="submit" class="btn btn-primary">Save Purchase</button>
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
      <div class="ibox" id="iboxPur">
        <div class="ibox-title">
          <h5>Purchases</h5>
          <div class="ibox-tools"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></div>
        </div>
        <div class="ibox-content">
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" id="purchasesTable">
              <thead>
                <tr>
                  <th>Ref No</th>
                  <th>Date</th>
                  <th>Total</th>
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
  <div class="modal fade" id="editPurchaseModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <form id="purchaseEditForm" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Purchase</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger d-none" id="purEditError"></div>
          <input type="hidden" name="purchase_id" id="edit_purchase_id">

          <div class="form-group row">
            <label class="col-sm-2 col-form-label">Reference No</label>
            <div class="col-sm-4"><input type="text" name="ref_no" id="edit_ref_no" class="form-control" required></div>

            <label class="col-sm-2 col-form-label">Date</label>
            <div class="col-sm-4"><input type="datetime-local" name="purchase_date" id="edit_purchase_date" class="form-control" required></div>
          </div>
          <div class="hr-line-dashed"></div>

          <div class="form-group row">
            <label class="col-sm-2 col-form-label">Supplier</label>
            <div class="col-sm-10">
              <select name="supplier_id" id="edit_supplier_id" class="form-control" required>
                <option >Select supplier</option>  
                  <?php foreach ($suppliers as $supplier) : ?>
                    <option value="<?= $supplier['id'] ?>"><?= $supplier['name'] ?></option>  
                  <?php endforeach; ?>  
                <!-- TODO: populate -->
              </select>
            </div>
          </div>
          <div class="hr-line-dashed"></div>

          <div class="form-group">
            <label>Items</label>
            <div id="pur-edit-items-wrapper"></div>
            <template id="pur-edit-item-row-tpl">
              <div class="row item-row align-items-center mb-2">
                <div class="col-md-4">
                  <select name="product_id[]" class="form-control" required>
                    <option >Select product</option>
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
            <button type="button" class="btn btn-sm btn-primary mt-2" id="purEditAddItem">+ Add Row</button>
          </div>
        </div>
        <div class="modal-footer">
          <div class="sk-spinner sk-spinner-wave d-none" id="purEditSpinner">
            <div class="sk-rect1"></div><div class="sk-rect2"></div><div class="sk-rect3"></div><div class="sk-rect4"></div><div class="sk-rect5"></div>
          </div>
          <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="purEditSubmitBtn">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once("layout/footer.php") ?>
<script src="<?= base_url() ?>theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?= base_url() ?>theme/js/plugins/dataTables/dataTables.bootstrap4.min.js"></script>

<script>
  var purchasesDT;

  // Add row in Add form
  $(document).on('click', '.add-item', function(){
    const tpl = document.getElementById('pur-item-row-tpl').content.cloneNode(true);
    $('#pur-items-wrapper').append(tpl);
  });
  $(document).on('click', '#pur-items-wrapper .remove-item', function(){
    $(this).closest('.item-row').remove();
  });

  // Submit Add form
  $("#purchaseAddForm").on("submit", function(e){
    e.preventDefault();
    const items = [];
    $("#pur-items-wrapper .item-row").each(function(){
      const product_id = $(this).find('[name="product_id[]"]').val();
      const batch_no   = $(this).find('[name="batch_no[]"]').val();
      const qty        = $(this).find('[name="qty[]"]').val();
      const price      = $(this).find('[name="price[]"]').val();
      if (product_id && batch_no && qty) {
        items.push({ product_id: parseInt(product_id,10), batch_no, qty: parseInt(qty,10), price: parseFloat(price||0) });
      }
    });

    const fd = new FormData(this);
    fd.append('items', JSON.stringify(items));

    $.ajax({
      url: "<?= base_url() . 'add-purchase-submit'; ?>",
      type: "POST",
      data: fd,
      processData: false,
      contentType: false,
      cache: false,
      beforeSend: function(){
        $("#purSpinner").removeClass("d-none");
        $("#purError").addClass("d-none").empty();
        $("#purchaseAddForm :submit").prop("disabled", true).addClass("d-none");
      },
      success: function(res){
        $("#purSpinner").addClass("d-none");
        $("#purchaseAddForm :submit").prop("disabled", false).removeClass("d-none");
        let obj = {}; try{ obj=JSON.parse(res);}catch(e){}
        if(obj.error){
          $("#purError").html(obj.error).removeClass("d-none");
          toastr.error("Please check errors list!","Error");
          return;
        }
        if(obj.success){
          toastr.success("Purchase saved","Success");
          $("#purResetBtn").trigger("click");
          if (purchasesDT) purchasesDT.ajax.reload(null,false);
          return;
        }
        toastr.error("Unexpected response","Error");
      },
      error: function(){
        $("#purSpinner").addClass("d-none");
        $("#purchaseAddForm :submit").prop("disabled", false).removeClass("d-none");
        toastr.error("Error while sending request!","Error");
      }
    });
  });

  // DataTable
  $(document).ready(function(){
    purchasesDT = $("#purchasesTable").DataTable({
      processing:true, serverSide:true, pageLength:25, responsive:true, searchDelay:500,
      ajax:{ url:"<?= base_url() . 'purchases-list'; ?>", type:"POST" },
      columns:[
        { data:"ref_no", title:"Ref No" },
        { data:"purchase_date", title:"Date" },
        { data:"total_amount", title:"Total" },
        { data:"id", orderable:false, searchable:false, render: id => `
          <div class="btn-group btn-group-sm">
            <button class="btn btn-warning btn-edit-pur" data-id="${id}">Edit</button>
          </div>`
        }
      ],
      order:[[1,"desc"]]
    });
  });

  // Load record into edit modal
  $(document).on('click', '.btn-edit-pur', function(){
    const id = $(this).data('id');
    $("#purEditError").addClass('d-none').empty();
    $("#pur-edit-items-wrapper").empty();

    $.ajax({
      url: "<?= base_url() . 'purchase/'; ?>"+id,
      type:"GET",
      beforeSend: function(){ $("#purEditSpinner").removeClass('d-none'); $('#iboxPur .ibox-content').addClass('sk-loading'); },
      success: function(res){
        $("#purEditSpinner").addClass('d-none'); $('#iboxPur .ibox-content').removeClass('sk-loading');
        let obj={}; try{ obj=JSON.parse(res);}catch(e){}
        if(obj.error){ toastr.error(obj.error.replace(/<[^>]*>?/gm,''),"Error"); return; }
        const r=obj.data;

        $("#edit_purchase_id").val(r.id);
        $("#edit_ref_no").val(r.ref_no);
        $("#edit_purchase_date").val(r.purchase_date.replace(' ','T')); // quick map for datetime-local
        $("#edit_supplier_id").val(r.supplier_id);

        const items = Array.isArray(r.items)? r.items : [];
        if(!items.length){
          const tpl = document.getElementById('pur-edit-item-row-tpl').content.cloneNode(true);
          $("#pur-edit-items-wrapper").append(tpl);
        }else{
          items.forEach(it=>{
            const tpl = document.getElementById('pur-edit-item-row-tpl').content.cloneNode(true);
            $(tpl).find('[name="product_id[]"]').val(it.product_id);
            $(tpl).find('[name="batch_no[]"]').val(it.batch_no);
            $(tpl).find('[name="qty[]"]').val(it.qty);
            $(tpl).find('[name="price[]"]').val(it.price);
            $("#pur-edit-items-wrapper").append(tpl);
          });
        }

        $("#editPurchaseModal").modal('show');
      },
      error: function(){
        $("#purEditSpinner").addClass('d-none'); $('#iboxPur .ibox-content').removeClass('sk-loading');
        toastr.error("Failed to load purchase","Error");
      }
    });
  });

  // Add row in edit modal
  $("#purEditAddItem").on('click', function(){
    const tpl = document.getElementById('pur-edit-item-row-tpl').content.cloneNode(true);
    $("#pur-edit-items-wrapper").append(tpl);
  });
  $(document).on('click', '#pur-edit-items-wrapper .remove-item', function(){
    $(this).closest('.item-row').remove();
  });

  // submit edit
  $("#purchaseEditForm").on("submit", function(e){
    e.preventDefault();
    const items=[];
    $("#pur-edit-items-wrapper .item-row").each(function(){
      const product_id = $(this).find('[name="product_id[]"]').val();
      const batch_no   = $(this).find('[name="batch_no[]"]').val();
      const qty        = $(this).find('[name="qty[]"]').val();
      const price      = $(this).find('[name="price[]"]').val();
      if(product_id && batch_no && qty){
        items.push({ product_id:parseInt(product_id,10), batch_no, qty:parseInt(qty,10), price:parseFloat(price||0) });
      }
    });
    const fd = new FormData(this);
    fd.append('items', JSON.stringify(items));

    $.ajax({
      url:"<?= base_url() . 'purchase-update'; ?>",
      type:"POST",
      data: fd, processData:false, contentType:false, cache:false,
      beforeSend:function(){ $("#purEditSubmitBtn").prop("disabled",true); $("#purEditSpinner").removeClass('d-none'); },
      success:function(res){
        $("#purEditSpinner").addClass('d-none'); $("#purEditSubmitBtn").prop("disabled",false);
        let obj={}; try{ obj=JSON.parse(res);}catch(e){}
        if(obj.error){ $("#purEditError").html(obj.error).removeClass('d-none'); toastr.error("Please check errors list!","Error"); return; }
        if(obj.success){ toastr.success("Purchase updated","Success"); $("#editPurchaseModal").modal('hide'); if(purchasesDT) purchasesDT.ajax.reload(null,false); return; }
        toastr.error("Unexpected response","Error");
      },
      error:function(){ $("#purEditSpinner").addClass('d-none'); $("#purEditSubmitBtn").prop("disabled",false); toastr.error("Error while sending request!","Error"); }
    });
  });
</script>
