<?php $pagename = "product"; ?>
<?php require_once("layout/header.php") ?>
<?php require_once("layout/sidebar.php") ?>
<?php require_once("layout/navbar.php") ?>
<link href="<?= base_url() ?>theme/css/plugins/dataTables/datatables.min.css" rel="stylesheet" />

<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <button class="btn btn-primary collapse-link" type="button">Click To Add Product</button>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form method="post" id="theform">
                        <div class="alert alert-danger d-none" id="error"></div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Name</label>
                            <div class="col-sm-10">
                                <input type="text" name="product_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Generic</label>
                            <div class="col-sm-10">
                                <input type="text" name="generic" class="form-control">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Prices</label>
                            <div class="col-sm-10" id="prices-wrapper">
                                <!-- first row has + button -->
                                <div class="row price-row align-items-center mb-2">
                                    <div class="col-5">
                                        <input type="number" step="0.01" name="mrp[]" class="form-control" placeholder="MRP" required>
                                    </div>
                                    <div class="col-5">
                                        <input type="number" step="0.01" name="tp[]" class="form-control" placeholder="TP" required>
                                    </div>
                                    <div class="col-2">
                                        <button type="button" class="btn btn-block btn-primary add-price">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Product Picture</label>
                            <div class="col-sm-10">
                                <input type="file" name="image" class="form-control">
                            </div>
                        </div>

                        <div class="sk-spinner sk-spinner-wave d-none" id="spinner">
                            <div class="sk-rect1"></div>
                            <div class="sk-rect2"></div>
                            <div class="sk-rect3"></div>
                            <div class="sk-rect4"></div>
                            <div class="sk-rect5"></div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button type="reset" class="btn btn-white" id="resetbtn">Reset</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </div>
                    </form>

                    <!-- hidden template for new rows (has a - button) -->
                    <template id="price-row-tpl">
                        <div class="row price-row align-items-center mb-2">
                            <div class="col-5">
                                <input type="number" step="0.01" name="mrp[]" class="form-control" placeholder="MRP" required>
                            </div>
                            <div class="col-5">
                                <input type="number" step="0.01" name="tp[]" class="form-control" placeholder="TP" required>
                            </div>
                            <div class="col-2">
                                <button type="button" class="btn btn-block btn-outline-danger remove-price">−</button>
                            </div>
                        </div>
                    </template>

                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-12">
            <div class="ibox" id="ibox1">
                <div class="ibox-title">
                    <h5>
                        Products
                    </h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
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
                        <table
                            class="table table-striped table-bordered table-hover" id="productsTable">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Generic</th>
                                    <th>Dated</th>
                                    <th>MRP</th>
                                    <th>TP</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="editForm" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-danger d-none" id="editError"></div>
                    <input type="hidden" name="product_id" id="edit_product_id">

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Name</label>
                        <div class="col-sm-9">
                            <input type="text" name="product_name" id="edit_product_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Generic</label>
                        <div class="col-sm-9">
                            <input type="text" name="generic" id="edit_generic" class="form-control">
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Prices</label>
                        <div class="col-sm-9" id="edit-prices-wrapper">
                            <!-- first row has + button; added by JS -->
                        </div>
                    </div>

                    <template id="edit-price-row-tpl">
                        <div class="row price-row align-items-center mb-2">
                            <div class="col-5">
                                <input type="number" step="0.01" name="mrp[]" class="form-control" placeholder="MRP" required>
                            </div>
                            <div class="col-5">
                                <input type="number" step="0.01" name="tp[]" class="form-control" placeholder="TP" required>
                            </div>
                            <div class="col-2">
                                <button type="button" class="btn btn-block btn-outline-danger remove-price">−</button>
                            </div>
                        </div>
                    </template>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Product Picture</label>
                        <div class="col-sm-9">
                            <div id="edit_imagePreview" class="mb-2"></div>
                            <input type="file" name="image" id="edit_image" class="form-control">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <div class="sk-spinner sk-spinner-wave d-none" id="editSpinner">
                        <div class="sk-rect1"></div>
                        <div class="sk-rect2"></div>
                        <div class="sk-rect3"></div>
                        <div class="sk-rect4"></div>
                        <div class="sk-rect5"></div>
                    </div>
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="editSubmitBtn">Update</button>
                </div>
            </form>
        </div>
    </div>

</div>




<?php require_once("layout/footer.php") ?>
<script src="<?= base_url() ?>theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?= base_url() ?>theme/js/plugins/dataTables/dataTables.bootstrap4.min.js"></script>

<script>
    var productsDT;

    $(document).ready(function() {
        productsDT = $("#productsTable").DataTable({
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
                    title: "Products"
                },
                {
                    extend: "pdf",
                    title: "Products"
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
                url: "<?= base_url() . "products-list"; ?>",
                type: "POST"
            },
            columns: [{
                    data: "image",
                    orderable: false,
                    searchable: false,
                    render: function(d) {
                        return d ? `<img src="${d}" style="height:40px">` : "";
                    }
                },
                {
                    data: "product_name",
                    title: "Name"
                },
                {
                    data: "generic",
                    title: "Generic"
                },
                {
                    data: "dated",
                    title: "Dated"
                }, // <-- new column (from exploded price row)
                {
                    data: "mrp",
                    title: "MRP"
                },
                {
                    data: "tp",
                    title: "TP"
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
                        return `
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-warning btn-edit" data-id="${id}">Edit</button>
                        </div>`;
                    }
                }
            ],
            order: [
                [1, "asc"]
            ] // default sort by Name
        });
    });



    // After successful add, reload without resetting page:
    function reloadProductsDT() {
        if (productsDT) productsDT.ajax.reload(null, false);
    }
</script>

<script>
    $(document).on('click', '.add-price', function() {
        const tpl = document.getElementById('price-row-tpl').content.cloneNode(true);
        $('#prices-wrapper').append(tpl);
    });

    $(document).on('click', '.remove-price', function() {
        $(this).closest('.price-row').remove();
    });

    $("#theform").on("submit", function(e) {
        e.preventDefault();

        // ---- build prices JSON ----
        const prices = [];
        const now = new Date();
        const currentDateTime = now.toISOString().slice(0, 19).replace('T', ' ');
        $("#prices-wrapper .price-row").each(function() {
            const mrp = $(this).find('input[name="mrp[]"]').val();
            const tp = $(this).find('input[name="tp[]"]').val();
            if (mrp || tp) {
                prices.push({
                    dated: currentDateTime,
                    mrp: mrp ? parseFloat(mrp) : null,
                    tp: tp ? parseFloat(tp) : null
                });
            }
        });

        // ---- form data with file(s) ----
        const formData = new FormData(this);
        formData.delete('mrp[]');
        formData.delete('tp[]');
        formData.append('prices', JSON.stringify(prices));

        $.ajax({
            url: "<?= base_url() . 'add-product-submit'; ?>",
            type: "post",
            data: formData,
            processData: false, // prevent jQuery from processing
            contentType: false, // prevent jQuery from overriding content type
            cache: false,
            beforeSend: function() {
                $(":submit").prop("disabled", true).addClass("d-none");
                $("#spinner").removeClass("d-none");
                $("#error").addClass("d-none");
                $("#error").addClass("d-none");
                $('#ibox1').children(".ibox-content").toggleClass("sk-loading")
            },
            success: function(res) {
                $('#ibox1').children(".ibox-content").toggleClass("sk-loading")
                let obj = {};
                try {
                    obj = JSON.parse(res);
                } catch (e) {}

                if (obj.error) {
                    $("#error").html(obj.error).removeClass("d-none");
                    $("#spinner").addClass("d-none");
                    $(":submit").removeClass("d-none");
                    toastr.error("Please check errors list!", "Error");
                    $(window).scrollTop(0);
                } else if (obj.success) {
                    $("#spinner").addClass("d-none");
                    toastr.success("Success!", "Product Saved");
                    $(":submit").prop("disabled", false).removeClass("d-none");
                    $(":submit").removeClass("d-none");
                    $("#resetbtn").trigger("click")
                    reloadProductsDT();
                } else {
                    $("#spinner").addClass("d-none");
                    $(":submit").prop("disabled", false).removeClass("d-none");
                    toastr.error("Something bad happened!", "Error");
                    $(window).scrollTop(0);
                }

                $(":submit").prop("disabled", false);
            },
            error: function() {
                $('#ibox1').children(".ibox-content").toggleClass("sk-loading")
                toastr.error("Error while sending request to server!", "Error");
                $(window).scrollTop(0);
                $("#spinner").addClass("d-none");
                $(":submit").prop("disabled", false).removeClass("d-none");
            }
        });
    });
</script>

<script>
    // open modal & populate
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        $("#editError").addClass('d-none').empty();
        $("#editForm")[0].reset();
        $("#edit_imagePreview").empty();
        $("#edit-prices-wrapper").empty();

        $.ajax({
            url: "<?= base_url() . "product/"; ?>" + id,
            type: "GET",
            beforeSend: function() {
                $("#editSpinner").removeClass('d-none');
                $('#ibox1').children(".ibox-content").toggleClass("sk-loading")
            },
            success: function(res) {
                $("#editSpinner").addClass('d-none');
                $('#ibox1').children(".ibox-content").toggleClass("sk-loading")
                let obj = {};
                try {
                    obj = JSON.parse(res);
                } catch (e) {}
                if (obj.error) {
                    toastr.error(obj.error.replace(/<[^>]*>?/gm, ''), 'Error');
                    return;
                }
                const p = obj.data;

                // basics
                $('#edit_product_id').val(p.id);
                $('#edit_product_name').val(p.product_name);
                $('#edit_generic').val(p.generic || '');

                // image preview
                if (p.image_url) {
                    $('#edit_imagePreview').html(`<img src="${p.image_url}" style="height:60px">`);
                }

                // prices: first row with + button
                const prices = Array.isArray(p.prices) && p.prices.length ? p.prices : [{
                    mrp: '',
                    tp: ''
                }];
                const first = prices[0];
                $("#edit-prices-wrapper").append(`
                    <div class="row price-row align-items-center mb-2">
                    <div class="col-5">
                        <input type="number" step="0.01" name="mrp[]" class="form-control" placeholder="MRP" value="${first.mrp ?? ''}" required>
                    </div>
                    <div class="col-5">
                        <input type="number" step="0.01" name="tp[]" class="form-control" placeholder="TP" value="${first.tp ?? ''}" required>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-block btn-primary add-price-edit">+</button>
                    </div>
                    </div>
                `);
                // rest with − button
                for (let i = 1; i < prices.length; i++) {
                    const pr = prices[i];
                    const tpl = document.getElementById('edit-price-row-tpl').content.cloneNode(true);
                    $(tpl).find('input[name="mrp[]"]').val(pr.mrp ?? '');
                    $(tpl).find('input[name="tp[]"]').val(pr.tp ?? '');
                    $("#edit-prices-wrapper").append(tpl);
                }

                // show modal
                $('#editProductModal').modal('show');
            },
            error: function() {
                $("#editSpinner").addClass('d-none');
                toastr.error('Failed to load product', 'Error');
            }
        });
    });

    // + / − buttons inside modal
    $(document).on('click', '.add-price-edit', function() {
        const tpl = document.getElementById('edit-price-row-tpl').content.cloneNode(true);
        $('#edit-prices-wrapper').append(tpl);
    });
    $(document).on('click', '#edit-prices-wrapper .remove-price', function() {
        $(this).closest('.price-row').remove();
    });

    // submit modal form
    $("#editForm").on("submit", function(e) {
        e.preventDefault();

        // build prices JSON for update
        const prices = [];
        const nowStr = new Date().toISOString().slice(0, 19).replace('T', ' ');
        $("#edit-prices-wrapper .price-row").each(function() {
            const mrp = $(this).find('input[name="mrp[]"]').val();
            const tp = $(this).find('input[name="tp[]"]').val();
            if (mrp || tp) prices.push({
                dated: nowStr,
                mrp: mrp ? parseFloat(mrp) : null,
                tp: tp ? parseFloat(tp) : null
            });
        });

        const fd = new FormData(this);
        fd.delete('mrp[]');
        fd.delete('tp[]');
        fd.append('prices', JSON.stringify(prices));

        $.ajax({
            url: "<?= base_url() . "product-update/"; ?>",
            type: "POST",
            data: fd,
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function() {
                $("#editSubmitBtn").prop("disabled", true);
                $("#editSpinner").removeClass('d-none');
                $("#editError").addClass('d-none').empty();
            },
            success: function(res) {
                $("#editSpinner").addClass('d-none');
                $("#editSubmitBtn").prop("disabled", false);

                let obj = {};
                try {
                    obj = JSON.parse(res);
                } catch (e) {}
                if (obj.error) {
                    $("#editError").html(obj.error).removeClass('d-none');
                    toastr.error("Please check errors list!", "Error");
                    return;
                }
                if (obj.success) {
                    toastr.success("Product updated", "Success");
                    $('#editProductModal').modal('hide');
                    if (window.productsDT) productsDT.ajax.reload(null, false);
                } else {
                    toastr.error("Unexpected response", "Error");
                }
            },
            error: function() {
                $("#editSpinner").addClass('d-none');
                $("#editSubmitBtn").prop("disabled", false);
                toastr.error("Error while sending request to server!", "Error");
            }
        });
    });
</script>