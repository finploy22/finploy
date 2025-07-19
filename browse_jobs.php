<?php
include 'header.php';
include 'db/connection.php';
$sql = "SELECT id,area,city,state,city_wise_id,state_wise_id FROM locations ORDER BY state";


$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row_locations = $result->fetch_all(MYSQLI_ASSOC);
}
// Fect Jobrole and company from job_id table 
$sql = "SELECT jobrole,companyname  FROM job_id ORDER BY jobrole ,companyname";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row_job_id = $result->fetch_all(MYSQLI_ASSOC);
}
// Load dropdown filters for product, subproduct, department, subdepartment.. 
function fetchTable($conn, $table)
{
    $result = $conn->query("SELECT * FROM {$table}");
    return ($result && $result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

$row_products = fetchTable($conn, 'products');
$row_sub_products = fetchTable($conn, 'sub_products');
$row_products_specialization = fetchTable($conn, 'products_specialization');
$row_departments = fetchTable($conn, 'departments');
$row_sub_departments = fetchTable($conn, 'sub_departments');
$row_departments_category = fetchTable($conn, 'departments_category');

$flow = $_GET['flow'] ?? null;

?>
<style>
    .index-body {
        background-image: url(images/img-browse-job.png) !important;
        background-size: cover !important;
        background-position: 0% 100% !important;
        background-repeat: no-repeat !important;
    }

    h1 {
        color: #FFF;
        font-size: 32px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        text-align: center;
        padding: 55px;
    }

    .switch-btn-div {
        text-align: center;
        margin: 40px 0;
    }

    .browse-job-btn.active,
    .hire-job-btn.active {
        color: #175DA8;
        background-color: #FCFDFD;
        border-radius: 8px;
        border: 0px;
        font-size: 14px;
        font-weight: 600;
        width: 216px;
        margin: 3px 0;
    }

    .browse-job-btn,
    .hire-job-btn {
        color: #FCFDFD;
        background-color: #4EA647;
        border-radius: 8px;
        border: 0px;
        font-size: 14px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        width: 216px;
        margin: 3px 0;

    }

    .btn-div {
        width: 40%;
        background-color: #4EA647;
        margin: auto;
        border-radius: 8px;
        box-shadow: 1.95px 1.95px 2.6px 0px rgba(0, 0, 0, 0.15);
    }

    .section-filter-row1 {
        display: flex;
        justify-content: space-between;
        border-bottom: 0.6px solid #C0C0C0;
    }

    .section-filter-row2 {
        display: flex;
        justify-content: space-between;
        border-bottom: 0.6px solid #C0C0C0;
        width: 66%;
        margin: auto;
    }

    .filter-word {
        color: #5C788A;
        cursor: pointer;
        font-size: 14px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
        margin-bottom: 0px;
        margin-top: 20px;
        padding-bottom: 6px;
    }

    .filter-word.active {
        border-bottom: 1px solid;
        color: var(--Deeesha-Blue, #175DA8);

        font-size: 15px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .alpa-filter {
        display: flex;
        justify-content: space-between;
        margin: 30px 0;
        border-bottom: 0.6px solid #C0C0C0;
    }

    .alpa-filter-word.active {
        color: var(--Deeesha-Blue, #175DA8);

        font-size: 15px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
    }

    .alpa-filter-word {
        color: #5C788A;
        cursor: pointer;
        font-size: 14px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
    }

    .all-jobs {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 25px;
    }

    .column-group {
        display: flex;
        justify-content: space-between;
        gap: 20px;
    }

    .job-column {
        list-style-type: none;
        padding-left: 0;
        margin: 0;
    }

    .job-column li {
        margin-bottom: 8px;
    }

    .job-column a {
        color: #5C788A;
        font-family: Poppins, sans-serif;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
    }

    .filter-div-company {
        display: none;
    }

    
    @media (max-width: 768px) {
    .btn-div {
        width: 100% !important;
        background-color: #4EA647;
        margin: auto;
        border-radius: 8px;
        box-shadow: 1.95px 1.95px 2.6px 0px rgba(0, 0, 0, 0.15);
    }
    .section-filter-row1 {
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        align-items: center !important;
        border-bottom: none !important;
    }
    .section-filter-row2 {
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        align-items: center !important;
    }
    .column-group {
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        margin: 0 auto !important;
        gap: 0 !important;
    }
    }
</style>

<body>
    <div class="index-body">
        <h1>Browse Jobs</h1>
    </div>
    <div class="container">
        <div class="switch-btn-div">
            <div class="btn-div">
                <button class="browse-job-btn active">Jobs for Candidates</button>
                <button class="hire-job-btn">Hiring for Companies</button>
            </div>
        </div>
        <div class="filter-div-candidate">
            <div class="section-filter-row1">
                <p class="filter-word active">Jobs by Location</p>
                <p class="filter-word">Jobs at Company</p>
                <p class="filter-word">Jobs by Department</p>
                <p class="filter-word">Jobs by Sub-Department</p>
                <p class="filter-word">Jobs by Categories</p>
                <!-- <p class="filter-word">Jobs by Skill</p> -->
            </div>
            <div class="section-filter-row2">
                <p class="filter-word">Jobs by Product</p>
                <p class="filter-word">Jobs by Sub-Product</p>
                <p class="filter-word">Jobs by Specialization</p>
                <p class="filter-word">Jobs by Designation</p>
                <p class="filter-word home-btn">View All Jobs</p>
            </div>
            <div class="alpa-filter">
                <p class="alpa-filter-word active">Filter by</p>
                <p class="alpa-filter-word">A</p>
                <p class="alpa-filter-word">B</p>
                <p class="alpa-filter-word">C</p>
                <p class="alpa-filter-word">D</p>
                <p class="alpa-filter-word">E</p>
                <p class="alpa-filter-word">F</p>
                <p class="alpa-filter-word">G</p>
                <p class="alpa-filter-word">H</p>
                <p class="alpa-filter-word">I</p>
                <p class="alpa-filter-word">J</p>
                <p class="alpa-filter-word">K</p>
                <p class="alpa-filter-word">L</p>
                <p class="alpa-filter-word">M</p>
                <p class="alpa-filter-word">N</p>
                <p class="alpa-filter-word">O</p>
                <p class="alpa-filter-word">P</p>
                <p class="alpa-filter-word">Q</p>
                <p class="alpa-filter-word">R</p>
                <p class="alpa-filter-word">S</p>
                <p class="alpa-filter-word">T</p>
                <p class="alpa-filter-word">U</p>
                <p class="alpa-filter-word">V</p>
                <p class="alpa-filter-word">W</p>
                <p class="alpa-filter-word">X</p>
                <p class="alpa-filter-word">Y</p>
                <p class="alpa-filter-word">Z</p>
                <p class="alpa-filter-word">&#8377;</p>
                <p class="alpa-filter-word">0-9</p>
            </div>
            <div class="all-jobs all-job-location">
                <?php
                if ($row_locations) {
                    $printed_states = [];
                    $columns = [];
                    $column = [];

                    foreach ($row_locations as $location) {
                        if (!in_array($location['state'], $printed_states)) {
                            $printed_states[] = $location['state'];
                        }
                    }
                    $chunks = array_chunk($printed_states, 7);
                    $column_groups = array_chunk($chunks, 5);
                    foreach ($column_groups as $group) {
                        echo '<div class="column-group">';
                        foreach ($group as $column_data) {
                            echo '<ul class="job-column">';
                            foreach ($column_data as $state) {
                                foreach ($row_locations as $loc) {
                                    if ($loc['state'] === $state) {
                                        $state_wise_id = $loc['state_wise_id'];
                                        break;
                                    }
                                }
                                $slug = strtolower(str_replace(' ', '-', $state));
                                $url = "/jobs-in-{$slug}";
                                echo "<li><a id='state-name' data-stateid={$state_wise_id} href='javascript:void(0)'>Jobs in {$state}</a></li>";
                            }
                            echo '</ul>';
                        }
                        echo '</div>';
                    }
                }
                ?>
            </div>
             <div class="all-jobs all-job-location-citys"></div>
            <div class="all-jobs all-job-company" style="display:none;">
                <?php
                if ($row_job_id) {
                    $printed_companies = [];
                    $columns = [];
                    $column = [];

                    foreach ($row_job_id as $job) {
                        if (!in_array($job['companyname'], $printed_companies)) {
                            $printed_companies[] = $job['companyname'];
                        }
                    }

                    $chunks = array_chunk($printed_companies, 7);
                    $column_groups = array_chunk($chunks, 5);
                    foreach ($column_groups as $group) {
                        echo '<div class="column-group">';
                        foreach ($group as $column_data) {
                            echo '<ul class="job-column">';
                            foreach ($column_data as $company) {
                                $slug = strtolower(str_replace(' ', '-', $company));
                                $url = "{$slug}";
                                echo "<li><a id='company-name' data-companyname=\"{$company}\" href='{$url}'>{$company}</a></li>";
                            }
                            echo '</ul>';
                        }
                        echo '</div>';
                    }
                }
                ?>

            </div>
            <div class="all-jobs all-job-department" style="display:none;">
                <?php
                if ($row_departments) {
                    $printed = [];
                    foreach ($row_departments as $item) {
                        if (!in_array($item['department_name'], $printed)) {
                            $printed[] = $item['department_name'];
                        }
                    }
                    $chunks = array_chunk($printed, 7);
                    $column_groups = array_chunk($chunks, 5);
                    foreach ($column_groups as $group) {
                        echo '<div class="column-group">';
                        foreach ($group as $column_data) {
                            echo '<ul class="job-column">';
                            foreach ($column_data as $label) {
                                foreach ($row_departments as $d) {
                                    if ($d['department_name'] === $label) {
                                        $id = $d['department_id'];
                                        break;
                                    }
                                }
                                $slug = strtolower(str_replace(' ', '-', $label));
                                echo "<li><a id='department-name' data-departmentid='{$id}' href='{$slug}'>{$label}</a></li>";
                            }
                            echo '</ul>';
                        }
                        echo '</div>';
                    }
                }
                ?>
            </div>
            <div class="all-jobs all-job-sub-department" style="display:none;">
                <?php
                if ($row_sub_departments) {
                    $printed = [];
                    foreach ($row_sub_departments as $item) {
                        if (!in_array($item['sub_department_name'], $printed)) {
                            $printed[] = $item['sub_department_name'];
                        }
                    }
                    $chunks = array_chunk($printed, 7);
                    $column_groups = array_chunk($chunks, 5);
                    foreach ($column_groups as $group) {
                        echo '<div class="column-group">';
                        foreach ($group as $column_data) {
                            echo '<ul class="job-column">';
                            foreach ($column_data as $label) {
                                foreach ($row_sub_departments as $sd) {
                                    if ($sd['sub_department_name'] === $label) {
                                        $id = $sd['sub_department_id'];
                                        break;
                                    }
                                }
                                $slug = strtolower(str_replace(' ', '-', $label));
                                echo "<li><a id='sub-department-name' data-subdepartmentid='{$id}' href='{$slug}'> {$label}</a></li>";
                            }
                            echo '</ul>';
                        }
                        echo '</div>';
                    }
                }
                ?>
            </div>
            <div class="all-jobs all-job-categories" style="display:none;">
                <?php
                if ($row_departments_category) {
                    $printed = [];
                    foreach ($row_departments_category as $item) {
                        if (!in_array($item['category'], $printed)) {
                            $printed[] = $item['category'];
                        }
                    }
                    $chunks = array_chunk($printed, 7);
                    $column_groups = array_chunk($chunks, 5);
                    foreach ($column_groups as $group) {
                        echo '<div class="column-group">';
                        foreach ($group as $column_data) {
                            echo '<ul class="job-column">';
                            foreach ($column_data as $label) {
                                foreach ($row_departments_category as $cat) {
                                    if ($cat['category'] === $label) {
                                        $id = $cat['category_id'];
                                        break;
                                    }
                                }
                                $slug = strtolower(str_replace(' ', '-', $label));
                                echo "<li><a id='category-name' data-categoryid='{$id}' href='{$slug}'>{$label}</a></li>";
                            }
                            echo '</ul>';
                        }
                        echo '</div>';
                    }
                }
                ?>
            </div>
            <div class="all-jobs all-job-skill" style="display:none;">
                <?php
                if ($row_locations) {
                    $printed_states = [];
                    $columns = [];
                    $column = [];

                    foreach ($row_locations as $location) {
                        if (!in_array($location['state'], $printed_states)) {
                            $printed_states[] = $location['state'];
                        }
                    }
                    $chunks = array_chunk($printed_states, 7);
                    $column_groups = array_chunk($chunks, 5);
                    foreach ($column_groups as $group) {
                        echo '<div class="column-group">';
                        foreach ($group as $column_data) {
                            echo '<ul class="job-column">';
                            foreach ($column_data as $state) {
                                foreach ($row_locations as $loc) {
                                    if ($loc['state'] === $state) {
                                        $state_wise_id = $loc['state_wise_id'];
                                        break;
                                    }
                                }
                                $slug = strtolower(str_replace(' ', '-', $state));
                                $url = "/jobs-in-{$slug}";
                                echo "<li><a id='state-name' data-stateid={$state_wise_id} href='javascript:void(0)'>Jobs in {$state}</a></li>";
                            }
                            echo '</ul>';
                        }
                        echo '</div>';
                    }
                }
                ?>
            </div>
            <div class="all-jobs all-job-product" style="display:none;">
                <?php
                if ($row_products) {
                    $printed = [];
                    foreach ($row_products as $item) {
                        if (!in_array($item['product_name'], $printed)) {
                            $printed[] = $item['product_name'];
                        }
                    }
                    $chunks = array_chunk($printed, 7);
                    $column_groups = array_chunk($chunks, 5);
                    foreach ($column_groups as $group) {
                        echo '<div class="column-group">';
                        foreach ($group as $column_data) {
                            echo '<ul class="job-column">';
                            foreach ($column_data as $label) {
                                foreach ($row_products as $p) {
                                    if ($p['product_name'] === $label) {
                                        $id = $p['product_id'];
                                        break;
                                    }
                                }
                                $slug = strtolower(str_replace(' ', '-', $label));
                                echo "<li><a id='product-name' data-productid='{$id}' href='{$slug}'> {$label}</a></li>";
                            }
                            echo '</ul>';
                        }
                        echo '</div>';
                    }
                }
                ?>
            </div>
            <div class="all-jobs all-job-sub-product" style="display:none;">
                <?php
                if ($row_sub_products) {
                    $printed = [];
                    foreach ($row_sub_products as $item) {
                        if (!in_array($item['sub_product_name'], $printed)) {
                            $printed[] = $item['sub_product_name'];
                        }
                    }
                    $chunks = array_chunk($printed, 7);
                    $column_groups = array_chunk($chunks, 5);
                    foreach ($column_groups as $group) {
                        echo '<div class="column-group">';
                        foreach ($group as $column_data) {
                            echo '<ul class="job-column">';
                            foreach ($column_data as $label) {
                                foreach ($row_sub_products as $sp) {
                                    if ($sp['sub_product_name'] === $label) {
                                        $id = $sp['sub_product_id'];
                                        break;
                                    }
                                }
                                $slug = strtolower(str_replace(' ', '-', $label));
                                echo "<li><a id='sub-product-name' data-subproductid='{$id}' href='{$slug}'> {$label}</a></li>";
                            }
                            echo '</ul>';
                        }
                        echo '</div>';
                    }
                }
                ?>
            </div>
            <div class="all-jobs all-job-specialization" style="display:none;">
                <?php
                if ($row_products_specialization) {
                    $printed = [];
                    foreach ($row_products_specialization as $item) {
                        if (!in_array($item['specialization'], $printed)) {
                            $printed[] = $item['specialization'];
                        }
                    }
                    $chunks = array_chunk($printed, 7);
                    $column_groups = array_chunk($chunks, 5);
                    foreach ($column_groups as $group) {
                        echo '<div class="column-group">';
                        foreach ($group as $column_data) {
                            echo '<ul class="job-column">';
                            foreach ($column_data as $label) {
                                foreach ($row_products_specialization as $spec) {
                                    if ($spec['specialization'] === $label) {
                                        $id = $spec['specialization_id'];
                                        break;
                                    }
                                }
                                $slug = strtolower(str_replace(' ', '-', $label));
                                echo "<li><a id='specialization-name' data-specializationid='{$id}' href='{$slug}'> {$label}</a></li>";
                            }
                            echo '</ul>';
                        }
                        echo '</div>';
                    }
                }
                ?>
            </div>
            <div class="all-jobs all-job-designation" style="display:none;">
                <?php
                if ($row_job_id) {
                    $printed_designations = [];
                    $columns = [];
                    $column = [];

                    foreach ($row_job_id as $job) {
                        if (!in_array($job['jobrole'], $printed_designations)) {
                            $printed_designations[] = $job['jobrole'];
                        }
                    }

                    $chunks = array_chunk($printed_designations, 7);
                    $column_groups = array_chunk($chunks, 5);
                    foreach ($column_groups as $group) {
                        echo '<div class="column-group">';
                        foreach ($group as $column_data) {
                            echo '<ul class="job-column">';
                            foreach ($column_data as $jobrole) {
                                $slug = strtolower(str_replace(' ', '-', $jobrole));
                                $url = "{$slug}";
                                echo "<li><a id='jobrole-name' data-jobrole=\"{$jobrole}\" href='{$url}'>{$jobrole}</a></li>";
                            }
                            echo '</ul>';
                        }
                        echo '</div>';
                    }
                }
                ?>
            </div>

        </div>


        <div class="filter-div-company">
            <div class="section-filter-row1">
                <p class="filter-word active">Hire by Location</p>
                <p class="filter-word">Hire at Company</p>
                <p class="filter-word">Hire by Department</p>
                <p class="filter-word">Hire by Sub-Department</p>
                <p class="filter-word">Hire by Categories</p>
                <!-- <p class="filter-word">Hire by Skill</p> -->
            </div>
            <div class="section-filter-row2">
                <p class="filter-word">Hire by Product</p>
                <p class="filter-word">Hire by Sub-Product</p>
                <p class="filter-word">Hire by Specialization</p>
                <p class="filter-word">Hire by Designation</p>
                <p class="filter-word home-btn">View All Jobs</p>
            </div>
            <div class="alpa-filter">
                <p class="alpa-filter-word active">Filter by</p>
                <p class="alpa-filter-word">A</p>
                <p class="alpa-filter-word">B</p>
                <p class="alpa-filter-word">C</p>
                <p class="alpa-filter-word">D</p>
                <p class="alpa-filter-word">E</p>
                <p class="alpa-filter-word">F</p>
                <p class="alpa-filter-word">G</p>
                <p class="alpa-filter-word">H</p>
                <p class="alpa-filter-word">I</p>
                <p class="alpa-filter-word">J</p>
                <p class="alpa-filter-word">K</p>
                <p class="alpa-filter-word">L</p>
                <p class="alpa-filter-word">M</p>
                <p class="alpa-filter-word">N</p>
                <p class="alpa-filter-word">O</p>
                <p class="alpa-filter-word">P</p>
                <p class="alpa-filter-word">Q</p>
                <p class="alpa-filter-word">R</p>
                <p class="alpa-filter-word">S</p>
                <p class="alpa-filter-word">T</p>
                <p class="alpa-filter-word">U</p>
                <p class="alpa-filter-word">V</p>
                <p class="alpa-filter-word">W</p>
                <p class="alpa-filter-word">X</p>
                <p class="alpa-filter-word">Y</p>
                <p class="alpa-filter-word">Z</p>
                <p class="alpa-filter-word">&#8377;</p>
                <p class="alpa-filter-word">0-9</p>
            </div>
            <div class="all-jobs all-job-location">
                <?php
                if ($row_locations) {
                    $printed_states = [];
                    $columns = [];
                    $column = [];

                    foreach ($row_locations as $location) {
                        if (!in_array($location['state'], $printed_states)) {
                            $printed_states[] = $location['state'];
                        }
                    }
                    $chunks = array_chunk($printed_states, 7);
                    $column_groups = array_chunk($chunks, 5);
                    foreach ($column_groups as $group) {
                        echo '<div class="column-group">';
                        foreach ($group as $column_data) {
                            echo '<ul class="job-column">';
                            foreach ($column_data as $state) {
                                foreach ($row_locations as $loc) {
                                    if ($loc['state'] === $state) {
                                        $state_wise_id = $loc['state_wise_id'];
                                        break;
                                    }
                                }
                                $slug = strtolower(str_replace(' ', '-', $state));
                                $url = "/jobs-in-{$slug}";
                                echo "<li><a id='state-name' data-stateid={$state_wise_id} href='javascript:void(0)'>Hire in {$state}</a></li>";
                            }
                            echo '</ul>';
                        }
                        echo '</div>';
                    }
                }
                ?>
            </div>
            <div class="all-jobs all-job-company" style="display:none;">
                <?php
                if ($row_job_id) {
                    $printed_companies = [];
                    $columns = [];
                    $column = [];

                    foreach ($row_job_id as $job) {
                        if (!in_array($job['companyname'], $printed_companies)) {
                            $printed_companies[] = $job['companyname'];
                        }
                    }

                    $chunks = array_chunk($printed_companies, 7);
                    $column_groups = array_chunk($chunks, 5);
                    foreach ($column_groups as $group) {
                        echo '<div class="column-group">';
                        foreach ($group as $column_data) {
                            echo '<ul class="job-column">';
                            foreach ($column_data as $company) {
                                $slug = strtolower(str_replace(' ', '-', $company));
                                $url = "{$slug}";
                                echo "<li><a id='company-name' data-companyname=\"{$company}\" href='{$url}'>{$company}</a></li>";
                            }
                            echo '</ul>';
                        }
                        echo '</div>';
                    }
                }
                ?>

            </div>
            <div class="all-jobs all-job-department" style="display:none;">
                <?php
                if ($row_departments) {
                    $printed = [];
                    foreach ($row_departments as $item) {
                        if (!in_array($item['department_name'], $printed)) {
                            $printed[] = $item['department_name'];
                        }
                    }
                    $chunks = array_chunk($printed, 7);
                    $column_groups = array_chunk($chunks, 5);
                    foreach ($column_groups as $group) {
                        echo '<div class="column-group">';
                        foreach ($group as $column_data) {
                            echo '<ul class="job-column">';
                            foreach ($column_data as $label) {
                                foreach ($row_departments as $d) {
                                    if ($d['department_name'] === $label) {
                                        $id = $d['department_id'];
                                        break;
                                    }
                                }
                                $slug = strtolower(str_replace(' ', '-', $label));
                                echo "<li><a id='department-name' data-departmentid='{$id}' href='{$slug}'>{$label}</a></li>";
                            }
                            echo '</ul>';
                        }
                        echo '</div>';
                    }
                }
                ?>
            </div>
            <div class="all-jobs all-job-sub-department" style="display:none;">
                <?php
                if ($row_sub_departments) {
                    $printed = [];
                    foreach ($row_sub_departments as $item) {
                        if (!in_array($item['sub_department_name'], $printed)) {
                            $printed[] = $item['sub_department_name'];
                        }
                    }
                    $chunks = array_chunk($printed, 7);
                    $column_groups = array_chunk($chunks, 5);
                    foreach ($column_groups as $group) {
                        echo '<div class="column-group">';
                        foreach ($group as $column_data) {
                            echo '<ul class="job-column">';
                            foreach ($column_data as $label) {
                                foreach ($row_sub_departments as $sd) {
                                    if ($sd['sub_department_name'] === $label) {
                                        $id = $sd['sub_department_id'];
                                        break;
                                    }
                                }
                                $slug = strtolower(str_replace(' ', '-', $label));
                                echo "<li><a id='sub-department-name' data-subdepartmentid='{$id}' href='{$slug}'> {$label}</a></li>";
                            }
                            echo '</ul>';
                        }
                        echo '</div>';
                    }
                }
                ?>
            </div>
            <div class="all-jobs all-job-categories" style="display:none;">
                <?php
                if ($row_departments_category) {
                    $printed = [];
                    foreach ($row_departments_category as $item) {
                        if (!in_array($item['category'], $printed)) {
                            $printed[] = $item['category'];
                        }
                    }
                    $chunks = array_chunk($printed, 7);
                    $column_groups = array_chunk($chunks, 5);
                    foreach ($column_groups as $group) {
                        echo '<div class="column-group">';
                        foreach ($group as $column_data) {
                            echo '<ul class="job-column">';
                            foreach ($column_data as $label) {
                                foreach ($row_departments_category as $cat) {
                                    if ($cat['category'] === $label) {
                                        $id = $cat['category_id'];
                                        break;
                                    }
                                }
                                $slug = strtolower(str_replace(' ', '-', $label));
                                echo "<li><a id='category-name' data-categoryid='{$id}' href='{$slug}'>{$label}</a></li>";
                            }
                            echo '</ul>';
                        }
                        echo '</div>';
                    }
                }
                ?>
            </div>
            <div class="all-jobs all-job-skill" style="display:none;">
                <?php
                if ($row_locations) {
                    $printed_states = [];
                    $columns = [];
                    $column = [];

                    foreach ($row_locations as $location) {
                        if (!in_array($location['state'], $printed_states)) {
                            $printed_states[] = $location['state'];
                        }
                    }
                    $chunks = array_chunk($printed_states, 7);
                    $column_groups = array_chunk($chunks, 5);
                    foreach ($column_groups as $group) {
                        echo '<div class="column-group">';
                        foreach ($group as $column_data) {
                            echo '<ul class="job-column">';
                            foreach ($column_data as $state) {
                                foreach ($row_locations as $loc) {
                                    if ($loc['state'] === $state) {
                                        $state_wise_id = $loc['state_wise_id'];
                                        break;
                                    }
                                }
                                $slug = strtolower(str_replace(' ', '-', $state));
                                $url = "/jobs-in-{$slug}";
                                echo "<li><a id='state-name' data-stateid={$state_wise_id} href='javascript:void(0)'>Jobs in {$state}</a></li>";
                            }
                            echo '</ul>';
                        }
                        echo '</div>';
                    }
                }
                ?>
            </div>
            <div class="all-jobs all-job-product" style="display:none;">
                <?php
                if ($row_products) {
                    $printed = [];
                    foreach ($row_products as $item) {
                        if (!in_array($item['product_name'], $printed)) {
                            $printed[] = $item['product_name'];
                        }
                    }
                    $chunks = array_chunk($printed, 7);
                    $column_groups = array_chunk($chunks, 5);
                    foreach ($column_groups as $group) {
                        echo '<div class="column-group">';
                        foreach ($group as $column_data) {
                            echo '<ul class="job-column">';
                            foreach ($column_data as $label) {
                                foreach ($row_products as $p) {
                                    if ($p['product_name'] === $label) {
                                        $id = $p['product_id'];
                                        break;
                                    }
                                }
                                $slug = strtolower(str_replace(' ', '-', $label));
                                echo "<li><a id='product-name' data-productid='{$id}' href='{$slug}'> {$label}</a></li>";
                            }
                            echo '</ul>';
                        }
                        echo '</div>';
                    }
                }
                ?>
            </div>
            <div class="all-jobs all-job-sub-product" style="display:none;">
                <?php
                if ($row_sub_products) {
                    $printed = [];
                    foreach ($row_sub_products as $item) {
                        if (!in_array($item['sub_product_name'], $printed)) {
                            $printed[] = $item['sub_product_name'];
                        }
                    }
                    $chunks = array_chunk($printed, 7);
                    $column_groups = array_chunk($chunks, 5);
                    foreach ($column_groups as $group) {
                        echo '<div class="column-group">';
                        foreach ($group as $column_data) {
                            echo '<ul class="job-column">';
                            foreach ($column_data as $label) {
                                foreach ($row_sub_products as $sp) {
                                    if ($sp['sub_product_name'] === $label) {
                                        $id = $sp['sub_product_id'];
                                        break;
                                    }
                                }
                                $slug = strtolower(str_replace(' ', '-', $label));
                                echo "<li><a id='sub-product-name' data-subproductid='{$id}' href='{$slug}'> {$label}</a></li>";
                            }
                            echo '</ul>';
                        }
                        echo '</div>';
                    }
                }
                ?>
            </div>
            <div class="all-jobs all-job-specialization" style="display:none;">
                <?php
                if ($row_products_specialization) {
                    $printed = [];
                    foreach ($row_products_specialization as $item) {
                        if (!in_array($item['specialization'], $printed)) {
                            $printed[] = $item['specialization'];
                        }
                    }
                    $chunks = array_chunk($printed, 7);
                    $column_groups = array_chunk($chunks, 5);
                    foreach ($column_groups as $group) {
                        echo '<div class="column-group">';
                        foreach ($group as $column_data) {
                            echo '<ul class="job-column">';
                            foreach ($column_data as $label) {
                                foreach ($row_products_specialization as $spec) {
                                    if ($spec['specialization'] === $label) {
                                        $id = $spec['specialization_id'];
                                        break;
                                    }
                                }
                                $slug = strtolower(str_replace(' ', '-', $label));
                                echo "<li><a id='specialization-name' data-specializationid='{$id}' href='{$slug}'> {$label}</a></li>";
                            }
                            echo '</ul>';
                        }
                        echo '</div>';
                    }
                }
                ?>
            </div>
            <div class="all-jobs all-job-designation" style="display:none;">
                <?php
                if ($row_job_id) {
                    $printed_designations = [];
                    $columns = [];
                    $column = [];

                    foreach ($row_job_id as $job) {
                        if (!in_array($job['jobrole'], $printed_designations)) {
                            $printed_designations[] = $job['jobrole'];
                        }
                    }

                    $chunks = array_chunk($printed_designations, 7);
                    $column_groups = array_chunk($chunks, 5);
                    foreach ($column_groups as $group) {
                        echo '<div class="column-group">';
                        foreach ($group as $column_data) {
                            echo '<ul class="job-column">';
                            foreach ($column_data as $jobrole) {
                                $slug = strtolower(str_replace(' ', '-', $jobrole));
                                $url = "{$slug}";
                                echo "<li><a id='jobrole-name' data-jobrole=\"{$jobrole}\" href='{$url}'>{$jobrole}</a></li>";
                            }
                            echo '</ul>';
                        }
                        echo '</div>';
                    }
                }
                ?>
            </div>

        </div>
    </div>
</body>
<?php include 'footer.php' ?>
<script>
    $(document).ready(function () {
        $('.browse-job-btn, .hire-job-btn').on('click', function () {
            $('.browse-job-btn, .hire-job-btn').removeClass('active');
            $(this).addClass('active');
        });
    });

    $(document).ready(function () {
        $('.filter-word').on('click', function () {
            var text = $(this).text().trim(); // e.g., "Jobs by Location" or "Hire by Company"

            // Remove prefix ("Jobs by", "Jobs at", "Hire by", "Hire at")
            var classSuffix = text.toLowerCase()
                .replace(/^(jobs|hire)\s+(by|at)\s+/i, '')
                .replace(/ /g, '-');

            var targetClass = '.all-job-' + classSuffix;

            // Hide all sections
            $('.all-jobs').hide();

            // Show the target section
            $(targetClass).show();

            // Update active class
            $('.filter-word').removeClass('active');
            $(this).addClass('active');
        });
    });

    $(document).ready(function () {
        $('.filter-word.home-btn').on('click', function () {
            window.location.href = 'index.php';
        });
    });



    $(document).ready(function () {
        $('.alpa-filter-word').on('click', function () {
            $('.alpa-filter-word').removeClass('active');
            $(this).addClass('active');
        });
    });
    $(document).ready(function () {
        $('.browse-job-btn, .hire-job-btn').on('click', function () {
            $('.browse-job-btn, .hire-job-btn').removeClass('active');
            $(this).addClass('active');

            if ($(this).hasClass('browse-job-btn')) {
                $('.filter-div-candidate').show();
                $('.filter-div-company').hide();
            } else {
                $('.filter-div-company').show();
                $('.filter-div-candidate').hide();
            }
        });
    });

    $(document).ready(function () {
        $('#state-name').on('click', function () {
            var stateId = $(this).data('stateid');

            $.ajax({
                url: '/fetch_seo_citys.php',
                type: 'POST',
                data: { ajax_stateid: stateId },
                success: function (response) {
                    $('.all-jobs.all-job-location').hide();
                    $('.all-jobs.all-job-location-citys').html(response).slideDown();
                },
                error: function () {
                    console.log("AJAX error");
                }
            });
        });
    });



</script>