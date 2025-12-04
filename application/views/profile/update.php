<?php
$data['title'] = "Update Profile | AdminLTE";
$data['body_class'] = "hold-transition sidebar-mini layout-fixed";
$this->load->view('partials/header', $data);
?>

<div class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">

        <!-- Header Nav -->
        <?php $this->load->view('navbar/headernav', ['user' => $user]); ?>

        <!-- Sidebar -->
        <?php $this->load->view('navbar/sidebar'); ?>

        <main class="app-main d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 56px); padding: 20px;">
            <div class="d-flex justify-content-center align-items-center w-100" style="min-height: 100%;">
                <div class="card shadow-lg border-0 rounded-lg text-center" style="width: 450px; background-color: #f8ea687a; position: relative; padding-top: 10px;">

                    <div class="position-relative">
                        <img src="<?= $user->avatar_url; ?>" class="rounded-circle border border-5 border-grey shadow-sm"
                            style="width:120px;height:120px;object-fit:cover;">
                    </div>

                    <div class="card-body p-4">

                        <div id="msg"></div>

                        <?= form_open_multipart('profile/update', ['id' => 'profileForm']) ?>
                        <input type="hidden" name="user_id" value="<?= $user->id ?>">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="fullname" name="fullname"
                                placeholder="Full Name" value="<?= set_value('fullname', $user->fullname); ?>" required>
                            <label for="fullname">Full Name</label>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="fname" name="fname"
                                        placeholder="First Name" value="<?= set_value('fname', $user->fname); ?>">
                                    <label for="fname">First Name</label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="lname" name="lname"
                                        placeholder="Last Name" value="<?= set_value('lname', $user->lname); ?>">
                                    <label for="lname">Last Name</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-floating mb-3">
                            <select id="status" name="status" class="form-select">
                                <option value="active" <?= ($user->status === 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?= ($user->status === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                            <label for="status">Status</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="Email" value="<?= set_value('email', $user->email); ?>" readonly>
                            <label for="email">Email</label>
                        </div>

                        <div class="form-floating mb-3">
                            <select id="role" name="role" class="form-select"
                                <?= ($this->session->userdata('role') !== 'admin') ? 'disabled' : ''; ?>>
                                <option value="customer" <?= ($user->role === 'customer') ? 'selected' : ''; ?>>Customer</option>
                                <option value="manager" <?= ($user->role === 'manager') ? 'selected' : ''; ?>>Manager</option>
                                <option value="admin" <?= ($user->role === 'admin') ? 'selected' : ''; ?>>Admin</option>
                            </select>
                            <label for="role">Role</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="tel" class="form-control" id="phone" name="phone"
                                placeholder="Enter phone number" maxlength="12" value="<?= set_value('phone', $user->phone_number); ?>" required>
                        </div>

                        <div class="mb-3 text-start">
                            <label for="profileimg" class="form-label fw-bold">Profile Image</label>
                            <sub class="fw-bold text-danger">(Accepted format: .jpg, .jpeg, .png, .gif)</sub>
                            <input type="file" name="userfile" accept=".jpg, .jpeg, .png, .gif" class="form-control" id="profileimg">
                        </div>

                        <div class="d-flex justify-content-center gap-2 mt-4">
                            <button type="submit" class="btn btn-success btn-lg shadow-sm">
                                <i class="bi bi-save me-1"></i> Save Changes
                            </button>
                            <a href="<?= site_url('dashboard/table'); ?>" class="btn btn-danger btn-lg shadow-sm">
                                <i class="bi bi-x-lg me-1"></i> Cancel
                            </a>
                        </div>

                        <?= form_close(); ?>

                    </div>

                    <div class="card-footer text-center" style="background-color: #f8ea687a;">
                        <small class="text-muted">Update user information anytime.</small>
                    </div>

                </div>
            </div>
        </main>

        <?php $this->load->view('navbar/footer'); ?>

    </div>
</div>

<?php $this->load->view('partials/footer'); ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@17.0.19/build/css/intlTelInput.min.css" />
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@17.0.19/build/js/intlTelInput.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@17.0.19/build/js/utils.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        const profileImgInput = document.querySelector('#profileimg');
        profileImgInput.addEventListener('change', function() {
            const [file] = this.files;
            if (file) {
                const img = document.querySelector('.position-relative img');
                img.src = URL.createObjectURL(file);
            }
        });

        profileImgInput.addEventListener('change', function() {
            const file = this.files[0];
            const maxSize = 5 * 1024 * 1024;
            if (file && file.size > maxSize) {
                alert('Error: File size should not exceed 5MB.');
                this.value = '';
            }
        });

        const phoneInput = document.querySelector("#phone");
        const iti = window.intlTelInput(phoneInput, {
            initialCountry: "in",
            separateDialCode: true,
            preferredCountries: ["in", "us"]
        });

        function formatPhone(value) {
            value = value.replace(/\D/g, '').substring(0, 10);
            if (value.length > 6) {
                return value.replace(/(\d{3})(\d{3})(\d{0,4})/, "$1-$2-$3");
            } else if (value.length > 3) {
                return value.replace(/(\d{3})(\d{0,3})/, "$1-$2");
            } else {
                return value;
            }
        }

        phoneInput.value = formatPhone(phoneInput.value);

        phoneInput.addEventListener('input', function() {
            const cursorPos = phoneInput.selectionStart;
            phoneInput.value = formatPhone(phoneInput.value);
            phoneInput.setSelectionRange(cursorPos, cursorPos);
        });

        $('#profileForm').on('submit', function(e) {
            e.preventDefault();

            var form = $(this)[0];
            var formData = new FormData(form);

            const countryData = iti.getSelectedCountryData();
            const nationalNumber = phoneInput.value.replace(/\D/g, '');

            formData.set('country_code', "+" + countryData.dialCode);
            formData.set('phone_number', nationalNumber);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#msg').html('<div class="alert alert-success">' + response.message + '</div>');

                        if (response.user.avatar_url) {
                            $('img.position-relative').attr('src', response.user.avatar_url);
                        }
                    } else {
                        $('#msg').html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                },
                error: function() {
                    $('#msg').html('<div class="alert alert-danger">Something went wrong!</div>');
                }
            });
        });

    });
</script>