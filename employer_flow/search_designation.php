<?php
include '../db/connection.php';

$query = $_POST['query'] ?? '';

if ($query !== '') {
    $sql = "SELECT DISTINCT jobrole FROM job_id WHERE jobrole LIKE ?";
    $stmt = $conn->prepare($sql);
    $like = '%' . $query . '%';
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<style>
        #suggestion-designation-list {
            font-size: 13px;
            list-style: none;
            margin: 0 auto;
            padding: 0;
            border: 1px solid #ccc;
            border-right: 0 !important;
            border-left: 0 !important;
            border-radius: 0 0 8px 8px;
            background-color: white;
            width: 97%;
            max-height: 200px;
            overflow-y: auto;
            z-index: 10;
            box-shadow: 2px 0px 7.467px 0px rgba(108, 99, 99, 0.20);
            margin-top:-10px;
        }

        #suggestion-designation-list li {
            
            padding: 8px;
            cursor: pointer;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }
    </style>';

    echo '<ul id="suggestion-designation-list">';
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<li id="list_designation" data-id="' . htmlspecialchars($row['jobrole']) . '">' . htmlspecialchars($row['jobrole']) . '</li>';
        }
    } else {
         echo '<li style="padding-top: 10px;text-align: center; color: #999 !important;">No results found</li>';
    }
    echo '</ul>';
}
