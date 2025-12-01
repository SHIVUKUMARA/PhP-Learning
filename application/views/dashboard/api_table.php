<?php
$this->load->view('partials/header', ['title' => 'API Users Table']);
?>

<div class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        <?php $this->load->view('navbar/headernav', ['user' => $logged_user]); ?>
        <?php $this->load->view('navbar/sidebar'); ?>

        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6 d-flex align-items-center">
                            <h3 class="mb-0 fw-bold">Users Information</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="<?= base_url('greet'); ?>">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Users Info</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid mt-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 text-center">Users List</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height:500px; overflow:auto;">
                            <table class="table table-striped table-hover align-middle text-center table-bordered" id="usersTable">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>ID</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Age</th>
                                        <th>Gender</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>DOB</th>
                                        <th>Role</th>
                                    </tr>
                                </thead>
                                <tbody id="usersTableBody">
                                    <tr>
                                        <td colspan="9" class="text-center">Loading users...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            <nav>
                                <ul class="pagination" id="pagination"></ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php $this->load->view('navbar/footer'); ?>
    </div>
</div>

<?php $this->load->view('partials/footer'); ?>

<script>
    $(document).ready(function() {

        const tableBody = $('#usersTableBody');
        const pagination = $('#pagination');
        const perPage = 50;
        let currentPage = 1;
        let users = [];

        function loadUsers() {
            $.ajax({
                url: "https://dummyjson.com/users?limit=250",
                method: "GET",
                dataType: "json",
                success: function(data) {
                    users = data.users || [];
                    render();
                },
                error: function() {
                    tableBody.html('<tr><td colspan="9" class="text-center text-danger">Failed to load users.</td></tr>');
                }
            });
        }

        function render() {
            renderTable();
            renderPagination();
        }

        function renderTable() {
            tableBody.html('');

            const start = (currentPage - 1) * perPage;
            const pageUsers = users.slice(start, start + perPage);

            if (!pageUsers.length) {
                tableBody.html('<tr><td colspan="9" class="text-center">No users found.</td></tr>');
                return;
            }

            $.each(pageUsers, function(i, u) {
                tableBody.append(`
                    <tr>
                        <td>${u.id}</td>
                        <td>${u.firstName}</td>
                        <td>${u.lastName}</td>
                        <td>${u.age}</td>
                        <td>${capitalize(u.gender)}</td>
                        <td>${u.email}</td>
                        <td>${formatPhone(u.phone)}</td>
                        <td>${u.birthDate}</td>
                        <td>${u.role || 'N/A'}</td>
                    </tr>
                `);
            });
        }

        function renderPagination() {
            const totalPages = Math.ceil(users.length / perPage);

            let html = '';

            if (currentPage > 1) {
                html += `<li class="page-item"><a href="#" class="page-link" id="prev">«</a></li>`;
            }

            html += `<li class="page-item active"><span class="page-link">${currentPage}</span></li>`;

            if (currentPage < totalPages) {
                html += `<li class="page-item"><a href="#" class="page-link" id="next">»</a></li>`;
            }

            pagination.html(html);

            $('#prev').click(function(e) {
                e.preventDefault();
                currentPage--;
                render();
            });

            $('#next').click(function(e) {
                e.preventDefault();
                currentPage++;
                render();
            });
        }

        function formatPhone(phone) {
            const d = phone.replace(/\D/g, '');
            return d.length === 10 ? d.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3') : phone;
        }

        function capitalize(t) {
            return t.charAt(0).toUpperCase() + t.slice(1);
        }

        loadUsers();
    });
</script>