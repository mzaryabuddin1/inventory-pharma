<?php $pagename = "sales"; ?>
<?php require_once("layout/header.php") ?>
<?php require_once("layout/sidebar.php") ?>
<?php require_once("layout/navbar.php") ?>
<link href="<?= base_url() ?>theme/css/plugins/dataTables/datatables.min.css" rel="stylesheet" />

<div class="wrapper wrapper-content">
  <!-- ADD SALE / INVOICE -->
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox">
        <div class="ibox-title">
          <button class="btn btn-primary collapse-link" type="button">Click To Create Invoice</button>
          <div class="ibox-tools"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></div>
        </div>
        <div class="ibox-content">
          <form id="saleAddForm">
            <div class="alert alert-danger d-none" id="saleError"></div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Invoice No</label>
              <div class="col-sm-4">
                <input type="text" name="invoice_no" class="form-control" required>
              </div>

              <label class="col-sm-2 col-form-label">Date</label>
              <div class="col-sm-4">
                <input type="datetime-local" name="sale_date" class="form-control" required>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Customer</label>
              <div class="col-sm-10">
                <select name="customer_id" class="form-control" required>
                  <option value="1">Select customer</option>
                  <!-- TODO: populate customers -->
                    <?php foreach ($customers as $customer) : ?>
                      <option value="<?= $customer['id'] ?>"><?= $customer['name'] ?></option>  
                    <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="hr-line-dashed"></div>

            <div class="form-group">
              <label>Items</label>
              <div id="sale-items-wrapper">
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
                  <div class="col-md-2">
                    <input type="text" name="batch_no[]" class="form-control" placeholder="Batch No" required>
                  </div>
                  <div class="col-md-2">
                    <input type="number" name="qty[]" class="form-control" placeholder="Qty" min="1" required>
                  </div>
                  <div class="col-md-2">
                    <input type="number" step="0.01" name="price[]" class="form-control" placeholder="Price" min="0" required>
                  </div>
                  <div class="col-md-2">
                    <button type="button" class="btn btn-block btn-primary add-item">+</button>
                  </div>
                </div>
              </div>
              <template id="sale-item-row-tpl">
                <div class="row item-row align-items-center mb-2">
                  <div class="col-md-4">
                    <select name="product_id[]" class="form-control" required>
                      <option value="">Select product</option>
                    </select>
                  </div>
                  <div class="col-md-2"><input type="text" name="batch_no[]" class="form-control" placeholder="Batch No" required></div>
                  <div class="col-md-2"><input type="number" name="qty[]" class="form-control" placeholder="Qty" min="1" required></div>
                  <div class="col-md-2"><input type="number" step="0.01" name="price[]" class="form-control" placeholder="Price" min="0" required></div>
                  <div class="col-md-2"><button type="button" class="btn btn-block btn-outline-danger remove-item">−</button></div>
                </div>
              </template>
            </div>

            <div class="sk-spinner sk-spinner-wave d-none" id="saleSpinner">
              <div class="sk-rect1"></div><div class="sk-rect2"></div><div class="sk-rect3"></div><div class="sk-rect4"></div><div class="sk-rect5"></div>
            </div>

            <div class="form-group row">
              <div class="col-sm-4 col-sm-offset-2">
                <button type="reset" class="btn btn-white" id="saleResetBtn">Reset</button>
                <button type="submit" class="btn btn-primary">Save Invoice</button>
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
      <div class="ibox" id="iboxSale">
        <div class="ibox-title">
          <h5>Sales</h5>
          <div class="ibox-tools"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></div>
        </div>
        <div class="ibox-content">
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" id="salesTable">
              <thead>
                <tr>
                  <th>Invoice No</th>
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
  <div class="modal fade" id="editSaleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <form id="saleEditForm" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Invoice</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger d-none" id="saleEditError"></div>
          <input type="hidden" name="sale_id" id="edit_sale_id">

          <div class="form-group row">
            <label class="col-sm-2 col-form-label">Invoice No</label>
            <div class="col-sm-4"><input type="text" name="invoice_no" id="edit_invoice_no" class="form-control" required></div>

            <label class="col-sm-2 col-form-label">Date</label>
            <div class="col-sm-4"><input type="datetime-local" name="sale_date" id="edit_sale_date" class="form-control" required></div>
          </div>

          <div class="form-group row">
            <label class="col-sm-2 col-form-label">Customer</label>
            <div class="col-sm-10">
              <select name="customer_id" id="edit_customer_id" class="form-control" required>
                <option value="">Select customer</option>
                <!-- TODO -->
                   <?php foreach ($customers as $customer) : ?>
                      <option value="<?= $customer['id'] ?>"><?= $customer['name'] ?></option>  
                    <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="hr-line-dashed"></div>

          <div class="form-group">
            <label>Items</label>
            <div id="sale-edit-items-wrapper"></div>
            <template id="sale-edit-item-row-tpl">
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
            <button type="button" class="btn btn-sm btn-primary mt-2" id="saleEditAddItem">+ Add Row</button>
          </div>
        </div>
        <div class="modal-footer">
          <div class="sk-spinner sk-spinner-wave d-none" id="saleEditSpinner">
            <div class="sk-rect1"></div><div class="sk-rect2"></div><div class="sk-rect3"></div><div class="sk-rect4"></div><div class="sk-rect5"></div>
          </div>
          <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="saleEditSubmitBtn">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once("layout/footer.php") ?>
<script src="<?= base_url() ?>theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?= base_url() ?>theme/js/plugins/dataTables/dataTables.bootstrap4.min.js"></script>

<script>
  var salesDT;

  // add/remove rows (Add form)
  $(document).on('click', '#sale-items-wrapper .add-item', function(){
    const tpl = document.getElementById('sale-item-row-tpl').content.cloneNode(true);
    $('#sale-items-wrapper').append(tpl);
  });
  $(document).on('click', '#sale-items-wrapper .remove-item', function(){
    $(this).closest('.item-row').remove();
  });

  // submit Add
  $("#saleAddForm").on("submit", function(e){
    e.preventDefault();
    const items=[];
    $("#sale-items-wrapper .item-row").each(function(){
      const product_id = $(this).find('[name="product_id[]"]').val();
      const batch_no   = $(this).find('[name="batch_no[]"]').val();
      const qty        = $(this).find('[name="qty[]"]').val();
      const price      = $(this).find('[name="price[]"]').val();
      if(product_id && batch_no && qty){
        items.push({
          product_id: parseInt(product_id,10),
          batch_no,
          qty: parseInt(qty,10),
          price: parseFloat(price || 0)
        });
      }
    });

    const fd = new FormData(this);
    fd.append('items', JSON.stringify(items));

    $.ajax({
      url: "<?= base_url() .'add-sale-submit'; ?>",
      type: "POST",
      data: fd,
      processData: false,
      contentType: false,
      cache: false,
      beforeSend: function(){
        $("#saleSpinner").removeClass("d-none");
        $("#saleError").addClass("d-none").empty();
        $("#saleAddForm :submit").prop("disabled", true).addClass("d-none");
      },
      success: function(res){
        $("#saleSpinner").addClass("d-none");
        $("#saleAddForm :submit").prop("disabled", false).removeClass("d-none");
        let obj={}; try{ obj=JSON.parse(res);}catch(e){}
        if(obj.error){
          $("#saleError").html(obj.error).removeClass("d-none");
          toastr.error("Please check errors list!", "Error");
          return;
        }
        if(obj.success){
          toastr.success("Invoice saved", "Success");
          $("#saleResetBtn").trigger("click");
          if (salesDT) salesDT.ajax.reload(null,false);
          return;
        }
        toastr.error("Unexpected response","Error");
      },
      error: function(){
        $("#saleSpinner").addClass("d-none");
        $("#saleAddForm :submit").prop("disabled", false).removeClass("d-none");
        toastr.error("Error while sending request!", "Error");
      }
    });
  });

  // DataTable
  $(document).ready(function(){
    salesDT = $("#salesTable").DataTable({
      processing:true, serverSide:true, pageLength:25, responsive:true, searchDelay:500,
      ajax:{ url:"<?= base_url()  . 'sales-list'; ?>", type:"POST" },
      columns:[
        { data:"invoice_no", title:"Invoice No" },
        { data:"sale_date",  title:"Date" },
        { data:"total_amount", title:"Total" },
        { data:"id", orderable:false, searchable:false, render: id => `
          <div class="btn-group btn-group-sm">
            <button class="btn btn-warning btn-edit-sale" data-id="${id}">Edit</button>
          </div>`
        }
      ],
      order:[[1,"desc"]]
    });
  });

  // open edit modal
  $(document).on('click', '.btn-edit-sale', function(){
    const id=$(this).data('id');
    $("#saleEditError").addClass('d-none').empty();
    $("#sale-edit-items-wrapper").empty();

    $.ajax({
      url: "<?= base_url() . 'sale/'; ?>"+id,
      type: "GET",
      beforeSend: function(){
        $("#saleEditSpinner").removeClass('d-none');
        $('#iboxSale .ibox-content').addClass('sk-loading');
      },
      success: function(res){
        $("#saleEditSpinner").addClass('d-none');
        $('#iboxSale .ibox-content').removeClass('sk-loading');
        let obj={}; try{ obj=JSON.parse(res);}catch(e){}
        if(obj.error){ toastr.error(obj.error.replace(/<[^>]*>?/gm,''),"Error"); return; }

        const r=obj.data;
        $("#edit_sale_id").val(r.id);
        $("#edit_invoice_no").val(r.invoice_no);
        $("#edit_sale_date").val(r.sale_date.replace(' ','T'));
        $("#edit_customer_id").val(r.customer_id);

        const items = Array.isArray(r.items) ? r.items : [];
        if(!items.length){
          const tpl=document.getElementById('sale-edit-item-row-tpl').content.cloneNode(true);
          $("#sale-edit-items-wrapper").append(tpl);
        }else{
          items.forEach(it=>{
            const tpl=document.getElementById('sale-edit-item-row-tpl').content.cloneNode(true);
            $(tpl).find('[name="product_id[]"]').val(it.product_id);
            $(tpl).find('[name="batch_no[]"]').val(it.batch_no);
            $(tpl).find('[name="qty[]"]').val(it.qty);
            $(tpl).find('[name="price[]"]').val(it.price);
            $("#sale-edit-items-wrapper").append(tpl);
          });
        }

        $("#editSaleModal").modal('show');
      },
      error: function(){
        $("#saleEditSpinner").addClass('d-none');
        $('#iboxSale .ibox-content').removeClass('sk-loading');
        toastr.error("Failed to load invoice","Error");
      }
    });
  });

  // add/remove rows in edit modal
  $("#saleEditAddItem").on('click', function(){
    const tpl=document.getElementById('sale-edit-item-row-tpl').content.cloneNode(true);
    $("#sale-edit-items-wrapper").append(tpl);
  });
  $(document).on('click', '#sale-edit-items-wrapper .remove-item', function(){
    $(this).closest('.item-row').remove();
  });

  // submit edit
  $("#saleEditForm").on("submit", function(e){
    e.preventDefault();
    const items=[];
    $("#sale-edit-items-wrapper .item-row").each(function(){
      const product_id=$(this).find('[name="product_id[]"]').val();
      const batch_no=$(this).find('[name="batch_no[]"]').val();
      const qty=$(this).find('[name="qty[]"]').val();
      const price=$(this).find('[name="price[]"]').val();
      if(product_id && batch_no && qty){
        items.push({ product_id:parseInt(product_id,10), batch_no, qty:parseInt(qty,10), price:parseFloat(price||0) });
      }
    });
    const fd = new FormData(this);
    fd.append('items', JSON.stringify(items));

    $.ajax({
      url:"<?= base_url() . 'sale-update'; ?>",
      type:"POST",
      data: fd,
      processData:false,
      contentType:false,
      cache:false,
      beforeSend:function(){
        $("#saleEditSubmitBtn").prop("disabled", true);
        $("#saleEditSpinner").removeClass('d-none');
      },
      success:function(res){
        $("#saleEditSpinner").addClass('d-none');
        $("#saleEditSubmitBtn").prop("disabled", false);
        let obj={}; try{ obj=JSON.parse(res);}catch(e){}
        if(obj.error){
          $("#saleEditError").html(obj.error).removeClass('d-none');
          toastr.error("Please check errors list!","Error");
          return;
        }
        if(obj.success){
          toastr.success("Invoice updated","Success");
          $("#editSaleModal").modal('hide');
          if (salesDT) salesDT.ajax.reload(null,false);
          return;
        }
        toastr.error("Unexpected response","Error");
      },
      error:function(){
        $("#saleEditSpinner").addClass('d-none');
        $("#saleEditSubmitBtn").prop("disabled", false);
        toastr.error("Error while sending request!","Error");
      }
    });
  });
</script>
