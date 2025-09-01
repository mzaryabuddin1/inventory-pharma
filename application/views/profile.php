<?php require_once("layout/header.php") ?>
<?php require_once("layout/sidebar.php") ?>
<?php require_once("layout/navbar.php") ?>


<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox">
        <div class="ibox-title">
          <h5>
            Profile Settings
          </h5>
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
              <label class="col-sm-2 col-form-label">First Name</label>
              <div class="col-sm-10">
                <input type="text" name="first_name" class="form-control"
                  value="<?= html_escape($user['first_name']) ?>">
              </div>
            </div>
            <div class="hr-line-dashed"></div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Last Name</label>
              <div class="col-sm-10">
                <input type="text" name="last_name" class="form-control"
                  value="<?= html_escape($user['last_name']) ?>">
              </div>
            </div>
            <div class="hr-line-dashed"></div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Email</label>
              <div class="col-sm-10">
                <input type="email" name="email" class="form-control" readonly
                  value="<?= html_escape($user['email']) ?>">
              </div>
            </div>
            <div class="hr-line-dashed"></div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Phone</label>
              <div class="col-sm-10">
                <input type="text" name="phone" class="form-control"
                  value="<?= html_escape($user['phone']) ?>">
              </div>
            </div>

            <div class="hr-line-dashed"></div>
            <div class="form-group row">
              <label class="col-sm-2 col-form-label">New Password</label>
              <div class="col-sm-10">
                <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep current password">
              </div>
            </div>

            <div class="hr-line-dashed"></div>



            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Profile Picture</label>
              <div class="col-sm-10">
                <?php if (!empty($user['profile_picture'])): ?>
                  <img src="<?= $user['profile_picture'] ?>" alt="Profile" style="height:60px">
                <?php endif; ?>
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
</div>




<?php require_once("layout/footer.php") ?>

<script>
  $("#theform").on("submit", function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    $.ajax({
      url: "<?= base_url() . "profile-submit"; ?>",
      type: "post",
      data: formData,
      processData: false, // tell jQuery not to process the data
      contentType: false, // tell jQuery not to set contentType
      cache: false,
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
            window.location = '<?php echo base_url() . 'profile' ?>';
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