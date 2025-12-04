<?php
$title = "AdminLTE 4 | Register Page";
$body_class = "hold-transition register-page";
$this->load->view('partials/header');
?>

<div class="register-box">
  <div class="register-logo">
    <a href="#"><b>Admin</b>LTE</a>
  </div>

  <div class="card">
    <div class="card-body register-card-body">

      <div id="ajaxMessage"></div>

      <p class="register-box-msg">Register a new membership</p>

      <?= form_open('auth/register_submit', ['id' => 'registerForm']); ?>
      <div class="input-group mb-3">
        <input type="text" name="fullname" class="form-control" placeholder="Full Name" required />
        <div class="input-group-text"><span class="bi bi-person"></span></div>
      </div>

      <div class="input-group mb-3">
        <input type="email" name="email" class="form-control" placeholder="Email" required />
        <div class="input-group-text"><span class="bi bi-envelope"></span></div>
      </div>

      <div class="input-group mb-3">
        <input type="password" name="password" class="form-control" placeholder="Password" required />
        <div class="input-group-text"><span class="bi bi-lock-fill"></span></div>
      </div>

      <div class="row">
        <div class="col-8">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="agreeTerms" name="agree_terms" value="1" required />
            <label class="form-check-label" for="agreeTerms">
              I agree to the <a href="#">terms</a>
            </label>
          </div>
        </div>
        <div class="col-4">
          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">Register</button>
          </div>
        </div>
      </div>
      </form>

      <div class="social-auth-links text-center mb-3 d-grid gap-2">
        <p>- OR -</p>
        <a href="#" class="btn btn-primary">
          <i class="bi bi-facebook me-2"></i> Sign in using Facebook
        </a>
        <a href="#" class="btn btn-danger">
          <i class="bi bi-google me-2"></i> Sign in using Google+
        </a>
      </div>

      <p class="mb-0">
        <a href="<?= site_url('auth/login'); ?>" class="text-center">I already have a membership</a>
      </p>
    </div>
  </div>
</div>

<?php $this->load->view('partials/footer'); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    $('#registerForm').on('submit', function(e) {
      e.preventDefault();

      var formData = $(this).serialize();

      $('#ajaxMessage').html('');
      $('button[type="submit"]').prop('disabled', true);

      $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(data) {
          if (data.status === 'success') {
            $('#ajaxMessage').html('<div class="alert alert-success">' + data.message + '</div>');
            setTimeout(function() {
              window.location.href = '<?= site_url("auth/login"); ?>';
            }, 2000);
          } else {
            $('#ajaxMessage').html('<div class="alert alert-danger">' + data.message + '</div>');
          }
        },
        error: function(xhr, status, error) {
          $('#ajaxMessage').html('<div class="alert alert-danger">Something went wrong. Please try again.</div>');
        },
        complete: function() {
          $('button[type="submit"]').prop('disabled', false);
        }
      });
    });
  });
</script>