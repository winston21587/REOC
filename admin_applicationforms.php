<?php
session_start();

// Regenerate session ID to prevent fixation
if (!isset($_SESSION['user_id'])) {
    session_regenerate_id(true); // Regenerate session id on first visit
}

// Check if the user is logged in and if their role is 'Admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Start CSRF token generation if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Database connection
require_once 'dbConnCode.php';

// Fetch uploaded research title information (including 'uploaded_at' field)
// Fetch uploaded research title information (including 'uploaded_at' and 'id' fields)
$query = "
  SELECT rti.id, 
         rti.user_id, 
         rti.uploaded_at, 
         rti.study_protocol_title, 
         rti.research_category, 
         rti.college,
         rti.adviser_name, 
         rti.payment,    
         rti.status,            -- Added payment column
         rti.type_of_review,         -- Added type_of_review column
         rti.Toggle, 
         a.appointment_date,
         rp.mobile_number,
         u.email
  FROM Researcher_title_informations AS rti
  LEFT JOIN appointments AS a ON rti.id = a.researcher_title_id  -- Change user_id to researcher_title_id
  LEFT JOIN researcher_profiles AS rp ON rti.user_id = rp.user_id
  LEFT JOIN users AS u ON rti.user_id = u.id
  ORDER BY rti.uploaded_at DESC
";




// Execute the query and store the result
$result = $conn->query($query);
// Fetch unique values for College, Research Category, and Status
$collegeOptions = [];
$categoryOptions = [];
$statusOptions = [];

$query = "SELECT DISTINCT college, research_category, status FROM Researcher_title_informations";
$Fresult = $conn->query($query);

if ($Fresult->num_rows > 0) {
    while ($row = $Fresult->fetch_assoc()) {
        if (!empty($row['college']) && !in_array($row['college'], $collegeOptions)) {
            $collegeOptions[] = $row['college'];
        }
        if (!empty($row['research_category']) && !in_array($row['research_category'], $categoryOptions)) {
            $categoryOptions[] = $row['research_category'];
        }
        if (!empty($row['status']) && !in_array($row['status'], $statusOptions)) {
            $statusOptions[] = $row['status'];
        }
    }
}

// Fetch unique months and years from uploaded_at
$monthYearOptions = [];

$query = "SELECT DISTINCT DATE_FORMAT(uploaded_at, '%M %Y') AS month_year FROM Researcher_title_informations ORDER BY uploaded_at DESC";
$Mresult = $conn->query($query);

if ($Mresult->num_rows > 0) {
    while ($row = $Mresult->fetch_assoc()) {
        if (!empty($row['month_year']) && !in_array($row['month_year'], $monthYearOptions)) {
            $monthYearOptions[] = $row['month_year'];
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Admin-Application Forms</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="./css/styles.css">
	<link rel="stylesheet" type="text/css" href="./css/piechart.css">
	<link rel="stylesheet" type="text/css" href="./css/admin-form.css" />
	<link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>

    
    <link rel="icon" type="image/x-icon" href="./img/reoclogo1.jpg">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all Edit buttons
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            // Get the 'id' from the data-id attribute of the clicked button
            const id = this.getAttribute('data-id');
            
            // Redirect to the edit page with the id as a query parameter
            window.location.href = 'editResearch.php?id=' + id;
        });
    });
});
document.addEventListener('DOMContentLoaded', function() {
    // Select all Edit buttons
    document.querySelectorAll('.edit-button').forEach(button => {
        button.addEventListener('click', function() {
            // Get the 'id' from the data-id attribute of the clicked button
            const id = this.getAttribute('data-id');
            
            // Redirect to the edit page with the id as a query parameter
            window.location.href = 'editResearchNouser.php?id=' + id;
        });
    });
});
</script>
    <style>
      
        
        .logout-button {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 20px;
        }
        .logout-button:hover {
            background-color: #c82333;
        }
        .main-content {
            flex: 1;
            padding: 20px;
        }
        .table-container {
            margin-top: 20px;
        }
 
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            font-size: 12px;
           
        }
        th {
            background-color:  #aa3636;
            color: white;
            text-align: center;
        }
     
        .logout-button {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 20px; /* Space between navbar and logout button */
        }
        .logout-button:hover {
            background-color: #c82333;
        }

    .insert{
        background-color:  #aa3636;
            color: white;
            padding: 10px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 95px;
            margin-top:20px; 
            transition: background-color 0.3s;
    }


    .insert:hover{
        background-color: #802c2c;
    }
    



.edit-btn{
    background-color:  #aa3636;
    padding:8px;
    cursor: pointer;
    transition: background-color 0.3s;
}


.edit-btn:hover{
    background-color: #802c2c;
}




        td.name {
	font-weight: bold;
}
td.email {
	color: #666;
	text-decoration: underline;
}

/* form styles */
input, select {
	font: inherit;
	margin: 0;
	padding: 4px;
	border: 1px solid #bbb;
}
.input-text {
	min-width: 150px;
	background-color: #fff;
}
select {
	min-width: 150px;
}
label {
	margin-right: 4px;
}



/* Filtable styles */
tr.hidden {
	display: none;
}
tr:nth-child(odd) > td {
	background-color: #ffffff;
}
tr:nth-child(even) > td {
	background-color: #f4f4f2;
}
tr.odd > td {
	background-color: #ffffff;
}
tr.even > td {
	background-color: #f4f4f2;
}


/* Large table example */

.console {
	font-family: ui-monospace, monospace;
}


.table-filters{
    position: relative;
   margin-left: 255px;
    unicode-bidi: isolate;

}

.table-filters label{
   margin-left: 35px;

}


.table-filters1{
    position: relative;
   margin-left: 95px;
    unicode-bidi: isolate;

}

.table-filters1 label{
   margin-left: 35px;

}

/* Modal checkbox trick */
input[type="checkbox"] {
    display: none;
}

input[type="checkbox"]:checked ~ .modal {
    display: flex;
}





button{
    cursor: pointer;
}



.tablee{
    position: relative;
    top: 50px;
}


.restitle {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
            transition: background-color 0.3s ease;
    
          }
          
          .restitle:hover {
            overflow: visible;
            white-space: normal;
            max-width: none;
            background-color: #fff;
            z-index: 1;
          }


    </style>
</head>

<body>
       
<header>
  <a href="#" class="brand">
    <img src="img/logos.png" class="logo">
    <span class="reoc">Research Ethics Oversite Committee Portal</span>
  </a>

  <div class="menu-btn">
    <div class="navigation">
      <div class="navigation-items">
      <a href="adminHome.php">Home</a>
      <a href="admin_applicationforms.php">Application Forms</a>
      <a href="account.php">Account Verifications</a>
    
       

        <!-- Logout Button -->
        <form method="POST" action="researcherHome.php" style="display: inline;">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
          <button type="submit" name="logout" class="logout-button">Logout</button>
        </form>
      </div>
    </div>
  </div>
  </header>
</header>
  
<h1 class="vision2">Application Forms</h1>
    <!-- Main Content -->
    <div class="main-content">
      
    <div class="table-filters1">
    <input type="text" id="searchTitle" placeholder="Search by Study Protocol Title..." onkeyup="filterTable()">
    <input type="text" id="searchEmail" placeholder="Search by Email..." onkeyup="filterTable()">

    <!-- College Filter -->
    <select id="filterCollege" onchange="filterTable()">
        <option value="">All Colleges</option>
        <?php foreach ($collegeOptions as $college) { ?>
            <option value="<?= htmlspecialchars($college) ?>"><?= htmlspecialchars($college) ?></option>
        <?php } ?>
    </select>

    <!-- Research Category Filter -->
    <select id="filterCategory" onchange="filterTable()">
        <option value="">All Categories</option>
        <?php foreach ($categoryOptions as $category) { ?>
            <option value="<?= htmlspecialchars($category) ?>"><?= htmlspecialchars($category) ?></option>
        <?php } ?>
    </select>

    <!-- Status Filter -->
    <select id="filterStatus" onchange="filterTable()">
        <option value="">All Statuses</option>
        <?php foreach ($statusOptions as $status) { ?>
            <option value="<?= htmlspecialchars($status) ?>"><?= htmlspecialchars($status) ?></option>
        <?php } ?>
    </select>
    <select id="filterMonthYear" onchange="filterTable()">
    <option value="">All Months</option>
    <?php foreach ($monthYearOptions as $monthYear) { ?>
        <option value="<?= htmlspecialchars($monthYear) ?>"><?= htmlspecialchars($monthYear) ?></option>
    <?php } ?>
</select>
</div>



<div style="width: 90%; overflow-x: auto; position: relative; margin-left: 100px;">
            <table  style="border-collapse: collapse; width: 100% !important; ; " >
                <thead>
                    <tr>
                        <th>Date Uploaded</th>
                        <th>Researchers Involved</th>
                        <th>Study Protocol Title</th>
                        <th>Research Category</th>
                        <th>College/ Institution</th>
                        <th>Name of the Adviser</th>
                        <th>Submitted Files</th>
                        <th>Email</th>
                        <th>Contact Number</th>
                        <th>Status</th>
                        <th>Type Of Research</th>
                        <th>Toggle Include</th>
                        <th>Certification</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                <?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['uploaded_at']) . "</td>";
        echo "<td><button class='view-btn' data-id='" . $row['id'] . "'>View</button></td>"; // Use id field for data-id
        echo "<td class='restitle'>" . htmlspecialchars($row['study_protocol_title']) . "</td>"; // Display study_protocol_title
        echo "<td>" . htmlspecialchars($row['research_category']) . "</td>";
        echo "<td>" . htmlspecialchars($row['college']) . "</td>";
        echo "<td>" . htmlspecialchars($row['adviser_name']) . "</td>";
        echo "<td><button class='view-files-btn' data-id='" . $row['id'] . "'>View Files</button></td>"; // Now using rti.id
        echo "<td class='emailColumn'>" . htmlspecialchars($row['email']) . "</td>";

        echo "<td>" . htmlspecialchars($row['mobile_number']) . "</td>";
        echo "<td>
        <select class='status-dropdown' data-id='" . $row['id'] . "'>
            <option value='" . htmlspecialchars($row['status']) . "' selected>" . htmlspecialchars($row['status']) . "</option>
            <option value='For Initial Review'>For Initial Review</option>
            <option value='Waiting for Revision'>Waiting for Revision</option>
            <option value='Panel Deliberation'>Panel Deliberation</option>
            <option value='Submission of Revisions'>Submission of Revisions</option>
            <option value='Checking of Revisions'>Checking of Revisions</option>
            <option value='Issuance of Certificate'>Issuance of Certificate</option>
            <option value='Complete Submission'>Complete Submission</option>
            <option value='Other'>Other</option> 
        </select>
        <input type='text' class='status-input' data-id='" . $row['id'] . "' placeholder='Enter custom status' style='display:none;'>
      </td>";


        

        // Type of Review dropdown
        echo "<td>
                <select class='type-review-dropdown' data-id='" . $row['id'] . "'>
                    <option value='For Initial Review' " . ($row['type_of_review'] === 'For Initial Review' ? 'selected' : '') . ">For Initial Review</option>
                    <option value='Initial Review' " . ($row['type_of_review'] === 'Initial Review' ? 'selected' : '') . ">Initial Review</option>
                    <option value='Full Review' " . ($row['type_of_review'] === 'Full Review' ? 'selected' : '') . ">Full Review</option>
                    <option value='Expedited' " . ($row['type_of_review'] === 'Expedited' ? 'selected' : '') . ">Expedited</option>
                    <option value='Exempt' " . ($row['type_of_review'] === 'Exempt' ? 'selected' : '') . ">Exempt</option>
                </select>
              </td>";
              
              echo "<td>
              <button class='toggle-btn' data-id='" . $row['id'] . "' data-toggle='" . $row['Toggle'] . "'>" . 
              ($row['Toggle'] == 1 ? "Exclude" : "Include") . 
              "</button>
            </td>";
      
      
         // Generate button
         echo '<td><button class="generate-btn" data-id="' . $row['id'] . '" data-review-type="' . htmlspecialchars($row['type_of_review']) . '">Generate</button></td>';
 
         // Edit button
         echo "<td><button class='edit-btn' data-id='" . $row['id'] . "'>Edit</button></td>";
 
         echo "</tr>";
    }
} else {
    echo "<tr><td colspan='11'>No files uploaded yet.</td></tr>"; // Adjust colspan based on the number of columns
}
?>
 </tbody>
            </table>
</div>
        </div>
    </div>
     
    <!-- Footer Section -->
   

	<!-- <footer class="footer">
		<div class="owl-carousel">
	  
		  <a href="#" class="gallery__photo">
			<img src="img/wmsu55.jpg" alt="" />
		 
		  </a>
		  <a href="#" class="gallery__photo">
			<img src="img/wmsu11.jpg" alt="" />
		  
		  </a>
		  <a href="#" class="gallery__photo">
			<img src="img/reoc11.jpg" alt="" />
		   
		  </a>
		  <a href="#" class="gallery__photo">
			<img src="img/wmsu22.jpg" alt="" />
		  
		  </a>
		  <a href="#" class="gallery__photo">
			<img src="img/reoc22.jpg" alt="" />
		   
		  </a>
		  <a href="#" class="gallery__photo">
			<img src="img/wmsu44.jpg" alt="" />
		   
		  </a>
	  
		</div>
		<div class="footer__redes">
		  <ul class="footer__redes-wrapper">
			<li>
			  <a href="#" class="footer__link">
				<i class=""></i>
				Normal Road, Baliwasan, Z.C.
			  </a>
			</li>
			<li>
			  <a href="#" class="footer__link">
				<i class=""></i>
				09112464566
			  </a>
			</li>
			<li>
			  <a href="#" class="footer__link">
				<i class=""></i>
				wmsureoc@gmail.com
			  </a>
			</li>
			<li>
			  <a href="#" class="footer__link">
				<i class="fab fa-phone-alt"></i>
				
			  </a>
			</li>
		  </ul>
		</div>
		<div class="separador"></div>
		<p class="footer__texto">RESEARCH ETHICS OVERSITE COMMITTEE - WMSU</p>
	  </footer> -->
	

	  
   
  
  
  
  <!-- partial -->

  
	<script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>
	<script src='https://unpkg.com/feather-icons'></script>
	  <script src="./js/main.js"></script>
	  <script src="./js/swiper.js"></script>
	  <script src="./js/footer.js"></script>
	  <script src="./js/faq.js"></script>
	

	<script src="./js/fonts.js"></script>
  
  

    <script>
    document.querySelectorAll('.view-btn').forEach(button => {
        button.addEventListener('click', function() {
            var researcherTitleId = this.getAttribute('data-id'); // Get the researcher_title_id from the button data-id
            
            // Send an AJAX request to fetch the involved researchers for the selected title
            fetch('fetch_researchers.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ researcher_title_id: researcherTitleId }) // Send the researcher_title_id to the server
            })
            .then(response => response.json()) // Parse the JSON response
            .then(data => {
                if (data.success) {
                    // Use SweetAlert to display the researchers' details
                    let researcherDetails = '';
                    data.researchers.forEach(researcher => {
                        researcherDetails += `<p><strong>Name:</strong> ${researcher.first_name} ${researcher.middle_initial}. ${researcher.last_name} ${researcher.suffix ? researcher.suffix : ''}</p>`;
                    });

                    Swal.fire({
                        title: 'Researchers Involved',
                        html: researcherDetails,
                        icon: 'info'
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'No researchers found for this title.',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while fetching the data.',
                    icon: 'error'
                });
            });
        });
    });

    // New View Files Button functionality
document.querySelectorAll('.view-files-btn').forEach(button => {
    button.addEventListener('click', function() {
        var researcherTitleId = this.getAttribute('data-id'); // Get the researcher_title_id from the button data-id

        fetch('fetch_files.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ researcher_title_id: researcherTitleId }) // Send researcher_title_id instead of user_id
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let fileDetails = '';
                data.files.forEach(file => {
                    fileDetails += `<p><strong>Type:</strong> ${file.file_type} <br> <strong>Filename:</strong> <a href="${file.file_path}" target="_blank">${file.filename}</a></p>`;
                });

                Swal.fire({
                    title: 'Uploaded Files',
                    html: fileDetails,
                    icon: 'info'
                });
            } else {
                Swal.fire({ title: 'Error', text: 'No files found for this researcher title.', icon: 'error' });
            }
        })
        .catch(error => {
            Swal.fire({ title: 'Error', text: 'An error occurred while fetching the files.', icon: 'error' });
        });
    });
});
    // Change event for Type of Review dropdown
    $('.type-review-dropdown').change(function() {
        var id = $(this).data('id');
        var newValue = $(this).val();

        // AJAX request to update the type of review value
        $.ajax({
            url: 'update_type_review.php', // Replace with your PHP script to handle the update
            type: 'POST',
            data: { id: id, type_of_review: newValue },
            success: function(response) {
                if (response.success) {
                    // Show success alert using SweetAlert
                    Swal.fire({
                        title: 'Success',
                        text: response.message,
                        icon: 'success'
                    });
                } else {
                    // Show error alert using SweetAlert
                    Swal.fire({
                        title: 'Error',
                        text: response.message,
                        icon: 'error'
                    });
                }
            },
            error: function() {
                // Handle errors if AJAX request fails
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while updating the type of review.',
                    icon: 'error'
                });
            }
        });
    });


document.querySelectorAll('.generate-btn').forEach(button => {
    button.addEventListener('click', function () {
        var userId = this.getAttribute('data-id'); // Get user ID from the button
        var reviewType = this.getAttribute('data-review-type'); // Get review type
        var rtiId = userId; // Assuming `userId` is the `rti_id`
        var certEndpoint = '';
        var coverLetterEndpoint = '';

        // Check if certificates already exist
        fetch(`check_certificate_status.php?rti_id=${rtiId}`, {
            method: 'GET',
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // If certificates exist, show them in a list with download links
                let certificateList = data.certificates
                    .map(cert => `<li><a href="${cert.file_url}" target="_blank">${cert.file_name}</a> (Generated at: ${cert.generated_at}, Type: ${cert.file_type})</li>`)
                    .join('');

                Swal.fire({
                    title: 'Certificates Found',
                    html: `<p>The following certificates have been generated for this title:</p><ul>${certificateList}</ul>`,
                    icon: 'info',
                });
            } else {
                // Prompt the user to select a date before proceeding
                Swal.fire({
                    title: 'Select Date',
                    html: '<input type="date" id="selectedDate" class="swal2-input">',
                    confirmButtonText: 'Confirm',
                    showCancelButton: true,
                    preConfirm: () => {
                        const selectedDate = document.getElementById('selectedDate').value;
                        if (!selectedDate) {
                            Swal.showValidationMessage('Please select a date');
                        }
                        return selectedDate;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        let selectedDate = result.value;

                        // Determine which PHP file to call based on review type
                        if (reviewType === 'Exempt') {
                            certEndpoint = `generate_cert_exempt.php?user_id=${userId}&date=${selectedDate}`;
                            coverLetterEndpoint = `generate_cover_letter_exempt.php?user_id=${userId}&date=${selectedDate}`;
                        } else if (reviewType === 'Full Review' || reviewType === 'Expedited') {
                            certEndpoint = `generate_REC_FLorEXP.php?user_id=${userId}&date=${selectedDate}`;
                            coverLetterEndpoint = `generate_cover_letter_researchEthics.php?user_id=${userId}&date=${selectedDate}`;
                        } else {
                            // Show SweetAlert for invalid review type
                            Swal.fire({
                                title: 'Not Eligible',
                                text: 'This research title is not eligible for certificate generation. Please check the review type.',
                                icon: 'warning',
                            });
                            return; // Stop further execution
                        }

                        // Generate certificate
                        fetch(certEndpoint, { method: 'GET' })
                            .then(response => response.json())
                            .then(certData => {
                                if (certData.success) {
                                    Swal.fire({
                                        title: 'Success',
                                        text: 'Certificate generated successfully.',
                                        icon: 'success',
                                    });

                                    // Generate cover letter
                                    if (coverLetterEndpoint) {
                                        fetch(coverLetterEndpoint, { method: 'GET' })
                                            .then(response => response.json())
                                            .then(coverData => {
                                                if (coverData.success) {
                                                    Swal.fire({
                                                        title: 'Success',
                                                        text: 'Cover letter and Certificate generated successfully.',
                                                        icon: 'success',
                                                    });
                                                } else {
                                                    Swal.fire({
                                                        title: 'Error',
                                                        text: coverData.message || 'Error generating cover letter.',
                                                        icon: 'error',
                                                    });
                                                }
                                            })
                                            .catch(error => {
                                                Swal.fire({
                                                    title: 'Error',
                                                    text: 'Error generating cover letter: ' + error.message,
                                                    icon: 'error',
                                                });
                                            });
                                    }
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: certData.message || 'Error generating certificate.',
                                        icon: 'error',
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Error generating certificate: ' + error.message,
                                    icon: 'error',
                                });
                            });
                    }
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error',
                text: 'Network or processing error: ' + error.message,
                icon: 'error',
            });
        });
    });
});



document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".status-dropdown").forEach(function (dropdown) {
        dropdown.addEventListener("change", function () {
            let inputField = this.nextElementSibling; // Get the adjacent input field
            if (this.value === "Other") {
                inputField.style.display = "inline-block"; // Show input field
                inputField.focus();
            } else {
                inputField.style.display = "none"; // Hide input field
                updateStatus(this.dataset.id, this.value); // Save the selected status
            }
        });
    });

    document.querySelectorAll(".status-input").forEach(function (input) {
        input.addEventListener("blur", function () {
            if (this.value.trim() !== "") {
                updateStatus(this.dataset.id, this.value.trim()); // Save the custom status
            }
            this.style.display = "none"; // Hide input field after saving
        });
    });

    function updateStatus(id, status) {
        fetch("update_status.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "id=" + encodeURIComponent(id) + "&status=" + encodeURIComponent(status),
        })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === "success") {
                Swal.fire({
                    title: "Updated!",
                    text: "Status updated successfully.",
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = "admin_applicationforms.php"; // Refresh page
                });
            } else {
                Swal.fire({
                    title: "Error!",
                    text: "Failed to update status.",
                    icon: "error"
                });
            }
        })
        .catch(error => {
            console.error("Error updating status:", error);
            Swal.fire({
                title: "Error!",
                text: "Something went wrong.",
                icon: "error"
            });
        });
    }
});







function filterTable() {
    let searchTitle = document.getElementById("searchTitle").value.toLowerCase();
    let searchEmail = document.getElementById("searchEmail").value.toLowerCase();
    let filterCollege = document.getElementById("filterCollege").value.toLowerCase();
    let filterCategory = document.getElementById("filterCategory").value.toLowerCase();
    let filterStatus = document.getElementById("filterStatus").value.toLowerCase();
    let filterMonthYear = document.getElementById("filterMonthYear").value.toLowerCase();

    let rows = document.querySelectorAll("#tableBody tr");

    rows.forEach(row => {
        let title = row.querySelector(".restitle")?.textContent.toLowerCase() || "";
        let email = row.querySelector(".emailColumn")?.textContent.toLowerCase() || "";
        let college = row.cells[4]?.textContent.toLowerCase() || "";
        let category = row.cells[3]?.textContent.toLowerCase() || "";
        let statusDropdown = row.cells[9]?.querySelector(".status-dropdown");
let status = statusDropdown ? statusDropdown.value.toLowerCase().trim() : "";

        
        // Get "Date Uploaded" column (should be the first column, so index 0)
        let uploadedAt = row.cells[0]?.textContent.trim() || "";

        // Extract month and year from uploaded date
        let dateObj = new Date(uploadedAt);
        let uploadedMonthYear = dateObj.toLocaleString('default', { month: 'long' }) + " " + dateObj.getFullYear();

        let matchesTitle = title.includes(searchTitle);
        let matchesEmail = email.includes(searchEmail);
        let matchesCollege = filterCollege === "" || college.includes(filterCollege);
        let matchesCategory = filterCategory === "" || category.includes(filterCategory);
        let matchesStatus = filterStatus === "" || status.includes(filterStatus);
        let matchesMonthYear = filterMonthYear === "" || uploadedMonthYear.toLowerCase() === filterMonthYear;

        if (matchesTitle && matchesEmail && matchesCollege && matchesCategory && matchesStatus && matchesMonthYear) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}



$(document).ready(function () {
    $(".toggle-btn").click(function () {
        var button = $(this);
        var researchId = button.data("id");
        var currentToggle = button.data("toggle");
        var newToggle = currentToggle == 1 ? 0 : 1; // Toggle value

        $.ajax({
            url: "update_toggle.php",
            type: "POST",
            data: { id: researchId, toggle: newToggle },
            success: function (response) {
                if (response == "success") {
                    button.data("toggle", newToggle);
                    button.text(newToggle == 1 ? "Exclude" : "Include");

                    Swal.fire({
                        icon: "success",
                        title: "Updated!",
                        text: newToggle == 1 ? "Now Including." : "Now Excluding.",
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Failed to update.",
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    text: "Something went wrong.",
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    });
});





</script>

</body>
</html>