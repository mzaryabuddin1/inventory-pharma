<?php $pagename = "purchase_return"; ?>
<?php require_once("layout/header.php") ?>
<?php require_once("layout/sidebar.php") ?>
<?php require_once("layout/navbar.php") ?>
<link href="<?= base_url() ?>theme/css/plugins/dataTables/datatables.min.css" rel="stylesheet" />

<div class="wrapper wrapper-content">
    <!-- ADD PURCHASE RETURN -->
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <button class="btn btn-primary collapse-link" type="button">Click To Add Purchase Return</button>
                    <div class="ibox-tools"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></div>
                </div>
                <div class="ibox-content">
                    <form id="prAddForm">
                        <div class="alert alert-danger d-none" id="prError"></div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Reference No</label>
                            <div class="col-sm-4"><input type="text" name="ref_no" class="form-control" required></div>

                            <label class="col-sm-2 col-form-label">Return Date</label>
                            <div class="col-sm-4"><input type="datetime-local" name="return_date" class="form-control" required></div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Original Purchase</label>
                            <div class="col-sm-4">
                                <select name="purchase_id" class="form-control" required>
                                    <option value="1">Select purchase</option>
                                    <!-- TODO -->
                                </select>
                            </div>
                            <label class="col-sm-2 col-form-label">Supplier</label>
                            <div class="col-sm-4">
                                <select name="supplier_id" class="form-control" required>
                                    <option value="1">Select supplier</option>
                                    <!-- TODO -->
                                </select>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>Items</label>
                            <div id="pr-items-wrapper">
                                <div class="row item-row align-items-center mb-2">
                                    <div class="col-md-4">
                                        <select name="product_id[]" class="form-control" required>
                                            <option value="1">Select product</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2"><input type="text" name="batch_no[]" class="form-control" placeholder="Batch No" required></div>
                                    <div class="col-md-2"><input type="number" name="qty[]" class="form-control" placeholder="Qty" min="1" required></div>
                                    <div class="col-md-2"><input type="number" step="0.01" name="price[]" class="form-control" placeholder="Price" min="0" required></div>
                                    <div class="col-md-2"><button type="button" class="btn btn-block btn-primary add-item">+</button></div>
                                </div>
                            </div>
                            <template id="pr-item-row-tpl">
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

                        <div class="sk-spinner sk-spinner-wave d-none" id="prSpinner">
                            <div class="sk-rect1"></div>
                            <div class="sk-rect2"></div>
                            <div class="sk-rect3"></div>
                            <div class="sk-rect4"></div>
                            <div class="sk-rect5"></div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button type="reset" class="btn btn-white" id="prResetBtn">Reset</button>
                                <button type="submit" class="btn btn-primary">Save Return</button>
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
            <div class="ibox" id="iboxPR">
                <div class="ibox-title">
                    <h5>Purchase Returns</h5>
                    <div class="ibox-tools"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="prTable">
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
    <div class="modal fade" id="editPRModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <form id="prEditForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Purchase Return</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger d-none" id="prEditError"></div>
                    <input type="hidden" name="purchase_return_id" id="edit_pr_id">

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Reference No</label>
                        <div class="col-sm-4"><input type="text" name="ref_no" id="edit_pr_ref_no" class="form-control" required></div>

                        <label class="col-sm-2 col-form-label">Return Date</label>
                        <div class="col-sm-4"><input type="datetime-local" name="return_date" id="edit_pr_date" class="form-control" required></div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Original Purchase</label>
                        <div class="col-sm-4">
                            <select name="purchase_id" id="edit_pr_purchase_id" class="form-control" required>
                                <option value="">Select purchase</option>
                            </select>
                        </div>
                        <label class="col-sm-2 col-form-label">Supplier</label>
                        <div class="col-sm-4">
                            <select name="supplier_id" id="edit_pr_supplier_id" class="form-control" required>
                                <option value="">Select supplier</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Items</label>
                        <div id="pr-edit-items-wrapper"></div>
                        <template id="pr-edit-item-row-tpl">
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
                        <button type="button" class="btn btn-sm btn-primary mt-2" id="prEditAddItem">+ Add Row</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="sk-spinner sk-spinner-wave d-none" id="prEditSpinner">
                        <div class="sk-rect1"></div>
                        <div class="sk-rect2"></div>
                        <div class="sk-rect3"></div>
                        <div class="sk-rect4"></div>
                        <div class="sk-rect5"></div>
                    </div>
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="prEditSubmitBtn">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once("layout/footer.php") ?>
<script src="<?= base_url() ?>theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?= base_url() ?>theme/js/plugins/dataTables/dataTables.bootstrap4.min.js"></script>

<script>
    var prDT;

    // Add/remove rows
    $(document).on('click', '#pr-items-wrapper .add-item', function() {
        const tpl = document.getElementById('pr-item-row-tpl').content.cloneNode(true);
        $('#pr-items-wrapper').append(tpl);
    });
    $(document).on('click', '#pr-items-wrapper .remove-item', function() {
        $(this).closest('.item-row').remove();
    });

    // Submit Add
    $("#prAddForm").on("submit", function(e) {
        e.preventDefault();
        const items = [];
        $("#pr-items-wrapper .item-row").each(function() {
            const product_id = $(this).find('[name="product_id[]"]').val();
            const batch_no = $(this).find('[name="batch_no[]"]').val();
            const qty = $(this).find('[name="qty[]"]').val();
            const price = $(this).find('[name="price[]"]').val();
            if (product_id && batch_no && qty) {
                items.push({
                    product_id: parseInt(product_id, 10),
                    batch_no,
                    qty: parseInt(qty, 10),
                    price: parseFloat(price || 0)
                });
            }
        });
        const fd = new FormData(this);
        fd.append('items', JSON.stringify(items));

        $.ajax({
            url: "<?= base_url() . 'add-purchase-return-submit'; ?>",
            type: "POST",
            data: fd,
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function() {
                $("#prSpinner").removeClass("d-none");
                $("#prError").addClass("d-none").empty();
                $("#prAddForm :submit").prop("disabled", true).addClass("d-none");
            },
            success: function(res) {
                $("#prSpinner").addClass("d-none");
                $("#prAddForm :submit").prop("disabled", false).removeClass("d-none");
                let obj = {};
                try {
                    obj = JSON.parse(res);
                } catch (e) {}
                if (obj.error) {
                    $("#prError").html(obj.error).removeClass("d-none");
                    toastr.error("Please check errors list!", "Error");
                    return;
                }
                if (obj.success) {
                    toastr.success("Purchase return saved", "Success");
                    $("#prResetBtn").trigger("click");
                    if (prDT) prDT.ajax.reload(null, false);
                    return;
                }
                toastr.error("Unexpected response", "Error");
            },
            error: function() {
                $("#prSpinner").addClass("d-none");
                $("#prAddForm :submit").prop("disabled", false).removeClass("d-none");
                toastr.error("Error while sending request!", "Error");
            }
        });
    });

    // DT
    $(document).ready(function() {
        prDT = $("#prTable").DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            responsive: true,
            searchDelay: 500,
            ajax: {
                url: "<?= base_url() . 'purchase-returns-list'; ?>",
                type: "POST"
            },
            columns: [{
                    data: "ref_no",
                    title: "Ref No"
                },
                {
                    data: "return_date",
                    title: "Date"
                },
                {
                    data: "return_amount",
                    title: "Amount"
                },
                {
                    data: "id",
                    orderable: false,
                    searchable: false,
                    render: id => `<div class="btn-group btn-group-sm"><button class="btn btn-warning btn-edit-pr" data-id="${id}">Edit</button></div>`
                }
            ],
            order: [
                [1, "desc"]
            ]
        });
    });

    // load edit
    $(document).on('click', '.btn-edit-pr', function() {
        const id = $(this).data('id');
        $("#prEditError").addClass('d-none').empty();
        $("#pr-edit-items-wrapper").empty();

        $.ajax({
            url: "<?= base_url() . 'purchase-return/'; ?>" + id,
            type: "GET",
            beforeSend: function() {
                $("#prEditSpinner").removeClass('d-none');
                $('#iboxPR .ibox-content').addClass('sk-loading');
            },
            success: function(res) {
                $("#prEditSpinner").addClass('d-none');
                $('#iboxPR .ibox-content').removeClass('sk-loading');
                let obj = {};
                try {
                    obj = JSON.parse(res);
                } catch (e) {}
                if (obj.error) {
                    toastr.error(obj.error.replace(/<[^>]*>?/gm, ''), "Error");
                    return;
                }
                const r = obj.data;
                $("#edit_pr_id").val(r.id);
                $("#edit_pr_ref_no").val(r.ref_no);
                $("#edit_pr_date").val(r.return_date.replace(' ', 'T'));
                $("#edit_pr_purchase_id").val(r.purchase_id);
                $("#edit_pr_supplier_id").val(r.supplier_id);

                const items = Array.isArray(r.items) ? r.items : [];
                if (!items.length) {
                    const tpl = document.getElementById('pr-edit-item-row-tpl').content.cloneNode(true);
                    $("#pr-edit-items-wrapper").append(tpl);
                } else {
                    items.forEach(it => {
                        const tpl = document.getElementById('pr-edit-item-row-tpl').content.cloneNode(true);
                        $(tpl).find('[name="product_id[]"]').val(it.product_id);
                        $(tpl).find('[name="batch_no[]"]').val(it.batch_no);
                        $(tpl).find('[name="qty[]"]').val(it.qty);
                        $(tpl).find('[name="price[]"]').val(it.price);
                        $("#pr-edit-items-wrapper").append(tpl);
                    });
                }
                $("#editPRModal").modal('show');
            },
            error: function() {
                $("#prEditSpinner").addClass('d-none');
                $('#iboxPR .ibox-content').removeClass('sk-loading');
                toastr.error("Failed to load purchase return", "Error");
            }
        });
    });

    $("#prEditAddItem").on('click', function() {
        const tpl = document.getElementById('pr-edit-item-row-tpl').content.cloneNode(true);
        $("#pr-edit-items-wrapper").append(tpl);
    });
    $(document).on('click', '#pr-edit-items-wrapper .remove-item', function() {
        $(this).closest('.item-row').remove();
    });

    $("#prEditForm").on('submit', function(e) {
        e.preventDefault();
        const items = [];
        $("#pr-edit-items-wrapper .item-row").each(function() {
            const product_id = $(this).find('[name="product_id[]"]').val();
            const batch_no = $(this).find('[name="batch_no[]"]').val();
            const qty = $(this).find('[name="qty[]"]').val();
            const price = $(this).find('[name="price[]"]').val();
            if (product_id && batch_no && qty) items.push({
                product_id: parseInt(product_id, 10),
                batch_no,
                qty: parseInt(qty, 10),
                price: parseFloat(price || 0)
            });
        });
        const fd = new FormData(this);
        fd.append('items', JSON.stringify(items));
        $.ajax({
            url: "<?= base_url() . 'purchase-return-update'; ?>",
            type: "POST",
            data: fd,
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function() {
                $("#prEditSubmitBtn").prop("disabled", true);
                $("#prEditSpinner").removeClass('d-none');
            },
            success: function(res) {
                $("#prEditSpinner").addClass('d-none');
                $("#prEditSubmitBtn").prop("disabled", false);
                let obj = {};
                try {
                    obj = JSON.parse(res);
                } catch (e) {}
                if (obj.error) {
                    $("#prEditError").html(obj.error).removeClass('d-none');
                    toastr.error("Please check errors list!", "Error");
                    return;
                }
                if (obj.success) {
                    toastr.success("Purchase return updated", "Success");
                    $("#editPRModal").modal('hide');
                    if (prDT) prDT.ajax.reload(null, false);
                    return;
                }
                toastr.error("Unexpected response", "Error");
            },
            error: function() {
                $("#prEditSpinner").addClass('d-none');
                $("#prEditSubmitBtn").prop("disabled", false);
                toastr.error("Error while sending request!", "Error");
            }
        });
    });
</script>