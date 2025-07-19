<?php
include 'db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_stateid'])) {
    $stateId = intval($_POST['ajax_stateid']);

    $sql = "SELECT DISTINCT city, city_wise_id FROM locations WHERE state_wise_id = $stateId ORDER BY city ASC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $cities = [];
        while ($row = $result->fetch_assoc()) {
            $cities[] = $row;
        }

        $cityNames = array_column($cities, 'city');
        $chunks = array_chunk($cityNames, 7);
        $columnGroups = array_chunk($chunks, 5);

        foreach ($columnGroups as $group) {
            echo '<div class="column-group">';
            foreach ($group as $column_data) {
                echo '<ul class="job-column">';
                foreach ($column_data as $cityName) {
                    foreach ($cities as $loc) {
                        if ($loc['city'] === $cityName) {
                            $city_wise_id = $loc['city_wise_id'];
                            break;
                        }
                    }
                    $slug = strtolower(str_replace(' ', '-', $cityName));
                    $url = "/jobs-in-{$slug}";
                    echo "<li><a href='{$url}'>Jobs in {$cityName}</a></li>";
                }
                echo '</ul>';
            }
            echo '</div>';
        }
    } else {
        echo "<p>No cities found for this state.</p>";
    }
}
?>