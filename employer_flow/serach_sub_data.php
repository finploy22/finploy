<?php
include '../db/connection.php';

// Reusable function to render checkboxes
function renderCheckboxResults($result, $valueKey, $labelKey, $nameAttr) {
    if ($result && $result->num_rows > 0) {
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        foreach ($rows as $row) {
            ?>
            <div class="custom-checkbox checkbox-item" style="display:flex;gap:6px">
                <input class="checkbox-input" type="checkbox"
                       name="<?= htmlspecialchars($nameAttr); ?>"
                       value="<?= htmlspecialchars($row[$valueKey]); ?>"
                       data-name="<?= htmlspecialchars($row[$labelKey]); ?>">
                <label class="filter-lable checkbox-label"><?= htmlspecialchars($row[$labelKey]); ?></label>
            </div>
            <?php
        }
    } else {
        echo '<p style="padding-top: 10px;text-align: center; color: #999;">No results found</p>';
    }
}

// Handling different AJAX searches
if (isset($_POST['departmentsearch'])) {
    $search = $conn->real_escape_string($_POST['departmentsearch']);
    $sql = "SELECT * FROM departments WHERE department_name LIKE '%$search%' ORDER BY department_name ASC";
    $result = $conn->query($sql);
    renderCheckboxResults($result, 'department_id', 'department_name', 'department');
}

if (isset($_POST['subdepartmentsearch'])) {
    $search = $conn->real_escape_string($_POST['subdepartmentsearch']);
    $sql = "SELECT * FROM sub_departments WHERE sub_department_name LIKE '%$search%' ORDER BY sub_department_name ASC";
    $result = $conn->query($sql);
    renderCheckboxResults($result, 'sub_department_id', 'sub_department_name', 'subdepartment');
}

if (isset($_POST['categorysearch'])) {
    $search = $conn->real_escape_string($_POST['categorysearch']);
    $sql = "SELECT * FROM departments_category WHERE category LIKE '%$search%' ORDER BY category ASC";
    $result = $conn->query($sql);
    renderCheckboxResults($result, 'category_id', 'category', 'category');
}

if (isset($_POST['productsearch'])) {
    $search = $conn->real_escape_string($_POST['productsearch']);
    $sql = "SELECT * FROM products WHERE product_name LIKE '%$search%' ORDER BY product_name ASC";
    $result = $conn->query($sql);
    renderCheckboxResults($result, 'product_id', 'product_name', 'product');
}

if (isset($_POST['subproductsearch'])) {
    $search = $conn->real_escape_string($_POST['subproductsearch']);
    $sql = "SELECT * FROM sub_products WHERE sub_product_name LIKE '%$search%' ORDER BY sub_product_name ASC";
    $result = $conn->query($sql);
    renderCheckboxResults($result, 'sub_product_id', 'sub_product_name', 'subproduct');
}

if (isset($_POST['specializationsearch'])) {
    $search = $conn->real_escape_string($_POST['specializationsearch']);
    $sql = "SELECT * FROM products_specialization WHERE specialization LIKE '%$search%' ORDER BY specialization ASC";
    $result = $conn->query($sql);
    renderCheckboxResults($result, 'specialization_id', 'specialization', 'specialization');
}
?>
