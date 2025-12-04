<?php
$data['title'] = "AdminLTE 4 | Dashboard";
$this->load->view('partials/header', $data);
?>

<div class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">

        <?php $this->load->view('navbar/headernav'); ?>
        <?php $this->load->view('navbar/sidebar'); ?>

        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <?php if ($this->session->userdata('role') === 'admin'): ?>
                            <div class="col-sm-6 d-flex align-items-center">
                                <label for="roleFilter" class="me-2 mb-0 fw-bold">List View</label>
                                <select class="form-select w-auto" id="roleFilter" name="roleFilter" style="min-width:150px;">
                                    <option value="" <?= $roleFilter == '' ? 'selected' : '' ?>>All Records</option>
                                    <option value="admin" <?= $roleFilter == 'admin' ? 'selected' : '' ?>>Admin Records</option>
                                    <option value="manager" <?= $roleFilter == 'manager' ? 'selected' : '' ?>>Manager Records</option>
                                    <option value="customer" <?= $roleFilter == 'customer' ? 'selected' : '' ?>>Customer Records</option>
                                </select>
                            </div>
                        <?php else: ?>
                            <div class="col-sm-6 d-flex align-items-center">
                                <h3 class="mb-0 fw-bold">Users Information</h3>
                            </div>
                        <?php endif; ?>

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
                        <div class="table-responsive" style="max-height:500px; overflow:auto; min-height:350px;">
                            <table class="table table-striped table-hover align-middle text-center table-bordered">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <?php
                                        $columns = [
                                            ['field' => 'id', 'label' => 'ID'],
                                            ['field' => 'fullname', 'label' => 'Full Name'],
                                            ['field' => 'fname', 'label' => 'First Name'],
                                            ['field' => 'lname', 'label' => 'Last Name'],
                                            ['field' => 'email', 'label' => 'Email'],
                                            ['field' => 'status', 'label' => 'Status'],
                                            ['field' => 'role', 'label' => 'Role'],
                                            ['field' => 'country_code', 'label' => 'Country'],
                                            ['field' => 'phone_number', 'label' => 'Ph No.'],
                                            ['field' => 'created_at', 'label' => 'Registered on'],
                                            ['field' => 'last_updated', 'label' => 'Updated on'],
                                        ];
                                        foreach ($columns as $col): ?>
                                            <th style="width:182px;">
                                                <?= $col['label'] ?>
                                                <?php $this->load->view('dashboard/filter', [
                                                    'field_name' => $col['field'],
                                                    'label' => $col['label'],
                                                    'search_value' => $search_column == $col['field'] ? $search_value : '',
                                                    'search_operator' => $search_column == $col['field'] ? $search_operator : ''
                                                ]); ?>
                                                <div class="resizer"></div>
                                            </th>
                                        <?php endforeach; ?>
                                        <th style="width:150px;">
                                            Actions
                                            <div class="resizer"></div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($users)): ?>
                                        <?php $i = $offset + 1; ?>
                                        <?php foreach ($users as $u): ?>
                                            <tr>
                                                <td><?= 'VV-' . sprintf('%04d', $u->id); ?></td>
                                                <td><?= htmlspecialchars(ucwords(strtolower($u->fullname))); ?></td>
                                                <td><?= htmlspecialchars(ucwords(strtolower($u->fname ?? ''))); ?></td>
                                                <td><?= htmlspecialchars(ucwords(strtolower($u->lname ?? ''))); ?></td>

                                                <td><?= htmlspecialchars($u->email); ?></td>
                                                <td>
                                                    <div class="form-check form-switch d-inline-block">
                                                        <input class="form-check-input status-switch" type="checkbox" <?= $u->status == 'active' ? 'checked' : ''; ?>>
                                                    </div>
                                                </td>
                                                <td><?= htmlspecialchars(ucfirst($u->role)); ?></td>
                                                <td><?= htmlspecialchars($u->country_code ?? 'NA') ?></td>
                                                <td><?= htmlspecialchars(format_phone($u->phone_number ?? 'NA')) ?></td>
                                                <td><?= date('M d, Y - h:i A', strtotime($u->created_at)); ?></td>
                                                <td><?= date('M d, Y - h:i A', strtotime($u->last_updated)); ?></td>
                                                <td>
                                                    <?php if ($u->can_view || $u->can_edit || $u->can_delete): ?>
                                                        <?php if ($u->can_view): ?>
                                                            <a href="<?= base_url('dashboard/view_user/' . $u->id); ?>" class="btn btn-sm btn-primary me-1"><i class="bi bi-eye"></i></a>
                                                        <?php endif; ?>

                                                        <?php if ($u->can_edit): ?>
                                                            <a href="<?= base_url('dashboard/edit_user/' . $u->id); ?>" class="btn btn-sm btn-info me-1"><i class="bi bi-pencil-square"></i></a>
                                                        <?php endif; ?>

                                                        <?php if ($u->can_delete): ?>
                                                            <a href="<?= base_url('dashboard/delete_user/' . $u->id); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');"><i class="bi bi-trash"></i></a>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">No Access</span>
                                                    <?php endif; ?>
                                                </td>

                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="10" class="text-center">The Searched Data Didn't found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-center">
                        <?= $this->pagination->create_links(); ?>
                    </div>
                </div>
            </div>
        </main>

        <?php $this->load->view('navbar/footer'); ?>

    </div>
</div>
<?php
function format_phone($number)
{
    $digits = preg_replace('/\D/', '', $number);
    if (strlen($digits) === 10) {
        return preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1-$2-$3', $digits);
    }
    return $number;
}
?>


<?php $this->load->view('partials/footer'); ?>

<script>
    $('#roleFilter').on('change', function() {
        const role = $(this).val();
        const urlParams = new URLSearchParams(window.location.search);
        if (role) {
            urlParams.set('role', role);
        } else {
            urlParams.delete('role');
        }
        urlParams.delete('per_page');

        window.location.href = '<?= base_url("dashboard/table") ?>?' + urlParams.toString();
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const table = document.querySelector('.table');
        const cols = table.querySelectorAll('th');

        cols.forEach(function(th, index) {
            const resizer = th.querySelector('.resizer');
            if (!resizer) return;

            resizer.addEventListener('mousedown', initResize);

            function initResize(e) {
                e.preventDefault();
                let startX = e.pageX;
                let startWidth = th.offsetWidth;

                function onMouseMove(e) {
                    let newWidth = startWidth + (e.pageX - startX);
                    if (newWidth > 50) {
                        th.style.width = newWidth + 'px';

                        table.querySelectorAll('tbody tr').forEach(tr => {
                            tr.children[index].style.width = newWidth + 'px';
                        });
                    }
                }

                function stopResize() {
                    document.removeEventListener('mousemove', onMouseMove);
                    document.removeEventListener('mouseup', stopResize);
                }

                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', stopResize);
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('.column-search-form').on('submit', function(e) {
            e.preventDefault();
            const column = $(this).data('column');
            const operator = $(this).find('select[name="operator"]').val();
            const value = $(this).find('input[name="value"]').val().trim();

            if (!value) {
                alert('Please enter a value to search.');
                return;
            }

            let url = '<?= base_url("dashboard/table") ?>';
            url += `?search_column=${column}&search_operator=${operator}&search_value=${encodeURIComponent(value)}`;

            const roleFilter = $('#roleFilter').val();
            if (roleFilter) {
                url += `&role=${roleFilter}`;
            }

            window.location.href = url;
        });

        $(document).on('input', '.column-search-form input[name="value"]', function() {
            const words = $(this).val().trim().split(/\s+/);
            if (words.length > 10) {
                $(this).val(words.slice(0, 10).join(' '));
            }
        });
    });
</script>