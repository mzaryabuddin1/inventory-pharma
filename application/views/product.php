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