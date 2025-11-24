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

      <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
      <?php endif; ?>

      <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
      <?php endif; ?>

      <p class="register-box-msg">Register a new membership</p>

      <?= form_open('auth/register_submit'); ?>
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