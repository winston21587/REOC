<!-- <?php include 'Website Loading Screen/loader.php'; ?> call these for website loading animation -->
<!-- <link rel="stylesheet" href="Website Loading Screen/loader.css"> call these for website loading animation -->
<!-- <script src="Website Loading Screen/loader.js"></script> call these for website loading animation -->
<?php
session_start();
// include('updateAppointments.php');


// if the session id is not present it will direct to the login page
if(!isset($_SESSION['user_id'])){
    header('location:/REOC/login.php');
    exit();
}

// if the role is not matching the type of page admin e.g it will direct to the login page
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin'){
    header('location:/REOC/login.php'); // rejects unauthorized role (most likely a user role)
    exit();
}


// Regenerate session ID to prevent fixation
if (!isset($_SESSION['user_id'])) {
    session_regenerate_id(true);
}
// Check if the user is logged in and if their role is 'Admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: /REOC/login.php");
    exit();
}

// Start CSRF token generation if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Logout logic
if (isset($_POST['logout'])) {
    // Validate CSRF token
    if (hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        session_destroy();
        header("Location: ../login.php");
        exit();
    } else {
        echo "<script>alert('Invalid CSRF token.');</script>";
    }
}

// Database connection
require_once '../dbConnCode.php'; // Replace with your actual database connection file

require_once '../class/Admin.php';
$admin = new admin();
$faculty_id = 1; // Replace with dynamic ID based on the schedule being edited

// Query to fetch the current picture for the faculty
$query = "SELECT `picture` FROM `faculty_members` WHERE `id` = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$stmt->bind_result($current_picture1);
$stmt->fetch();
$stmt->close();

$schedule_id = 1; // Replace with dynamic ID based on the schedule being edited

// Query to fetch the current picture for the schedule
$query = "SELECT `picture` FROM `Schedule` WHERE `id` = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $schedule_id); // Bind the schedule_id dynamically
$stmt->execute();
$stmt->bind_result($current_picture2); // Get the current picture filename
$stmt->fetch();
$stmt->close();

// Fetch available months dynamically based on uploaded_at
$query = "
    SELECT DISTINCT DATE_FORMAT(uploaded_at, '%Y-%m-01') AS month FROM Researcher_title_informations
    UNION 
    SELECT DISTINCT DATE_FORMAT(uploaded_at, '%Y-%m-01') AS month FROM ResearcherTitleInfo_NoUser
    ORDER BY month DESC";
$result = $conn->query($query);
$availableMonths = [];
while ($row = $result->fetch_assoc()) {
    $availableMonths[] = $row['month'];
}

// Handle month selection with validation for empty $availableMonths
if (!empty($availableMonths)) {
    // Set selectedMonth from the URL parameter or default to the first available month
    $selectedMonth = $_GET['month'] ?? $availableMonths[0];
} else {
    // Handle the case where no months are available
    $selectedMonth = null; // Or any other fallback logic you prefer
}

// Fetch data for the selected month
$collegeDataQuery = "
    SELECT college, COUNT(*) AS count 
    FROM (
        SELECT college, uploaded_at FROM Researcher_title_informations 
        WHERE status = 'Complete Submission' AND Toggle = 1
        UNION ALL
        SELECT college, uploaded_at FROM ResearcherTitleInfo_NoUser 
        WHERE status = 'Complete Submission' AND Toggle = 1
    ) AS combined
    WHERE DATE_FORMAT(uploaded_at, '%Y-%m') = DATE_FORMAT('$selectedMonth', '%Y-%m') 
    GROUP BY college";

$collegeDataResult = $conn->query($collegeDataQuery);

$collegeData = [];
while ($row = $collegeDataResult->fetch_assoc()) {
    $collegeData[] = [
        'college' => $row['college'],
        'count' => $row['count']
    ];
}

// Fetch data for Exempt research categories for the selected month
$exemptDataQuery = "
    SELECT research_category, COUNT(*) AS count 
    FROM (
        SELECT research_category, uploaded_at 
        FROM Researcher_title_informations 
        WHERE status = 'Complete Submission' AND type_of_review = 'Exempt'AND Toggle = 1
        
        UNION ALL
        
        SELECT research_category, uploaded_at 
        FROM ResearcherTitleInfo_NoUser 
        WHERE status = 'Complete Submission' AND type_of_review = 'Exempt' AND Toggle = 1
    ) AS combined
    WHERE DATE_FORMAT(uploaded_at, '%Y-%m') = DATE_FORMAT('$selectedMonth', '%Y-%m') 
    GROUP BY research_category";
$exemptDataResult = $conn->query($exemptDataQuery);

$exemptData = [];
while ($row = $exemptDataResult->fetch_assoc()) {
    $exemptData[] = [
        'research_category' => $row['research_category'],
        'count' => $row['count']
    ];
}


// Fetch data for Expedited research categories for the selected month
$expeditedDataQuery = "
    SELECT research_category, COUNT(*) AS count 
    FROM (
        SELECT research_category, uploaded_at 
        FROM Researcher_title_informations 
        WHERE status = 'Complete Submission' 
        AND type_of_review = 'Expedited' AND Toggle = 1
        
        UNION ALL
        
        SELECT research_category, uploaded_at 
        FROM ResearcherTitleInfo_NoUser 
        WHERE status = 'Complete Submission' AND Toggle = 1 
        AND type_of_review = 'Expedited'
    ) AS combined
    WHERE DATE_FORMAT(uploaded_at, '%Y-%m') = DATE_FORMAT('$selectedMonth', '%Y-%m') 
    GROUP BY research_category";

$expeditedDataResult = $conn->query($expeditedDataQuery);

$expeditedData = [];
while ($row = $expeditedDataResult->fetch_assoc()) {
    $expeditedData[] = [
        'research_category' => $row['research_category'],
        'count' => $row['count']
    ];
}

// Fetch data for Full Review research categories for the selected month
$fullReviewDataQuery = "
    SELECT research_category, COUNT(*) AS count 
    FROM (
        SELECT research_category, uploaded_at 
        FROM Researcher_title_informations 
        WHERE status = 'Complete Submission' AND type_of_review = 'Full Review' AND Toggle = 1
        
        UNION ALL
        
        SELECT research_category, uploaded_at 
        FROM ResearcherTitleInfo_NoUser 
        WHERE status = 'Complete Submission' AND type_of_review = 'Full Review' AND Toggle = 1 
    ) AS combined
    WHERE DATE_FORMAT(uploaded_at, '%Y-%m') = DATE_FORMAT('$selectedMonth', '%Y-%m') 
    GROUP BY research_category";
$fullReviewDataResult = $conn->query($fullReviewDataQuery);

$fullReviewData = [];
while ($row = $fullReviewDataResult->fetch_assoc()) {
    $fullReviewData[] = [
        'research_category' => $row['research_category'],
        'count' => $row['count']
    ];
} 
// Query to get research categories and their counts
$query = "
    SELECT research_category, COUNT(*) as count 
    FROM (
        SELECT research_category, uploaded_at FROM Researcher_title_informations 
        WHERE status = 'Complete Submission' AND Toggle = 1
        
        UNION ALL
        
        SELECT research_category, uploaded_at FROM ResearcherTitleInfo_NoUser 
        WHERE status = 'Complete Submission' AND Toggle = 1
    ) AS combined 
    WHERE DATE_FORMAT(uploaded_at, '%Y-%m') = DATE_FORMAT('$selectedMonth', '%Y-%m')
    GROUP BY research_category";
$result = $conn->query($query);

// Initialize arrays for categories and counts
$researchCategories = [];
$researchCounts = [];

while ($row = $result->fetch_assoc()) {
    $researchCategories[] = $row['research_category'];
    $researchCounts[] = $row['count'];
}

// Convert PHP arrays to JSON for use in JavaScript
$researchCategories = json_encode($researchCategories);
$researchCounts = json_encode($researchCounts);

// Prepare data for the chart (Full Review category)
$fullReviewCategories = json_encode(array_column($fullReviewData, 'research_category'));
$fullReviewCounts = json_encode(array_column($fullReviewData, 'count'));
// Prepare data for the chart (Expedited category)
$expeditedCategories = json_encode(array_column($expeditedData, 'research_category'));
$expeditedCounts = json_encode(array_column($expeditedData, 'count'));
// Prepare data for the chart (Exempt category)
$exemptCategories = json_encode(array_column($exemptData, 'research_category'));
$exemptCounts = json_encode(array_column($exemptData, 'count'));
// Prepare data for the chart
$collegeNames = json_encode(array_column($collegeData, 'college'));
$collegeCounts = json_encode(array_column($collegeData, 'count'));


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin-Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="../fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" type="image/x-icon" href="../img/reoclogo1.jpg">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../sidebar/sidebar.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/admin-dash.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script defer src="./js/table.js"></script>


</head>



<body>

    <?php require '../sidebar/sidebar.html' ?>
    <main id="content">
        <h1 class="vision2">Analytics</h1>
 


        <div class="totalstat">
            <div class="box1 box">
                <div class="icon-stat">
                <svg class="w-[48px] h-[48px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
  <path fill-rule="evenodd" d="M8 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4H6Zm7.25-2.095c.478-.86.75-1.85.75-2.905a5.973 5.973 0 0 0-.75-2.906 4 4 0 1 1 0 5.811ZM15.466 20c.34-.588.535-1.271.535-2v-1a5.978 5.978 0 0 0-1.528-4H18a4 4 0 0 1 4 4v1a2 2 0 0 1-2 2h-4.535Z" clip-rule="evenodd"/>
</svg>
                </div>
                <div class="n-stat">
                    <strong><span class="OutputNumber" ><?= $admin->getTotalUsers()['total_users'] ?></span> Total Users</strong>
                    <p>something something description</p>
                </div>
            </div>
            <div class="box2 box">
                <div class="icon-stat">
                <svg class="w-[48px] h-[48px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
  <path fill-rule="evenodd" d="M9 2.221V7H4.221a2 2 0 0 1 .365-.5L8.5 2.586A2 2 0 0 1 9 2.22ZM11 2v5a2 2 0 0 1-2 2H4v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2h-7Z" clip-rule="evenodd"/>
</svg>
                </div>
                <div class="n-stat">
                    <strong><span class="OutputNumber" ><?= $admin->getTotalResearch()['total_research'] ?></span> Total Research's</strong>
                    <p>something something description</p>
                </div>
            </div>
            <div class="box3 box">
                <div class="icon-stat">
                <svg class="w-[48px] h-[48px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
  <path d="M9 7V2.221a2 2 0 0 0-.5.365L4.586 6.5a2 2 0 0 0-.365.5H9Z"/>
  <path fill-rule="evenodd" d="M11 7V2h7a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V9h5a2 2 0 0 0 2-2Zm4.707 5.707a1 1 0 0 0-1.414-1.414L11 14.586l-1.293-1.293a1 1 0 0 0-1.414 1.414l2 2a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
</svg>
                </div>
                <div class="n-stat">
                    <strong><span class="OutputNumber" ><?= $admin->getTotalTitleCompleted()['total_completed'] ?></span> Complete Submission</strong>
                    <p>something something description</p>
                </div>
            </div>
        </div>
        <!-- Filter Dropdown -->

        <div class="filter-container">
            <form method="GET">
                <label for="month"></label>
                <select name="month" id="month" onchange="this.form.submit()" class="monthSelect">
                    <?php foreach ($availableMonths as $month): ?>
                    <option value="<?php echo $month; ?>" <?php echo ($month === $selectedMonth) ? 'selected' : ''; ?>>
                        <?php echo date("F Y", strtotime($month)); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        <!-- Chart Container -->

        <div class="charts-container">
            <div class="chart-item">
                <h3>Colleges/Institutions</h3>
                <canvas id="collegePieChart"></canvas>
            </div>
            <div class="chart-item">
                <h3>Exempt Review</h3>
                <canvas id="exemptPieChart"></canvas>
            </div>
            <div class="chart-item">
                <h3>Expedited Research</h3>
                <canvas id="expeditedPieChart"></canvas>
            </div>
            <div class="chart-item">
                <h3>Full Review</h3>
                <canvas id="fullReviewPieChart"></canvas>
            </div>
            <div class="chart-item">
                <h3>Number of Research per Category</h3>
                <canvas id="researchCategoryPieChart"></canvas>
            </div>

        </div>
        </div>
    </main>
</body>
<!-- <script src='https://code.jquery.com/jquery-2.2.4.min.js'></script>
<script src='https://codepen.io/MaciejCaputa/pen/EmMooZ.js'></script>
<script src="../script.js"></script>
<script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>
<script src='https://unpkg.com/feather-icons'></script>
<script src="../js/fonts.js"></script> -->
<script src="../js/piechart.js"></script>
<script>
const ctx = document.getElementById('collegePieChart').getContext('2d');

// Modify the college names to exclude the part after the '-'
const modifiedCollegeNames = <?php echo $collegeNames; ?>.map(name => {
    // Split the name by '-' and take the first part (before the dash)
    return name.split(' -')[0];
});

const collegeCounts =
    <?php echo $collegeCounts; ?>; // Assuming this is an array with counts corresponding to the college names

const collegePieChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: modifiedCollegeNames, // Modified names without '-'
        datasets: [{
            data: collegeCounts, // Counts corresponding to the college names
            backgroundColor: [
                '#007bff',
                '#28a745',
                '#dc3545',
                '#ffc107',
                '#6c757d'
            ],
            borderColor: '#fff',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom', // Move the legend below the chart
                align: 'start', // Align labels to the left
                labels: {
                    boxWidth: 20, // Increase box width for better visibility
                    padding: 10, // Adjust the space between legend items
                    font: {
                        size: 12 // Adjust font size as needed
                    },
                    usePointStyle: true, // This makes the boxes circular instead of squares
                    boxHeight: 15, // Adjust box height for the circular style
                    generateLabels: function(chart) {
                        const original = Chart.overrides.pie.plugins.legend.labels.generateLabels;
                        const labels = original.call(this, chart);

                        labels.forEach((label, index) => {
                            // Modify the label text to append the count (collegeCounts[index])
                            label.text = `${label.text} (${collegeCounts[index]})`;

                            // Optional: Customize box color (if needed for dataset index)
                            label.fillStyle = label.datasetIndex === 0 ? '#007bff' : label
                                .fillStyle;
                        });

                        return labels;
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        // Display the category and its count in the tooltip
                        return `${context.label}: ${context.raw}`;
                    }
                }
            }
        }
    }
});


const ctxExempt = document.getElementById('exemptPieChart').getContext('2d');

// Modify the exempt categories names to ensure proper display
const exemptCategories = <?php echo $exemptCategories; ?>; // e.g., ["WMSU - Category A", "WMSU - Category B"]
const exemptCounts = <?php echo $exemptCounts; ?>; // e.g., [15, 25]

const exemptPieChart = new Chart(ctxExempt, {
    type: 'pie',
    data: {
        labels: exemptCategories, // Original categories; they will be modified dynamically
        datasets: [{
            data: exemptCounts,
            backgroundColor: [
                '#007bff',
                '#28a745',
                '#dc3545',
                '#ffc107',
                '#6c757d'
            ],
            borderColor: '#fff',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom', // Move the legend below the chart
                align: 'start', // Align labels to the left
                labels: {
                    boxWidth: 20, // Increase box width for better visibility
                    padding: 10, // Adjust the space between legend items
                    font: {
                        size: 12 // Adjust font size as needed
                    },
                    usePointStyle: true, // This makes the boxes circular instead of squares
                    boxHeight: 15, // Adjust box height for the circular style
                    generateLabels: function(chart) {
                        const original = Chart.overrides.pie.plugins.legend.labels.generateLabels;
                        const labels = original.call(this, chart);

                        labels.forEach((label, index) => {
                            // Trim label by splitting and removing 'WMSU'
                            let labelName = label.text.split(' -')[0]
                                .trim(); // Take the first part before ' -'
                            labelName = labelName.replace('WMSU', '')
                                .trim(); // Remove 'WMSU' if exists

                            // Append the count to the label
                            label.text = `${labelName} (${exemptCounts[index]})`;

                            // Optional: Customize box color (if needed for dataset index)
                            label.fillStyle = label.datasetIndex === 0 ? '#007bff' : label
                                .fillStyle;
                        });

                        return labels;
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        // Display the category and its count in the tooltip
                        return `${context.label}: ${context.raw}`;
                    }
                }
            }
        }
    }
});


const ctxExpedited = document.getElementById('expeditedPieChart').getContext('2d');

// Modify the exempt categories names to ensure proper display
const expeditedCategories = <?php echo $expeditedCategories; ?>; // e.g., ["WMSU - Category A", "WMSU - Category B"]
const expeditedCounts = <?php echo $expeditedCounts; ?>; // e.g., [15, 25]

const expeditedPieChart = new Chart(ctxExpedited, {
    type: 'pie',
    data: {
        labels: expeditedCategories, // Original categories; they will be modified dynamically
        datasets: [{
            data: expeditedCounts,
            backgroundColor: [
                '#007bff',
                '#28a745',
                '#dc3545',
                '#ffc107',
                '#6c757d'
            ],
            borderColor: '#fff',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom', // Move the legend below the chart
                align: 'start', // Align labels to the left
                labels: {
                    boxWidth: 20, // Increase box width for better visibility
                    padding: 10, // Adjust the space between legend items
                    font: {
                        size: 12 // Adjust font size as needed
                    },
                    usePointStyle: true, // This makes the boxes circular instead of squares
                    boxHeight: 15, // Adjust box height for the circular style
                    generateLabels: function(chart) {
                        const original = Chart.overrides.pie.plugins.legend.labels.generateLabels;
                        const labels = original.call(this, chart);

                        labels.forEach((label, index) => {
                            // Trim label by splitting and removing 'WMSU'
                            let labelName = label.text.split(' -')[0]
                                .trim(); // Take the first part before ' -'
                            labelName = labelName.replace('WMSU', '')
                                .trim(); // Remove 'WMSU' if exists

                            // Append the count to the label
                            label.text = `${labelName} (${expeditedCounts[index]})`;

                            // Optional: Customize box color (if needed for dataset index)
                            label.fillStyle = label.datasetIndex === 0 ? '#007bff' : label
                                .fillStyle;
                        });

                        return labels;
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        // Display the category and its count in the tooltip
                        return `${context.label}: ${context.raw}`;
                    }
                }
            }
        }
    }
});

const ctxFullReview = document.getElementById('fullReviewPieChart').getContext('2d');

// Modify the full review categories names to ensure proper display
const fullReviewCategories =
    <?php echo $fullReviewCategories; ?>; // e.g., ["WMSU - Category A", "WMSU - Category B"]
const fullReviewCounts = <?php echo $fullReviewCounts; ?>; // e.g., [10, 20]

const fullReviewPieChart = new Chart(ctxFullReview, {
    type: 'pie',
    data: {
        labels: fullReviewCategories, // Original categories; they will be modified dynamically
        datasets: [{
            data: fullReviewCounts,
            backgroundColor: [
                '#007bff',
                '#28a745',
                '#dc3545',
                '#ffc107',
                '#6c757d'
            ],
            borderColor: '#fff',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom', // Move the legend below the chart
                align: 'start', // Align labels to the left
                labels: {
                    boxWidth: 20, // Increase box width for better visibility
                    padding: 10, // Adjust the space between legend items
                    font: {
                        size: 12 // Adjust font size as needed
                    },
                    usePointStyle: true, // This makes the boxes circular instead of squares
                    boxHeight: 15, // Adjust box height for the circular style
                    generateLabels: function(chart) {
                        const original = Chart.overrides.pie.plugins.legend.labels.generateLabels;
                        const labels = original.call(this, chart);

                        labels.forEach((label, index) => {
                            // Trim label by splitting and removing 'WMSU'
                            let labelName = label.text.split(' -')[0]
                                .trim(); // Take the first part before ' -'
                            labelName = labelName.replace('WMSU', '')
                                .trim(); // Remove 'WMSU' if exists

                            // Append the count to the label
                            label.text = `${labelName} (${fullReviewCounts[index]})`;

                            // Optional: Customize box color (if needed for dataset index)
                            label.fillStyle = label.datasetIndex === 0 ? '#007bff' : label
                                .fillStyle;
                        });

                        return labels;
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        // Display the category and its count in the tooltip
                        return `${context.label}: ${context.raw}`;
                    }
                }
            }
        }
    }
});

const ctxResearchCategory = document.getElementById('researchCategoryPieChart').getContext('2d');

// Data for the research category chart
const researchCategories = <?php echo $researchCategories; ?>; // e.g., ["WMSU - Biology", "WMSU - Chemistry"]
const researchCounts = <?php echo $researchCounts; ?>; // e.g., [10, 20]

const researchCategoryPieChart = new Chart(ctxResearchCategory, {
    type: 'pie',
    data: {
        labels: researchCategories, // Original categories; they will be modified dynamically
        datasets: [{
            data: researchCounts,
            backgroundColor: [
                '#007bff',
                '#28a745',
                '#dc3545',
                '#ffc107',
                '#6c757d'
            ],
            borderColor: '#fff',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                align: 'start',
                labels: {
                    boxWidth: 20,
                    padding: 10,
                    font: {
                        size: 12
                    },
                    usePointStyle: true,
                    boxHeight: 15,
                    generateLabels: function(chart) {
                        const original = Chart.overrides.pie.plugins.legend.labels.generateLabels;
                        const labels = original.call(this, chart);

                        labels.forEach((label, index) => {
                            // Trim label by splitting and removing 'WMSU'
                            let labelName = label.text.split(' -')[0]
                                .trim(); // Take the first part before ' -'
                            labelName = labelName.replace('WMSU', '')
                                .trim(); // Remove 'WMSU' if exists
                            label.text =
                                `${labelName} (${researchCounts[index]})`; // Add count to the label
                        });

                        return labels;
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return `${context.label}: ${context.raw}`;
                    }
                }
            }
        }
    }
});

function openFacultyModal() {


    // Show the modal
    document.getElementById("facultyModal").style.display = "flex";
}

// Close Modal
function closeFacultyModal() {
    document.getElementById("facultyModal").style.display = "none";
}


function removePicture() {
    const facultyId = document.querySelector('input[name="faculty_id"]').value;

    // Show SweetAlert confirmation dialog
    Swal.fire({
        title: 'Are you sure you want to remove the current picture?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove it!',
        cancelButtonText: 'No, keep it'
    }).then((result) => {
        if (result.isConfirmed) {
            // Send AJAX request to remove the picture
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "remove_picture.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Remove the image from the modal and hide the remove button
                        document.getElementById("current-picture").remove();
                        document.getElementById("remove-picture").style.display = 'none';
                        Swal.fire({
                            title: 'Picture removed successfully.',
                            icon: 'success'
                        }).then(function() {
                            // Redirect to adminHome.php after success
                            window.location.href =
                                "adminHome.php"; // Redirect to the admin home page
                        });
                    } else {
                        Swal.fire({
                            title: 'Error removing the picture.',
                            icon: 'error'
                        });
                    }
                }
            };
            xhr.send("faculty_id=" + facultyId);
        }
    });
}

// Function to open the Schedule Modal
function openScheduleModal() {
    // Show the modal
    document.getElementById("scheduleModal").style.display = "block";
}

// Function to close the Schedule Modal
function closeScheduleModal() {
    document.getElementById("scheduleModal").style.display = "none";
}



// Function to close the FAQ  Modal
function closeFaqModal() {
    console.log('working');
    document.getElementById("faqModal").style.display = "none";
}


// Function to remove the picture for a schedule
function removeSchedulePicture() {
    const scheduleId = document.querySelector('input[name="schedule_id"]').value;

    // Show SweetAlert confirmation dialog
    Swal.fire({
        title: 'Are you sure you want to remove the current picture?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove it!',
        cancelButtonText: 'No, keep it'
    }).then((result) => {
        if (result.isConfirmed) {
            // Send AJAX request to remove the picture
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "remove_schedule_picture.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Remove the image from the modal and hide the remove button
                        document.getElementById("current-picture").remove();
                        document.getElementById("remove-picture").style.display = 'none';
                        Swal.fire({
                            title: 'Picture removed successfully.',
                            icon: 'success'
                        }).then(function() {
                            // Refresh the page after success
                            window.location.href = "adminHome.php"; // Redirect to adminHome.php
                        });
                    } else {
                        Swal.fire({
                            title: response.message || 'Error removing the picture.',
                            icon: 'error'
                        });
                    }
                }
            };
            xhr.send("schedule_id=" + scheduleId);
        }
    });
}

flatpickr("#dates", {
    mode: "multiple", // Allow multiple date selection
    dateFormat: "Y-m-d", // Date format
    allowInput: false, // Prevent manual typing
    defaultDate: null, // Remove default date
    disableMobile: true, // Disable mobile UI
    minDate: "today", // Disable past dates
    onChange: function(selectedDates) {
        // Store the selected dates in a hidden input field (for POST submission)
        document.getElementById("dates").value = selectedDates.map(function(date) {
            return date.toISOString().split('T')[0]; // Convert date to YYYY-MM-DD format
        }).join(',');
    }
});

function openvmModal() {
    console.log('vm opended');
    document.getElementById("vmForm").style.display = "flex";
}

function closevmModal() {
    document.getElementById("vmForm").style.display = "none";
}

document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById("faqModal");
    const closeModal = document.querySelector(".close");
    const editFaqBtn = document.getElementById("editFaqBtn");
    const faqList = document.getElementById("faqList");
    const faqForm = document.getElementById("faqForm");
    const faqIdInput = document.getElementById("faqId");
    const questionInput = document.getElementById("question");
    const answerInput = document.getElementById("answer");

    // Open modal
    editFaqBtn.addEventListener("click", function() {
        modal.style.display = "flex";
        loadFAQs(); // Load FAQs when opening 
    });

    // Close modal
    closeModal.addEventListener("click", function() {
        modal.style.display = "none";
    });


    // Load FAQs from database
    function loadFAQs() {
        fetch("fetch_faqs.php")
            .then(response => response.json())
            .then(data => {
                faqList.innerHTML = "";
                data.forEach(faq => {
                    let encodedQuestion = encodeURIComponent(faq.question);
                    let encodedAnswer = encodeURIComponent(faq.answer);
                    let li = document.createElement("li");
                    li.innerHTML = `
                    <strong>${faq.question}</strong><br>
                    ${faq.answer}<br>
                    <button onclick="editFAQ(${faq.id}, '${encodedQuestion}', '${encodedAnswer}')">Edit</button>
                    <button onclick="deleteFAQ(${faq.id})">Delete</button>
                `;
                    faqList.appendChild(li);
                });
            })
            .catch(error => console.error("Error fetching FAQs:", error));
    }

    // Fill form for editing
    window.editFAQ = function(id, question, answer) {
        faqIdInput.value = id;
        questionInput.value = decodeURIComponent(question);
        answerInput.value = decodeURIComponent(answer);
        modal.style.display = "block"; // Open the modal when editing
    };


    // Save or update FAQ
    faqForm.addEventListener("submit", function(e) {
        e.preventDefault();
        const formData = new FormData();
        formData.append("id", faqIdInput.value);
        formData.append("question", questionInput.value);
        formData.append("answer", answerInput.value);

        fetch("save_faq.php", {
                method: "POST",
                body: formData,
            })
            .then(response => response.text())
            .then(data => {
                Swal.fire({
                    title: "Success!",
                    text: "FAQ saved successfully.",
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    modal.style.display = "none";
                    loadFAQs();
                    setTimeout(() => {
                        window.location.href =
                            "adminHome.php"; // Refresh adminHome.php
                    }, 500); // Slight delay to ensure smooth transition
                });
            })
            .catch(error => Swal.fire("Error!", "Could not save FAQ.", "error"));
    });

    // Delete FAQ with SweetAlert confirmation
    window.deleteFAQ = function(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "This FAQ will be deleted permanently.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("delete_faq.php?id=" + id, {
                        method: "GET"
                    })
                    .then(response => response.text())
                    .then(data => {
                        Swal.fire({
                            title: "Deleted!",
                            text: "FAQ has been removed.",
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            loadFAQs();
                            setTimeout(() => {
                                window.location.href =
                                    "adminHome.php"; // Refresh adminHome.php
                            }, 500); // Slight delay to ensure smooth transition
                        });
                    })
                    .catch(error => Swal.fire("Error!", "Could not delete FAQ.", "error"));
            }
        });
    };
});
</script>

</html>