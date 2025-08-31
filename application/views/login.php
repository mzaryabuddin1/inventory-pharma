<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= PROJECT_NAME ?></title>
    <meta name="description" content="<?= PROJECT_DESCRIPTION ?>" />

    <link href="<?= base_url() ?>theme/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url() ?>theme/font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="<?= base_url() ?>theme/css/animate.css" rel="stylesheet">
    <link href="<?= base_url() ?>theme/css/style.css" rel="stylesheet">

</head>

<body class="gray-bg">

    <div class="loginColumns animated fadeInDown">
        <div class="row">

            <div class="col-md-6">
                <h2 class="font-bold">Welcome to Inventory Pharma</h2>

                <p>
                    A clean, purpose-built admin for pharmaceutical inventory—add products, register suppliers, and manage batch/expiry details with confidence.
                </p>

                <p>
                    Streamline your day-to-day: purchase receiving, sales billing, returns, GRN, and automated stock adjustments—tracked to the exact lot.
                </p>

                <p>
                    Fast invoicing with taxes and discounts, barcodes and SKUs supported, and role-based access to keep sensitive data secure.
                </p>

                <p>
                    Real-time stock levels, minimum/maximum alerts, and expiry notifications so you never run out—or sell expired items.
                </p>

                <p>
                    Powerful ledgers and reconciliations: customer and supplier statements, cash/bank books, and audit logs for every transaction.
                </p>

                <p>
                    Insightful reports—sales, purchases, profitability, and aging—exportable to PDF/Excel for easy sharing and compliance.
                </p>

                <p>
                    <small>
                        Built for pharmacy workflows: batch tracking, controlled medicines handling, multi-store support, and GST/Sales Tax ready.
                    </small>
                </p>

            </div>
            <div class="col-md-6">
                <div class="ibox-content">
                    <form class="m-t" role="form" id="theform">
                        <div class="alert alert-danger d-none" id="error"></div>
                        <div class="form-group">
                            <input type="email" class="form-control" placeholder="Email" required="" name="email">
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" placeholder="Password" required="" name="password">
                        </div>
                        <div class="sk-spinner sk-spinner-wave d-none" id="spinner">
                            <div class="sk-rect1"></div>
                            <div class="sk-rect2"></div>
                            <div class="sk-rect3"></div>
                            <div class="sk-rect4"></div>
                            <div class="sk-rect5"></div>
                        </div>
                        <button type="submit" class="btn btn-primary block full-width m-b">Login</button>

                        <a href="<?= base_url() ?>forgot-password">
                            <small>Forgot password?</small>
                        </a>

                        <p class="text-muted text-center">
                            <small>Do not have an account?</small>
                        </p>
                        <a class="btn btn-sm btn-white btn-block" href="<?= base_url() ?>register">Create an account</a>
                    </form>
                </div>
            </div>
        </div>
        <hr />
        <div class="row">
            <div class="col-md-6">
                Copyright Liveasoft
            </div>
            <div class="col-md-6 text-right">
                <small>© 2025</small>
            </div>
        </div>
    </div>

    <script src="<?= base_url() ?>theme/js/jquery-3.1.1.min.js"></script>

    <script>
        $("#theform").on("submit", function(e) {
            e.preventDefault();

            $.ajax({
                url: "<?= base_url() . "login-submit"; ?>",
                type: "post",
                data: $(this).serialize(),
                // processData: false, // tell jQuery not to process the data
                // contentType: false, // tell jQuery not to set contentType
                // cache: false,
                beforeSend: function() {
                    $(":submit").prop("disabled", true);
                    $(":submit").addClass("d-none");
                    $("#spinner").removeClass("d-none");
                    $("#error").addClass("d-none");
                },
                success: function(res) {

                    console.log(res)
                    // let obj = JSON.parse(res);
                    // if (obj.error) {
                    //     $("#error").html(obj.error);
                    //     $("#error").removeClass("d-none");
                    //     $("#spinner").addClass("d-none");
                    //     $(":submit").removeClass("d-none");
                    //     toastr.error("Please check errors list!", "Error");
                    //     $(window).scrollTop(0);
                    // } else if (obj.success) {
                    //     $("#spinner").addClass("d-none");
                    //     toastr.success("Success!", "Hurray");
                    //     setTimeout(function() {
                    //         window.location = '<?php echo base_url() . 'customer-settings' ?>';
                    //     }, 1000);
                    // } else {
                    //     $("#spinner").addClass("d-none");
                    //     $(":submit").prop("disabled", false);
                    //     $(":submit").removeClass("d-none");
                    //     toastr.error("Something bad happened!", "Error");
                    //     $(window).scrollTop(0);
                    // }
                    // $(":submit").prop("disabled", false);
                },
                error: function(error) {
                    // toastr.error("Error while sending request to server!", "Error");
                    // $(window).scrollTop(0);
                    // $("#spinner").addClass("d-none");
                    // $(":submit").prop("disabled", false);
                    // $(":submit").removeClass("d-none");
                }
            })
        });
    </script>

</body>

</html>