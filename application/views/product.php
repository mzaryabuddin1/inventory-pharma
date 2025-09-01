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
                    <button class="btn btn-primary collapse-link">Click To Add Product</button>
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
                                <input type="text" name="first_name" class="form-control">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Generic</label>
                            <div class="col-sm-10">
                                <input type="text" name="last_name" class="form-control">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Prices</label>
                            <div class="col-sm-10">
                                <div class="row">
                                    <div class="col-5">
                                        <input type="number" name="mrp[]" class="form-control" placeholder="MRP" >
                                    </div>
                                    <div class="col-5">
                                        <input type="number" name="tp[]" class="form-control" placeholder="TP" >
                                    </div>
                                    <div class="col-2">
                                        <button class="btn btn-block btn-primary">+</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5">
                                        <input type="number" name="mrp[]" class="form-control" placeholder="MRP" >
                                    </div>
                                    <div class="col-5">
                                        <input type="number" name="tp[]" class="form-control" placeholder="TP" >
                                    </div>
                                    <div class="col-2">
                                        <button class="btn btn-block btn-outline-danger">-</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5">
                                        <input type="number" name="mrp[]" class="form-control" placeholder="MRP" >
                                    </div>
                                    <div class="col-5">
                                        <input type="number" name="tp[]" class="form-control" placeholder="TP" >
                                    </div>
                                    <div class="col-2">
                                        <button class="btn btn-block btn-outline-danger">-</button>
                                    </div>
                                </div>
                            </div>
                        </div>
           

                        <div class="hr-line-dashed"></div>



                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Product Picture</label>
                            <div class="col-sm-10">
                                <input type="file" name="profile_picture" class="form-control">
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
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
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
                    <div class="table-responsive">
                        <table
                            class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Generic</th>
                                    <th>MRP</th>
                                    <th>TP</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><img src="https://purepharmacy.ie/cdn/shop/products/panadol-film-coated-tablets-500mg-24-pack_large.jpg" width="50px"></td>
                                    <td>Panadol</td>
                                    <td>Paracetamol</td>
                                    <td>100</td>
                                    <td>130</td>
                                    <td>2023-09-01 00:00:00</td>
                                    <td></td>
                                </tr>

                            </tbody>
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
    $(document).ready(function() {
        $(".dataTables-example").DataTable({
            pageLength: 25,
            responsive: true,
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [{
                    extend: "copy"
                },
                {
                    extend: "csv"
                },
                {
                    extend: "excel",
                    title: "ExampleFile"
                },
                {
                    extend: "pdf",
                    title: "ExampleFile"
                },

                {
                    extend: "print",
                    customize: function(win) {
                        $(win.document.body).addClass("white-bg");
                        $(win.document.body).css("font-size", "10px");

                        $(win.document.body)
                            .find("table")
                            .addClass("compact")
                            .css("font-size", "inherit");
                    },
                },
            ],
        });
    });
</script>