<?php
    require_once '../../class/Admin.php';
    include '../../class/clean.php';
    session_start();
// Regenerate session ID to prevent fixation
if (!isset($_SESSION['user_id'])) {
    session_regenerate_id(true);
}
// Check if the user is logged in and if their role is 'Admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../login.php");
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
        header("Location: ../../login.php");
        exit();
    } else {
        echo "<script>alert('Invalid CSRF token.');</script>";
    }
}


$admin = new admin();
require_once '../../dbConnCode.php';

// Initialize a message variable for SweetAlert
$message = null;

// Handle combined form submission for updating both tables
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Toggle application status
    if (isset($_POST['toggle_status'])) {
        $status = $_POST['current_status'] === 'open' ? 'closed' : 'open';

        $stmt = $conn->prepare("UPDATE application_status SET status = ? WHERE id = 1");
        $stmt->bind_param("s", $status);
        $stmt->execute();

        $message = $status === 'open' ? "Application is now open." : "Application is now closed.";
    }

    // Update research codes
    if (isset($_POST['research_codes'])) {
        foreach ($_POST['research_codes'] as $id => $data) {
            $codeAcronym = htmlspecialchars($data['code_acronym']);
            $codeNumber = intval($data['code_number']);

            $stmt = $conn->prepare("UPDATE research_codes SET code_acronym = ?, code_number = ? WHERE id = ?");
            $stmt->bind_param("sii", $codeAcronym, $codeNumber, $id);
            $stmt->execute();
        }
    }

    // Update dynamic data
    if (isset($_POST['dynamic_data'])) {
        foreach ($_POST['dynamic_data'] as $id => $data) {
            $certificateVersion = htmlspecialchars($data['certificate_version']);
            $dateEffective = htmlspecialchars($data['date_effective']);
            $letCode = htmlspecialchars($data['let_code']);

            $stmt = $conn->prepare("UPDATE reoc_dynamic_data SET certificate_version = ?, date_effective = ?, let_code = ? WHERE id = ?");
            $stmt->bind_param("sssi", $certificateVersion, $dateEffective, $letCode, $id);
            $stmt->execute();
        }
    }

    $message = "Data updated successfully.";
}
// Check if the application status exists, if not, insert default value
$result = $conn->query("SELECT * FROM application_status WHERE id = 1");

if ($result && $result->num_rows > 0) {
    // If data exists, fetch the status
    $status = $result->fetch_assoc()['status'];
} else {
    // If no data exists, insert a default record with 'open' status
    $insertQuery = "INSERT INTO application_status (id, status) VALUES (1, 'open')";
    if ($conn->query($insertQuery) === TRUE) {
        // After inserting, fetch the newly inserted status
        $status = 'open';
    } else {
        // Handle any error with the insertion
        $status = 'error';  // You can set an error status or handle it differently
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-Reoc-Data</title>
    <link rel="icon" type="image/x-icon" href="../../img/reoclogo1.jpg">
    <link rel="stylesheet" href="../../sidebar/sidebar.css">
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="../../css/admin-reoc-data.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css">
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>

<body>
<?php if (isset($_SESSION['message'])): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '<?= $_SESSION['message'] ?>',
            timer: 2000,
            showConfirmButton: false
        });
    </script>
    <?php unset($_SESSION['message']);
     endif;  ?>
    <?php require '../../sidebar/sidebar.html' ?>
    <main id="content">
    <div class="main-content">
    <h1 class="vision2">Manage Datas</h1>

        <!-- Status Toggle Section -->
        <?php
        $result = $conn->query("SELECT * FROM application_status WHERE id = 1");
        $status = $result->fetch_assoc()['status'];
        ?>
        <form method="POST" >
            <input type="hidden" name="current_status" value="<?php echo $status; ?>">
            <button type="submit" name="toggle_status" class="status-toggle-btn">
                Application is currently: <?php echo ucfirst($status); ?>
            </button>
        

        <!-- Research Codes Section -->
        <h3>Research Codes</h3>
        <table>
            <thead>
                <tr>
                    <th style="background-color:#aa3636; color:white;">ID</th>
                    <th style="background-color:#aa3636; color:white;">Code Acronym</th>
                    <th style="background-color:#aa3636; color:white;">Code Number</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $researchCodes = $conn->query("SELECT * FROM research_codes");
                while ($row = $researchCodes->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td>
                        <input type="text" name="research_codes[<?php echo $row['id']; ?>][code_acronym]" 
                               value="<?php echo htmlspecialchars($row['code_acronym']); ?>">
                    </td>
                    <td>
                        <input type="number" name="research_codes[<?php echo $row['id']; ?>][code_number]" 
                               value="<?php echo $row['code_number']; ?>">
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- REOC Dynamic Data Section -->
        <h3>REOC Dynamic Data</h3>
        <table>
            <thead>
                <tr>
                <th style="background-color:#aa3636; color:white;">ID</th>
                <th style="background-color:#aa3636; color:white;">Certificate Version</th>
                <th style="background-color:#aa3636; color:white;">Date Effective</th>
                <th style="background-color:#aa3636; color:white;">Let Code</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $dynamicData = $conn->query("SELECT * FROM reoc_dynamic_data");
                while ($row = $dynamicData->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td>
                        <input type="text" name="dynamic_data[<?php echo $row['id']; ?>][certificate_version]" 
                               value="<?php echo htmlspecialchars($row['certificate_version']); ?>">
                    </td>
                    <td>
                        <input type="text" name="dynamic_data[<?php echo $row['id']; ?>][date_effective]" 
                               value="<?php echo htmlspecialchars($row['date_effective']); ?>">
                    </td>
                    <td>
                        <input type="text" name="dynamic_data[<?php echo $row['id']; ?>][let_code]" 
                               value="<?php echo htmlspecialchars($row['let_code']); ?>">
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Single Submit Button -->
        <button type="submit" class="saveall">Save All Data</button>
    </form>
</div>
    </main>
</body>

</html>