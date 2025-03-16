<?php
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

<header class="desktop-navbar">
    <a href="#" class="brand">
        <img src="./img/wmsu-logo-hd.png" class="logo">
        <span class="reoc">Research Ethics Oversight Committee Portal</span>
    </a>

    <div class="navigation">
        <div class="navigation-items">
            <div class="dropdown1">
                <a href="#">Applications</a>
                <div class="dropdown-content1">
                    <div class="file-item1"><a href="SubmitFiles.php">Submit Application</a></div>
                    <div class="file-item1"><a href="viewApplications.php">View Applications</a></div>
                </div>
            </div>

            <div class="dropdown">
                <a href="#">Downloadables</a>
                <div class="dropdown-content">
                    <div class="file-item">
                        <span><strong>Application Form</strong></span>
                        <a href="?download=2-FR.002-Application-Form.doc">Download</a>
                    </div>
                    <div class="file-item">
                        <span><strong>Study Protocol Assessment</strong></span>
                        <a href="?download=4-FR.004-Study-Protocol-Assessment-Form-Copy.docx">Download</a>
                    </div>
                </div>
            </div>

            <a href="./instructions.html">Instructions</a>

            <form method="POST" action="researcherHome.php" style="display: inline;">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <button type="submit" name="logout" class="logout-button">Logout</button>
            </form>
        </div>
    </div>
</header>

<header class="mobile-navbar">
    <a href="#" class="brand">
        <img src="./img/wmsu-logo-hd.png" class="logo">
        <span class="reoc">WMSU REOC PORTAL</span>
    </a>
    
    <div class="menu-btn" id="mobile-menu-btn">
        <span class="burger"></span>
    </div>

    <nav class="mobile-menu" id="mobile-menu">
        <ul>
            <li><a href="SubmitFiles.php">Submit Application</a></li>
            <li><a href="viewApplications.php">View Applications</a></li>
            <li><a href="?download=2-FR.002-Application-Form.doc">Application Form</a></li>
            <li><a href="?download=4-FR.004-Study-Protocol-Assessment-Form-Copy.docx">Study Protocol</a></li>
            <li><a href="./instructions.html">Instructions</a></li>
            <li>
                <form method="POST" action="researcherHome.php">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                    <button type="submit" name="logout" class="logout-button">Logout</button>
                </form>
            </li>
        </ul>
    </nav>
</header>

<style>
  .desktop-navbar {
    height: 10vh;
  }
  .logo {
    width: auto;
    height: auto;
    max-width: none;
    max-height: none;
    margin-bottom: 5px;
  }
  .menu-btn {
    margin-top: 15px;
  }
  .reoc {
    margin-bottom: 10px;
  }
</style>

<script>
document.getElementById("mobile-menu-btn").addEventListener("click", function() {
    document.getElementById("mobile-menu").classList.toggle("open");
});
</script>
