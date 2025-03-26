<?php
ob_start();

if (isset($_GET['download'])) {
    $file = basename($_GET['download']);
    $filepath = __DIR__ . "/files/" . $file;

    if (file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    } else {
        http_response_code(404);
        die("Error: File not found.");
    }
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <style>
    .navbar-brand img {
      height: 40px;
    }
    
    .navbar {
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .dropdown-item > a {
      text-decoration: none;
      color: inherit;
    }

    .reoc-text {
    color: #990101 !important;
    }
  </style>

<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
    <div class="container-fluid">
      
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="./img/reoclogo1.jpg" alt="">
        <div class="ms-2">
        <span class="d-none d-lg-block reoc-text">Research Ethics Oversight Committee Portal</span>
        <span class="d-lg-none reoc-text">WMSU REOC PORTAL</span>
       </div>
      </a>
      
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
              aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
     
      <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
         
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="applicationsDropdown" role="button"
               data-bs-toggle="dropdown" aria-expanded="false">
              Applications
            </a>
            <ul class="dropdown-menu" aria-labelledby="applicationsDropdown">
              <li><a class="dropdown-item" href="SubmitFiles.php">Submit Application</a></li>
              <li><a class="dropdown-item" href="viewApplications.php">View Applications</a></li>
            </ul>
          </li>
          
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="downloadablesDropdown" role="button"
               data-bs-toggle="dropdown" aria-expanded="false">
              Downloadables
            </a>
            <ul class="dropdown-menu" aria-labelledby="downloadablesDropdown">
              <li>
                <div class="dropdown-item">
                  <strong>Application Form</strong>
                  <div><a href="./REOC Application Form/2-FR.002 Application Form.doc">Download</a></div>
                </div>
              </li>
              <li>
                <div class="dropdown-item">
                  <strong>Study Protocol Assessment</strong>
                  <div><a href="./Expedited and Full Form/4-FR.004 Study Protocol  Assessment Form.docx">Download</a></div>
                </div>
              </li>
              <li>
                <div class="dropdown-item">
                  <strong>Informed Consent Assessment Form</strong>
                  <div><a href="./Expedited and Full Form/5 -FR.005 Informed Consent Assessment Form.docx">Download</a></div>
                </div>
              </li>
              <li>
                <div class="dropdown-item">
                  <strong>Exempt Review Assessment Form</strong>
                  <div><a href="./Exempt Form/6- FR.006 EXEMPT REVIEW ASSESSMENT FORM.docx">Download</a></div>
                </div>
              </li>
            </ul>
          </li>
          <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Instructions
    </a>
    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
        <li><a class="dropdown-item" href="./instructions.html">Instructions</a></li>
        <li><a class="dropdown-item" href="view_faculty_members.php">Faculty Members</a></li>
    </ul>
</li>
          <li class="nav-item">
            <form method="POST" action="researcherHome.php" class="d-flex align-items-center">
              <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
              <button type="submit" name="logout" class="btn btn-danger ms-lg-2">Logout</button>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
