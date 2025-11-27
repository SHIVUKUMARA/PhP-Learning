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
    document.addEventListener('DOMContentLoaded', () => {
        const tableBody = document.getElementById('usersTableBody');
        const pagination = document.getElementById('pagination');
        const perPage = 50;
        let currentPage = 1;
        let users = [];

        function renderTable(page) {
            tableBody.innerHTML = '';
            const start = (page - 1) * perPage;
            const end = start + perPage;
            const pagedUsers = users.slice(start, end);

            if (pagedUsers.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="9" class="text-center">No users found.</td></tr>';
                return;
            }

            pagedUsers.forEach(user => {
                const row = document.createElement('tr');
                row.innerHTML = `
                <td>${user.id}</td>
                <td>${user.firstName}</td>
                <td>${user.lastName}</td>
                <td>${user.age}</td>
                <td>${capitalize(user.gender)}</td>
                <td>${user.email}</td>
                <td>${formatPhone(user.phone)}</td>
                <td>${user.birthDate}</td>
                <td>${user.role || 'N/A'}</td>
            `;
                tableBody.appendChild(row);
            });

            renderPagination(page);
        }

        function renderPagination(page) {
            pagination.innerHTML = '';
            const totalPages = Math.ceil(users.length / perPage);

            const createPageItem = (p, text = p, active = false) => {
                const li = document.createElement('li');
                li.className = 'page-item' + (active ? ' active' : '');
                li.innerHTML = `<a class="page-link" href="#">${text}</a>`;
                li.addEventListener('click', e => {
                    e.preventDefault();
                    currentPage = p;
                    renderTable(currentPage);
                });
                return li;
            };

            if (page > 1) pagination.appendChild(createPageItem(page - 1, '«'));
            for (let i = 1; i <= totalPages; i++) {
                pagination.appendChild(createPageItem(i, i, i === page));
            }
            if (page < totalPages) pagination.appendChild(createPageItem(page + 1, '»'));
        }

        function formatPhone(phone) {
            const digits = phone.replace(/\D/g, '');
            return digits.length === 10 ? digits.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3') : phone;
        }

        function capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        fetch('https://dummyjson.com/users?limit=250')
            .then(res => res.json())
            .then(data => {
                users = data.users || [];
                renderTable(currentPage);
            })
            .catch(err => {
                tableBody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Failed to load users.</td></tr>';
                console.error(err);
            });
    });
</script>