<?php

/**
 * $field_name: The DB column name (e.g., 'id', 'fullname')
 * $label: Label to show in the dropdown (e.g., 'ID', 'Full Name')
 * $search_value: current search value (optional)
 * $search_operator: current search operator (optional)
 */
?>

<div class="dropdown d-inline-block">
    <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-filter"></i>
    </button>
    <div class="dropdown-menu p-3" style="min-width: 250px;">
        <form class="column-search-form d-flex flex-column" data-column="<?= $field_name ?>">
            <!-- Field Name (Disabled) -->
            <div class="mb-2">
                <label class="form-label">Field</label>
                <input type="text" class="form-control form-control-sm" value="<?= $label ?>" disabled>
            </div>
            <!-- Operator Selector -->
            <div class="mb-2">
                <label class="form-label">Operator</label>
                <select name="operator" class="form-select form-select-sm">
                    <option value="equals" <?= ($search_operator == 'equals') ? 'selected' : '' ?>>Equals</option>
                    <option value="not_equals" <?= ($search_operator == 'not_equals') ? 'selected' : '' ?>>Not Equals</option>
                    <option value="contains" <?= ($search_operator == 'contains' || !$search_operator) ? 'selected' : '' ?>>Contains</option>
                </select>
            </div>
            <!-- Input Value -->
            <div class="mb-3">
                <label class="form-label">Value</label>
                <input type="text" class="form-control form-control-sm" name="value" placeholder="Enter value" value="<?= htmlspecialchars($search_value ?? '') ?>">
            </div>
            <!-- Buttons side by side -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">Apply</button>
                <button type="button" class="btn btn-secondary btn-sm flex-fill" data-bs-dismiss="dropdown">Cancel</button>
            </div>
        </form>
    </div>
</div>