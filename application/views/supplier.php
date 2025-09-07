<?php $pagename = "supplier"; ?>
<?php require_once("layout/header.php") ?>
<?php require_once("layout/sidebar.php") ?>
<?php require_once("layout/navbar.php") ?>
<link href="<?= base_url() ?>theme/css/plugins/dataTables/datatables.min.css" rel="stylesheet" />

<div class="wrapper wrapper-content">
    <!-- ADD SUPPLIER PANEL -->
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <button class="btn btn-primary collapse-link" type="button">Click To Add Supplier</button>
                    <div class="ibox-tools">
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form method="post" id="supplierAddForm" enctype="multipart/form-data">
                        <div class="alert alert-danger d-none" id="supError"></div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Name</label>
                            <div class="col-sm-10">
                                <input type="text" name="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" name="email" class="form-control">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Phone</label>
                            <div class="col-sm-10">
                                <input type="text" name="phone" class="form-control">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Address</label>
                            <div class="col-sm-10">
                                <textarea name="address" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Logo</label>
                            <div class="col-sm-10">
                                <input type="file" name="logo" class="form-control">
                            </div>
                        </div>

                        <div class="sk-spinner sk-spinner-wave d-none" id="supSpinner">
                            <div class="sk-rect1"></div>
                            <div class="sk-rect2"></div>
                            <div class="sk-rect3"></div>
                            <div class="sk-rect4"></div>
                            <div class="sk-rect5"></div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button type="reset" class="btn btn-white" id="supResetBtn">Reset</button>
                                <button type="submit" class="btn btn-primary">Save Supplier</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- LIST PANEL -->
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox" id="iboxSup">
                <div class="ibox-title">
                    <h5>Suppliers</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>
                <div class="ibox-content">
                                        <div class="sk-spinner sk-spinner-wave">
                        <div class="sk-rect1"></div>
                        <div class="sk-rect2"></div>
                        <div class="sk-rect3"></div>
                        <div class="sk-rect4"></div>
                        <div class="sk-rect5"></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="suppliersTable">
                            <thead>
                                <tr>
                                    <th>Logo</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>x
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editSupplierModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="supplierEditForm" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit supplier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-danger d-none" id="supEditError"></div>
                    <input type="hidden" name="supplier_id" id="edit_supplier_id">

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
                        <label class="col-sm-3 col-form-label">Logo</label>
                        <div class="col-sm-9">
                            <div id="edit_logoPreview" class="mb-2"></div>
                            <input type="file" name="logo" id="edit_logo" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="sk-spinner sk-spinner-wave d-none" id="supEditSpinner">
                        <div class="sk-rect1"></div>
                        <div class="sk-rect2"></div>
                        <div class="sk-rect3"></div>
                        <div class="sk-rect4"></div>
                        <div class="sk-rect5"></div>
                    </div>
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="supEditSubmitBtn">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once("layout/footer.php") ?>
<script src="<?= base_url() ?>theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?= base_url() ?>theme/js/plugins/dataTables/dataTables.bootstrap4.min.js"></script>

<script>
    var suppliersDT;

    $(document).ready(function() {
        suppliersDT = $("#suppliersTable").DataTable({
            serverSide: true,
            pageLength: 25,
            responsive: true,
            searchDelay: 500,
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [{
                    extend: "copy"
                },
                {
                    extend: "csv"
                },
                {
                    extend: "excel",
                    title: "Suppliers"
                },
                {
                    extend: "pdf",
                    title: "Suppliers"
                },
                {
                    extend: "print",
                    customize: function(win) {
                        $(win.document.body).addClass("white-bg").css("font-size", "10px");
                        $(win.document.body).find("table").addClass("compact").css("font-size", "inherit");
                    },
                },
            ],
            ajax: {
                url: "<?= base_url() . 'suppliers-list'; ?>",
                type: "POST"
            },
            columns: [{
                    data: "logo",
                    orderable: false,
                    searchable: false,
                    render: function(d) {
                        return d ? `<img src="${d}" style="height:40px">` : "";
                    }
                },
                {
                    data: "name",
                    title: "Name"
                },
                {
                    data: "email",
                    title: "Email"
                },
                {
                    data: "phone",
                    title: "Phone"
                },
                {
                    data: "address",
                    title: "Address"
                },
                {
                    data: "status",
                    title: "Status",
                    render: function(s) {
                        return (Number(s) === 1) ?
                            '<span class="badge badge-success">Active</span>' :
                            '<span class="badge badge-secondary">Inactive</span>';
                    }
                },
                {
                    data: "created_at",
                    title: "Created"
                },
                {
                    data: "id",
                    orderable: false,
                    searchable: false,
                    render: function(id, t, row) {
                        return `<div class="btn-group btn-group-sm">
                      <button type="button" class="btn btn-warning btn-edit-sup" data-id="${id}">Edit</button>
                    </div>`;
                    }
                }
            ],
            order: [
                [1, "asc"]
            ]
        });
    });

    function reloadSuppliersDT() {
        if (suppliersDT) suppliersDT.ajax.reload(null, false);
    }
</script>

<script>
    // ADD supplier submit
    $("#supplierAddForm").on("submit", function(e) {
        e.preventDefault();
        const fd = new FormData(this);

        $.ajax({
            url: "<?= base_url() . "add-supplier-submit"; ?>",
            type: "POST",
            data: fd,
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function() {
                $(":submit", "#supplierAddForm").prop("disabled", true).addClass("d-none");
                $("#supSpinner").removeClass("d-none");
                $("#supError").addClass("d-none").empty();
            },
            success: function(res) {
                $("#supSpinner").addClass("d-none");
                $(":submit", "#supplierAddForm").prop("disabled", false).removeClass("d-none");

                let obj = {};
                try {
                    obj = JSON.parse(res);
                } catch (e) {}
                if (obj.error) {
                    $("#supError").html(obj.error).removeClass("d-none");
                    toastr.error("Please check errors list!", "Error");
                    return;
                }
                if (obj.success) {
                    toastr.success("Supplier Saved", "Success");
                    $("#supResetBtn").trigger("click");
                    reloadSuppliersDT();
                    return;
                }
                toastr.error("Unexpected response", "Error");
            },
            error: function() {
                $("#supSpinner").addClass("d-none");
                $(":submit", "#supplierAddForm").prop("disabled", false).removeClass("d-none");
                toastr.error("Error while sending request!", "Error");
            }
        });
    });

    // open Edit modal
    $(document).on('click', '.btn-edit-sup', function() {
        const id = $(this).data('id');
        $("#supEditError").addClass('d-none').empty();
        $("#supplierEditForm")[0].reset();
        $("#edit_logoPreview").empty();

        $.ajax({
            url: "<?= base_url() . "supplier/"; ?>" + id,
            type: "GET",
            beforeSend: function() {
                $("#supEditSpinner").removeClass('d-none');
                $('#iboxSup').children(".ibox-content").addClass("sk-loading");
            },
            success: function(res) {
                $("#supEditSpinner").addClass('d-none');
                $('#iboxSup').children(".ibox-content").removeClass("sk-loading");

                let obj = {};
                try {
                    obj = JSON.parse(res);
                } catch (e) {}
                if (obj.error) {
                    toastr.error(obj.error.replace(/<[^>]*>?/gm, ''), "Error");
                    return;
                }

                const s = obj.data;
                $('#edit_supplier_id').val(s.id);
                $('#edit_name').val(s.name);
                $('#edit_email').val(s.email || '');
                $('#edit_phone').val(s.phone || '');
                $('#edit_address').val(s.address || '');
                if (s.logo) $('#edit_logoPreview').html(`<img src="${s.logo}" style="height:60px">`);

                $('#editSupplierModal').modal('show');
            },
            error: function() {
                $("#supEditSpinner").addClass('d-none');
                $('#iboxSup').children(".ibox-content").removeClass("sk-loading");
                toastr.error("Failed to load supplier", "Error");
            }
        });
    });

    // submit Edit
    $("#supplierEditForm").on("submit", function(e) {
        e.preventDefault();
        const fd = new FormData(this);

        $.ajax({
            url: "<?= base_url() . 'supplier-update'; ?>",
            type: "POST",
            data: fd,
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function() {
                $("#supEditSubmitBtn").prop("disabled", true);
                $("#supEditSpinner").removeClass('d-none');
                $("#supEditError").addClass('d-none').empty();
            },
            success: function(res) {
                $("#supEditSpinner").addClass('d-none');
                $("#supEditSubmitBtn").prop("disabled", false);

                let obj = {};
                try {
                    obj = JSON.parse(res);
                } catch (e) {}
                if (obj.error) {
                    $("#supEditError").html(obj.error).removeClass('d-none');
                    toastr.error("Please check errors list!", "Error");
                    return;
                }
                if (obj.success) {
                    toastr.success("Supplier updated", "Success");
                    $('#editSupplierModal').modal('hide');
                    reloadSuppliersDT();
                    return;
                }
                toastr.error("Unexpected response", "Error");
            },
            error: function() {
                $("#supEditSpinner").addClass('d-none');
                $("#supEditSubmitBtn").prop("disabled", false);
                toastr.error("Error while sending request!", "Error");
            }
        });
    });
</script>