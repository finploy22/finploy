<?php
session_start();
include '../db/connection.php';

if (isset($_SESSION['mobile'])) {
    $mobile = $_SESSION['mobile'];
}

$sql = "SELECT 
            id, user_id, username, mobile_number, gender,
            employed, current_company, sales_experience, destination, 
            work_experience, current_location, current_salary, resume, 
            products, sub_products, departments, sub_departments, 
            specialization, category, location_code, age
        FROM candidate_details 
        WHERE mobile_number = '$mobile'";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
}

$selected_sub_products = isset($row['sub_products']) ? explode(',', $row['sub_products']) : [];
$selected_sub_departments = isset($row['sub_departments']) ? explode(',', $row['sub_departments']) : [];
$selected_specializations = isset($row['specialization']) ? explode(',', $row['specialization']) : [];
$selected_categories = isset($row['category']) ? explode(',', $row['category']) : [];

// Decode incoming data
$productData = json_decode($_POST['selectedProducts'] ?? '', true);
$subProductData = json_decode($_POST['selectedSubProducts'] ?? '', true);
$departmentData = json_decode($_POST['selecteddepartments'] ?? '', true);
$subDepartmentData = json_decode($_POST['selectedSubdepartments'] ?? '', true);

// Return sub_products
if (is_array($productData)) {
    foreach ($productData as $product) {
        if ((int)$product['value'] !== 0) {
            $stmt = $conn->prepare("SELECT sub_product_id, sub_product_name FROM sub_products WHERE product_id = ?");
            $stmt->bind_param("i", $product['id']);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $checked = in_array($row['sub_product_id'], $selected_sub_products) ? 'checked' : '';
                echo '<div class="col-md-6">
                        <div class="form-check mb-2" style="display:flex; gap:6px;">
                            <input class="form-check-input border-primary" type="checkbox" name="sub_products" value="' . htmlspecialchars($row['sub_product_id']) . '" data-name="' . htmlspecialchars($row['sub_product_name']) . '" style="flex-shrink:0;" ' . $checked . '>
                            <label class="form-check-label">' . htmlspecialchars($row['sub_product_name']) . '</label>
                        </div>
                    </div>';
            }
        }
    }
}

// Return specialization
if (is_array($subProductData)) {
    foreach ($subProductData as $subproduct) {
        if ((int)$subproduct['value'] !== 0) {
            $stmt = $conn->prepare("SELECT specialization_id, specialization FROM products_specialization WHERE sub_product_id = ?");
            $stmt->bind_param("i", $subproduct['id']);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $checked = in_array($row['specialization_id'], $selected_specializations) ? 'checked' : '';
                echo '<div class="col-md-6">
                        <div class="form-check mb-2" style="display:flex; gap:6px;">
                            <input class="form-check-input border-success" type="checkbox" name="specialization" value="' . htmlspecialchars($row['specialization_id']) . '" data-name="' . htmlspecialchars($row['specialization']) . '" style="flex-shrink:0;" ' . $checked . '>
                            <label class="form-check-label">' . htmlspecialchars($row['specialization']) . '</label>
                        </div>
                    </div>';
            }
        }
    }
}

// Return sub_departments
if (is_array($departmentData)) {
    foreach ($departmentData as $department) {
        if ((int)$department['value'] !== 0) {
            $stmt = $conn->prepare("SELECT sub_department_id, sub_department_name FROM sub_departments WHERE department_id = ?");
            $stmt->bind_param("i", $department['id']);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $checked = in_array($row['sub_department_id'], $selected_sub_departments) ? 'checked' : '';
                echo '<div class="col-md-6">
                        <div class="form-check mb-2" style="display:flex; gap:6px;">
                            <input class="form-check-input border-success" type="checkbox" name="sub_departments" value="' . htmlspecialchars($row['sub_department_id']) . '" data-name="' . htmlspecialchars($row['sub_department_name']) . '" style="flex-shrink:0;" ' . $checked . '>
                            <label class="form-check-label">' . htmlspecialchars($row['sub_department_name']) . '</label>
                        </div>
                    </div>';
            }
        }
    }
}

// Return category
if (is_array($subDepartmentData)) {
    foreach ($subDepartmentData as $subdepartment) {
        if ((int)$subdepartment['value'] !== 0) {
            $stmt = $conn->prepare("SELECT category_id, category FROM departments_category WHERE sub_department_id = ?");
            $stmt->bind_param("i", $subdepartment['id']);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $checked = in_array($row['category_id'], $selected_categories) ? 'checked' : '';
                echo '<div class="col-md-6">
                        <div class="form-check mb-2" style="display:flex; gap:6px;">
                            <input class="form-check-input border-success" type="checkbox" name="category" value="' . htmlspecialchars($row['category_id']) . '" data-name="' . htmlspecialchars($row['category']) . '" style="flex-shrink:0;" ' . $checked . '>
                            <label class="form-check-label">' . htmlspecialchars($row['category']) . '</label>
                        </div>
                    </div>';
            }
        }
    }
}
?>
