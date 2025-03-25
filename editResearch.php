<?php
require_once 'dbConnCode.php';

// Fetch all colleges from the colleges table
$collegesQuery = "SELECT * FROM colleges";
$collegesStmt = $conn->prepare($collegesQuery);
$collegesStmt->execute();
$collegesResult = $collegesStmt->get_result();
$colleges = [];
while ($college = $collegesResult->fetch_assoc()) {
    $colleges[] = $college;
}


// Check if the 'id' parameter is passed in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the record for the specified ID
    $query = "SELECT * FROM Researcher_title_informations WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the record exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Record not found!";
        exit;
    }

    // Fetch researchers involved in the selected title
    $researchersQuery = "
        SELECT id, first_name, last_name, middle_initial
        FROM Researcher_involved
        WHERE researcher_title_id = ?
    ";
    $researchersStmt = $conn->prepare($researchersQuery);
    $researchersStmt->bind_param("i", $id);
    $researchersStmt->execute();
    $researchersResult = $researchersStmt->get_result();
    $researchers = [];
    while ($researcher = $researchersResult->fetch_assoc()) {
        $researchers[] = $researcher;
    }
     // Fetch certificates linked to this ID
     $certificatesQuery = "SELECT * FROM Certificate_generated WHERE rti_id = ?";
     $certificatesStmt = $conn->prepare($certificatesQuery);
     $certificatesStmt->bind_param("i", $id);
     $certificatesStmt->execute();
     $certificatesResult = $certificatesStmt->get_result();
     $certificates = [];
     while ($certificate = $certificatesResult->fetch_assoc()) {
         $certificates[] = $certificate;
     }

    
} else {
    echo "Invalid request!";
    exit;
}
?>

<!-- HTML for displaying the edit form -->

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit Research</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="./css/styles.css">
    <link rel="stylesheet" type="text/css" href="./css/piechart.css">
    <link rel="stylesheet" type="text/css" href="./css/admin-form.css" />
    <link rel="stylesheet" type="text/css" href="./css/editResearch.css" />
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>


    <link rel="icon" type="image/x-icon" href="./img/reoclogo1.jpg">


    <script defer src="./js/table.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
    // Function to show the input field when 'Other' is selected for College/Institution
    function toggleOtherCollege() {
        var collegeSelect = document.getElementById("college");
        var otherCollegeInput = document.getElementById("otherCollegeInput");
        if (collegeSelect.value === "Other") {
            otherCollegeInput.style.display = "inline";
        } else {
            otherCollegeInput.style.display = "none";
        }
    }
    </script>


</head>



<!-- Header Section -->

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
                    <input type="hidden" name="csrf_token"
                        value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <button type="submit" name="logout" class="logout-button">Logout</button>
                </form>
            </div>
        </div>
    </div>
</header>
</header>

<body>


<div class="editContent">
      <form action="updateResearch.php" method="POST" enctype="multipart/form-data" class="formm">
          <h2>Edit Researcher Application</h2>
          <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
  
          <!-- Study Protocol Title -->
          <label for="study_protocol_title">Study Protocol Title:</label>
          <input type="text" name="study_protocol_title"
              value="<?php echo htmlspecialchars($row['study_protocol_title']); ?>" required><br>
  
          <!-- College/Institution Dropdown -->
          <!-- College/Institution Dropdown -->
          <label for="college">College/Institution:</label>
          <select name="college" id="college" onchange="toggleOtherCollege()" required>
              <?php
      // Check if the user has selected a college and display that value as selected
      if ($row['college'] == "") {
          echo '<option value="">Select College/Institution</option>';
      }
      ?>
              <?php foreach ($colleges as $college): ?>
              <option value="<?php echo htmlspecialchars($college['college_name_and_color']); ?>"
                  <?php echo ($row['college'] == $college['college_name_and_color']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($college['college_name_and_color']); ?>
              </option>
              <?php endforeach; ?>
              <option value="Other" <?php echo ($row['college'] == 'Other') ? 'selected' : ''; ?>>Other</option>
          </select><br>
  
          <!-- Input for 'Other' College/Institution -->
          <input type="text" id="otherCollegeInput" name="college_other" placeholder="Enter College/Institution"
              style="display: <?php echo ($row['college'] == 'Other') ? 'inline' : 'none'; ?>;"><br>
  
          <!-- Research Category Dropdown -->
          <label for="research_category">Research Category:</label>
          <select name="research_category" required>
              <option value="WMSU Undergraduate Thesis - 300.00"
                  <?php echo ($row['research_category'] == 'WMSU Undergraduate Thesis - 300.00') ? 'selected' : ''; ?>>
                  WMSU Undergraduate Thesis - 300.00</option>
              <option value="WMSU Master's Thesis - 700.00"
                  <?php echo ($row['research_category'] == "WMSU Master's Thesis - 700.00") ? 'selected' : ''; ?>>WMSU
                  Master's Thesis - 700.00</option>>
              <option value="WMSU Dissertation - 1,500.00"
                  <?php echo ($row['research_category'] == 'WMSU Dissertation - 1,500.00') ? 'selected' : ''; ?>>WMSU
                  Dissertation - 1,500.00</option>
              <option value="WMSU Institutionally Funded Research - 2,000.00"
                  <?php echo ($row['research_category'] == 'WMSU Institutionally Funded Research - 2,000.00') ? 'selected' : ''; ?>>
                  WMSU Institutionally Funded Research - 2,000.00</option>
              <option value="Externally Funded Research / Other Institution - 3,000.00"
                  <?php echo ($row['research_category'] == 'Externally Funded Research / Other Institution - 3,000.00') ? 'selected' : ''; ?>>
                  Externally Funded Research / Other Institution - 3,000.00</option>
          </select><br>
  
          <!-- Adviser Name -->
          <label for="adviser_name">Adviser Name:</label>
          <input type="text" name="adviser_name" value="<?php echo htmlspecialchars($row['adviser_name']); ?>"
              required><br>
  
          <!-- Editable Researchers Involved -->
          <label for="researchers_involved">Researchers Involved:</label>
          <div>
              <?php if (count($researchers) > 0): ?>
              <?php foreach ($researchers as $researcher): ?>
              <div>
                  <label>First Name:</label>
                  <input type="text" name="researcher_first_name[<?php echo $researcher['id']; ?>]"
                      value="<?php echo htmlspecialchars($researcher['first_name']); ?>"><br>
                  <label>Middle Initial:</label>
                  <input type="text" name="researcher_middle_initial[<?php echo $researcher['id']; ?>]"
                      value="<?php echo htmlspecialchars($researcher['middle_initial']); ?>"><br>
                  <label>Last Name:</label>
                  <input type="text" name="researcher_last_name[<?php echo $researcher['id']; ?>]"
                      value="<?php echo htmlspecialchars($researcher['last_name']); ?>"><br>
  
  
              </div>
              <?php endforeach; ?>
              <?php else: ?>
              <p>No researchers found.</p>
              <?php endif; ?>
          </div><br>
  
          <!-- Certificates Section -->
          <h3>Certificates</h3>
          <?php if (count($certificates) > 0): ?>
          <?php foreach ($certificates as $certificate): ?>
          <div>
              <p>
                  Certificate File Name: <?php echo htmlspecialchars($certificate['file_path']); ?>
                  <a href="<?php echo 'http://localhost/REOC/pdfs/' . htmlspecialchars(basename($certificate['file_path'])); ?>"
                      download>
                      Download
                  </a>
              </p>
              <label for="replace_certificate_<?php echo $certificate['id']; ?>">Replace File:</label>
              <input type="file" name="replace_certificate[<?php echo $certificate['id']; ?>]"
                  id="replace_certificate_<?php echo $certificate['id']; ?>"><br>
              <input type="hidden" name="current_file_path[<?php echo $certificate['id']; ?>]"
                  value="<?php echo htmlspecialchars($certificate['file_path']); ?>">
  
              <!-- Status Dropdown -->
              <label for="status_<?php echo $certificate['id']; ?>">Status:</label>
              <select name="certificate_status[<?php echo $certificate['id']; ?>]"
                  id="status_<?php echo $certificate['id']; ?>">
                  <option value="Hide" <?php echo ($certificate['status'] === 'Hide') ? 'selected' : ''; ?>>Hide</option>
                  <option value="Show" <?php echo ($certificate['status'] === 'Show') ? 'selected' : ''; ?>>Show</option>
              </select>
          </div>
          <?php endforeach; ?>
          <?php else: ?>
          <p>No certificates found for this application.</p>
          <?php endif; ?>
  
  
  
          <!-- Submit button -->
          <input type="submit" value="Update" class="updatebtn">
      </form>
  
</div>
   
   
    <!--     
<footer class="footer">
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
	  </footer>
	 -->






    <!-- partial -->


    <script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>
    <script src='https://unpkg.com/feather-icons'></script>
    <script src="./js/main.js"></script>
    <script src="./js/swiper.js"></script>
    <script src="./js/footer.js"></script>
    <script src="./js/faq.js"></script>


    <script src="./js/fonts.js"></script>


</body>

</html>