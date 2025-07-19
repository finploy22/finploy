<?php
include '../db/connection.php';

$department_id = $_POST['department_id'] ?? '';
$sub_department_id = $_POST['sub_department_id'] ?? '';

$product_id = $_POST['product_id'] ?? '';
$sub_product_id = $_POST['sub_product_id'] ?? '';


// Process departments if available
if (!empty($department_id)) {
    $sql = "SELECT sub_department_name, sub_department_id FROM sub_departments WHERE department_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $department_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0): ?>
        <label class="form-label">Sub - Department</label>
        <select class="form-select Department" id="sub-departments" name="sub_department">
            <option selected>Select Sub Department</option>
            <?php while ($row = $result->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($row['sub_department_id']) ?>" data-name="<?= htmlspecialchars($row['sub_department_name']) ?>">
                    <?= htmlspecialchars($row['sub_department_name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <?php else: ?>
        <label class="form-label">Sub - Department</label>
        <select class="form-select Department" id="sub-departments" name="sub_department">
             <option selected>Select Sub Department</option>
        </select>
        <?php endif; 
}

if (!empty($sub_department_id)) {
  $sql = "SELECT category, category_id  FROM departments_category WHERE sub_department_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $sub_department_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0): ?>
        <label class="form-label">Category</label>
      <select class="form-select Department" id="department_category" name="department_category">
            <option selected>Select Category</option>
            <?php while ($row = $result->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($row['category_id']) ?>" data-name="<?= htmlspecialchars($row['category']) ?>">
                    <?= htmlspecialchars($row['category']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <?php else: ?>
        <label class="form-label">Sub Category</label>
        <select class="form-select Department" id="department_category" name="department_category">
             <option selected>Select Category</option>
        </select>
    <?php endif;
}


// Process products if available
if (!empty($product_id)) {
    $sql = "SELECT sub_product_name, sub_product_id FROM sub_products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0): ?>
        <label class="form-label">Sub - Product</label>
        <select class="form-select Product" id="sub-products" name="sub_product">
            <option selected>Select Sub Product</option>
            <?php while ($row = $result->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($row['sub_product_id']) ?>" data-name="<?= htmlspecialchars($row['sub_product_name']) ?>">
                    <?= htmlspecialchars($row['sub_product_name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <?php else: ?>
            <label class="form-label">Sub - Product</label>
            <select class="form-select Product" id="sub-products" name="sub_product">
                 <option selected>Select Sub Product</option>
            </select>
        <?php endif; 
}

if (!empty($sub_product_id)) {
    $sql = "SELECT specialization, specialization_id  FROM products_specialization WHERE sub_product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $sub_product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0): ?>
        <label class="form-label">Specialization</label>
     <select class="form-select Product" id="product_specialization" name="product_specialization">
            <option selected>Select Specialization</option>
            <?php while ($row = $result->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($row['specialization_id']) ?>" data-name="<?= htmlspecialchars($row['specialization']) ?>">
                    <?= htmlspecialchars($row['specialization']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <?php else: ?>
            <label class="form-label">Specialization</label>
            <select class="form-select Product" id="product_specialization" name="product_specialization">
                 <option selected>Select Specialization</option>
            </select>
           <!--<input type="text" class="form-control specialization"  name="product_specialization" id="product_specialization" placeholder="Enter specialization"autocomplete="off">-->

    <?php endif;
}

