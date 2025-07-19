<?php
include '../db/connection.php';

$query = $_POST['query'] ?? '';
$allLocation = $_POST['allLocation'] ?? '';

if ($query !== '') {
    $sql = "SELECT id, CONCAT(area, ', ', city, ', ', state) AS location 
            FROM locations 
            WHERE area LIKE ? OR city LIKE ? OR state LIKE ?";
    $stmt = $conn->prepare($sql);
    $like = '%' . $query . '%';
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<style>

        #suggestion-list {
           font-size: 13px;
            list-style: none;
            margin: 0 auto;
            padding: 0;
            border: 1px solid #ccc;
            border-right: 0px solid !important;
            border-left: 0px solid !important;
            border-radius: 0 0 8px 8px;
            background-color:white;
            width: 97%;
            max-height: 200px;
            overflow-y: auto;
            z-index: 10;
            box-shadow: 2px 0px 7.467px 0px rgba(108, 99, 99, 0.20);
        }


        #suggestion-list li {
            padding: 8px;
            cursor: pointer;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }

        .all-location {
            display: none;
        }
    </style>';


    echo '<ul id="suggestion-list">';
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<li id="list_location" data-id="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['location']) . '</li>';
        }
    } else {
        echo '<li style="padding-top: 10px;text-align: center; color: #999 !important;">No results found</li>';
    }
    echo '</ul>';

}
if ($allLocation == '' || $allLocation !== '') {
    $sql = "SELECT MIN(id) AS id, state,area, city FROM locations GROUP BY city, state ORDER BY state ASC, city ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo '<style>
        #suggestion-list {
           font-size: 13px;
            list-style: none;
            margin: 0 auto;
            padding: 0;
            border: 1px solid #ccc;
            border-right: 0px solid !important;
            border-left: 0px solid !important;
            border-radius: 0 0 8px 8px;
            background-color:white;
            width: 97%;
            max-height: 200px;
            overflow-y: auto;
            z-index: 10;
            box-shadow: 2px 0px 7.467px 0px rgba(108, 99, 99, 0.20);
        }
        
        
        
        #suggestion-list li {
        
            padding: 8px;
            cursor: pointer;
            text-align: left;
        }

    </style>';
    echo '<ul id="suggestion-list" class="all-location">';

if ($result->num_rows > 0) {
    $states = [];

    while ($row = $result->fetch_assoc()) {
        $state = htmlspecialchars($row['state']);
        $city = htmlspecialchars($row['city']);
        $area = htmlspecialchars($row['area']);
        $id = htmlspecialchars($row['id']);

        // Just collect all rows grouped by state
        if (!isset($states[$state])) {
            $states[$state] = [];
        }

        $states[$state][] = ['id' => $id, 'city' => $city, 'area' => $area];
    }

    foreach ($states as $stateName => $locations) {
        echo '<li class="state-name" style="font-weight:bold;margin-left:5px;text-align: left;color: black;">' . $stateName . '</li>';
        foreach ($locations as $location) {
            $area = $location['area'];
            $city = $location['city'];
            $displayText = !empty($area) ? "$area, $city" : $city;

            echo '<li id="list_location" class="city-name" style="margin-left:30px;text-align: left;" data-id="' . $location['id'] . '">' . $displayText . '</li>';
        }
    }
}

else {
          echo '<li style="padding-top: 10px;text-align: center; color: #999 !important;">No results found</li>';
    }

    echo '</ul>';
}

