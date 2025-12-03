<div class="dropdown d-inline-block">
    <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-filter"></i>
    </button>
    <div class="dropdown-menu p-3" style="min-width: 250px; position: relative; overflow: auto;
    max-height: 500px;">
        <form class="column-search-form d-flex flex-column" data-column="<?= $field_name ?>">
            <div class="mb-2">
                <label class="form-label">Field</label>
                <input type="text" class="form-control form-control-sm" value="<?= $label ?>" disabled>
            </div>
            <div class="mb-2">
                <label class="form-label">Select Operator</label>
                <select name="operator" class="form-select form-select-sm">
                    <option value="equals" <?= ($search_operator == 'equals') ? 'selected' : '' ?>>Equals</option>
                    <option value="not_equals" <?= ($search_operator == 'not_equals') ? 'selected' : '' ?>>Not Equals</option>
                    <option value="contains" <?= ($search_operator == 'contains' || !$search_operator) ? 'selected' : '' ?>>Contains</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Value</label>
                <input type="text" class="form-control form-control-sm" name="value" placeholder="Enter value" value="<?= htmlspecialchars($search_value ?? '') ?>">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">Apply</button>
                <button type="button" class="btn btn-secondary btn-sm flex-fill" data-bs-dismiss="dropdown">Cancel</button>
            </div>
        </form>
    </div>
</div>