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

    <div class="passwordBox animated fadeInDown">
        <div class="row">

            <div class="col-md-12">
                <div class="ibox-content">

                    <h2 class="font-bold">Forgot password</h2>

                    <p>
                        Enter your email address and your password will be reset and emailed to you.
                    </p>

                    <div class="row">

                        <div class="col-lg-12">
                            <form class="m-t" role="form" id="theform">
                                <div class="alert alert-danger d-none" id="error"></div>

                                <div class="form-group">
                                    <input type="email" class="form-control" placeholder="Email address" required="" name="email">
                                </div>
                                <div class="sk-spinner sk-spinner-wave d-none" id="spinner">
                                    <div class="sk-rect1"></div>
                                    <div class="sk-rect2"></div>
                                    <div class="sk-rect3"></div>
                                    <div class="sk-rect4"></div>
                                    <div class="sk-rect5"></div>
                                </div>
                                <button type="submit" class="btn btn-primary block full-width m-b">Send new password</button>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr />
        <div class="row">
            <div class="col-md-6">
                Liveasoft
            </div>
            <div class="col-md-6 text-right">
                <small>© 2025</small>
            </div>
        </div>
    </div>



    <script src="<?= base_url() ?>theme/js/jquery-3.1.1.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        // Set the options that I want
        toastr.options = {
            "closeButton": true,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    </script>

    <script>
        $("#theform").on("submit", function(e) {
            e.preventDefault();

            $.ajax({
                url: "<?= base_url() . "forgot-password-submit"; ?>",
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
                    let obj = JSON.parse(res);
                    if (obj.error) {
                        $("#error").html(obj.error);
                        $("#error").removeClass("d-none");
                        $("#spinner").addClass("d-none");
                        $(":submit").removeClass("d-none");
                        toastr.error("Please check errors list!", "Error");
                        $(window).scrollTop(0);
                    } else if (obj.success) {
                        $("#spinner").addClass("d-none");
                        toastr.success("Success!", "Hurray");
                        setTimeout(function() {
                            window.location = '<?php echo base_url() ?>';
                        }, 1000);
                    } else {
                        $("#spinner").addClass("d-none");
                        $(":submit").prop("disabled", false);
                        $(":submit").removeClass("d-none");
                        toastr.error("Something bad happened!", "Error");
                        $(window).scrollTop(0);
                    }
                    $(":submit").prop("disabled", false);
                },
                error: function(error) {
                    toastr.error("Error while sending request to server!", "Error");
                    $(window).scrollTop(0);
                    $("#spinner").addClass("d-none");
                    $(":submit").prop("disabled", false);
                    $(":submit").removeClass("d-none");
                }
            })
        });
    </script>

</body>

</html>