<?php
// candidate_search.php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Include your database connection file
include '../db/connection.php';

if (isset($_GET['keyword'])) {
    $keyword = trim($_GET['keyword']);
    
    // If the keyword is empty, return an empty JSON array
    if (empty($keyword)) {
        echo json_encode([]);
        exit;
    }
    
    // Prepare the search term for the LIKE query
    $searchTerm = "%" . $keyword . "%";
    
    // Define all searchable fields - make sure all fields are included
    $searchableFields = [
        'username', 'gender', 'employed', 'current_company', 
        'sales_experience', 'destination', 'work_experience', 'current_location', 
        'current_salary', 'hl_lap', 'personal_loan', 'business_loan', 
        'education_loan', 'credit_cards', 'gold_loan', 'casa', 'others', 'companyname', 
        'location', 'Sales', 'salary', 'Credit_dept', 'HR_Training', 
        'Legal_compliance_Risk', 'Operations', 'Others1'
    ];
    
    // Build the SQL query to search across all fields
    $sql = "SELECT id, " . implode(", ", $searchableFields) . " FROM candidate_details WHERE ";
    
    // Add conditions for each field
    $conditions = [];
    $params = [];
    $types = "";
    
    foreach ($searchableFields as $field) {
        $conditions[] = "$field LIKE ?";
        $params[] = $searchTerm;
        $types .= "s"; // All parameters are strings
    }
    
    $sql .= implode(" OR ", $conditions);
    
    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        // Dynamically bind parameters
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Fetch results
        $candidates = $result->fetch_all(MYSQLI_ASSOC);
        
        // Process to find matching fields for each candidate
        $matchResults = [];
        
        // Define mapping for special fields to their text names
        $specialFields = [
            'hl_lap' => 'HL LAP',
            'business_loan' => 'Business Loan',
            'gold_loan' => 'Gold Loan',
            'casa' => 'CASA',
            'personal_loan' => 'Personal Loan',
            'education_loan' => 'Education Loan',
            'credit_cards' => 'Credit Cards',
            'Sales' => 'Sales',
            'Credit_dept' => 'Credit Dept',
            'HR_Training' => 'HR / Training',
            'Operations' => 'Operations',
            'Legal_compliance_Risk' => 'Legal/Compliance/Risk'
        ];
        
        foreach ($candidates as $candidate) {
            foreach ($searchableFields as $field) {
                // Skip empty fields or null values
                if (empty($candidate[$field])) {
                    continue;
                }
                
                // Check if this field contains the search term (case insensitive)
                if (stripos($candidate[$field], $keyword) !== false) {
                    // Add this field match to results
                    $fieldValue = $candidate[$field];
                    
                    // For special fields with "yes" status, return the text name
                    if (isset($specialFields[$field]) && 
                        strtolower($fieldValue) === 'yes') {
                        $fieldValue = $specialFields[$field];
                    }
                    
                    $matchResults[] = [
                        'id' => $candidate['id'],
                        'field' => $field,
                        'value' => $fieldValue,
                        'candidate_name' => $candidate['username'] // Include the candidate name for reference
                    ];
                }
            }
            
            // Additionally, check all special fields for "yes" status
            // even if they don't match the search keyword
            foreach ($specialFields as $field => $textName) {
                if (isset($candidate[$field]) && 
                    strtolower($candidate[$field]) === 'yes' && 
                    stripos($textName, $keyword) !== false) {
                    
                    $matchResults[] = [
                        'id' => $candidate['id'],
                        'field' => $field,
                        'value' => $textName,
                        'candidate_name' => $candidate['username']
                    ];
                }
            }
        }
        
        // Return the results
        header('Content-Type: application/json');
        echo json_encode($matchResults);
        
        $stmt->close();
    } else {
        echo json_encode(["error" => "Query preparation failed: " . $conn->error]);
    }
}
$conn->close();
?>