<?php
    require_once '../../class/Admin.php';
    include '../../class/clean.php';
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

$admin = new admin();

$dataAPP = $admin->fetchAppData();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-Application_form</title>
    <link rel="icon" type="image/x-icon" href="../../img/reoclogo1.jpg">
    <link rel="stylesheet" href="../../sidebar/sidebar.css">
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="../../css/admin-app-data.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css">
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
</head>
<body>
<?php require '../../sidebar/sidebar.html' ?>

    <main class="main-content">
        <h2>Application Forms</h2>
        <div>
            <table id="myTable" class="display" style="width:2550px">
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
                        <th>Type Of Review</th>
                        <th>Generate Recommendation letter</th>
                        <th>Toggle Include</th>
                        <th>Certification</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach( $dataAPP as $data ): ?>
                        <tr>
                            <td><?= clean($data['uploaded_at']) ?></td>
                            <td><button class='view-btn' data-id=' <?= clean($data['id']) ?>'>View</button></td>
                            <td><?= clean($data['study_protocol_title']) ?></td>
                            <td><?= clean( $data['research_category'] ) ?></td>
                            <td><?= clean( $data['college'] ) ?></td>
                            <td><?= clean( $data['adviser_name'] ) ?></td>
                            <td><button class='view-files-btn' data-id="<?=  $data['id'] ?>">View Files</button></td>
                            <td class='emailColumn' ><?= clean( $data['email'] ) ?></td>
                            <td  ><?= clean( $data['mobile_number'] ) ?></td>
                            <td>
                                <select class='status-dropdown' data-id='<?= clean($data['id']) ?> '>
                                    <option value=' <?= clean($data['status']) ?>' selected><?= clean($data['status']) ?></option>
                                    <option value='For Initial Review'>For Initial Review</option>
                                    <option value='Waiting for Revision'>Waiting for Revision</option>  
                                    <option value='Panel Deliberation'>Panel Deliberation</option>
                                    <option value='Submission of Revisions'>Submission of Revisions</option>
                                    <option value='Checking of Revisions'>Checking of Revisions</option>
                                    <option value='Issuance of Certificate'>Issuance of Certificate</option>
                                    <option value='Submission Finalized'>Submission Finalized</option>
                                    <option value='Other'>Other</option> 
                                </select>
                                <?php if($data['status'] == "Issuance of Certificate"){
                                    
                                 echo "<button class='notif-btn' >Notify</button>";
                                }else{ 
                                    echo "<button class='notif-btn' disabled >Notified</button>";
                                } 
                                 
                                 ?>
                                <input type='text' class='status-input' data-id='<?= clean($data['id']) ?>' placeholder='Enter custom status' style='display:none;'>
                            </td>
                            <td>
                            <select class='type-review-dropdown' data-id='<?= clean(clean($data['id'])) ?> '>
                                <option value='Full Review' <?=  clean($data['type_of_review']) === 'Full Review' ? 'selected' : ''   ?>>Full Review</option>
                                <option value='Expedited' <?=  clean($data['type_of_review']) === 'Expedited' ? 'selected' : ''   ?>>Expedited</option>
                                <option value='Exempt' <?=  clean($data['type_of_review']) === 'Exempt' ? 'selected' : ''   ?>>Exempt</option>
                            </select>
                            </td>
                            <td><button class="generate-recommendation"><a href="RC_letter.php?id=<?= $data['id'] ?>">Create</a>  </button></td>
                            <td>                
                                <button class='toggle-btn' data-id='<?= clean($data['id']) ?> ' data-toggle='<?= clean($data['Toggle']) ?> '><?= 
                                    $data['Toggle'] == 1 ? "Exclude" : "Include" ?>
                                </button>
                            </td>
                            <td><button class="generate-btn" data-id="<?= $data['id']  ?>" data-review-type="<?= clean($data['type_of_review'])  ?>">Generate</button></td>
                            <td><button class='edit-btn' ><a href=' <?=' /REOC/editResearch.php?id='. $data['id'] ?>'>EDIT</a></button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>



</body>
<script>
$(document).ready(function() {
    $('#myTable').DataTable({
        "paging": true,          // Enables pagination
        "searching": true,       // Enables search box
        "ordering": false,        // Enables sorting
        "info": true,            // Shows "Showing X of Y entries"
        "lengthMenu": [5, 10, 25, 50],  // Controls entries per page
    });
});

document.querySelectorAll('.notif-btn').forEach(button => {
    button.addEventListener('click', function() {
        var email = this.closest('tr').querySelector('.emailColumn').textContent; // Get the email from the same row
        var id = this.closest('tr').querySelector('.status-dropdown').getAttribute('data-id'); // Get the ID from the dropdown

        // Send an AJAX request to notify the user
        fetch('/REOC/notify_user.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: email, id: id }) // Send the email and ID to the server
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Success',
                    text: 'Notification sent successfully.',
                    icon: 'success'
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to send notification.',
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error',
                text: 'An error occurred while sending the notification.',
                icon: 'error'
            });
        });
    });
});

document.querySelectorAll('.view-btn').forEach(button => {
        button.addEventListener('click', function() {
            var researcherTitleId = this.getAttribute('data-id'); // Get the researcher_title_id from the button data-id
            
            // Send an AJAX request to fetch the involved researchers for the selected title
            fetch('/REOC/fetch_researchers.php', {
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


document.querySelectorAll('.view-files-btn').forEach(button => {
    button.addEventListener('click', function() {
        // console.log('beep');
        var researcherTitleId = this.getAttribute('data-id'); // Get the researcher_title_id from the button data-id

        fetch('/REOC/fetch_files.php', {
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

$('.type-review-dropdown').change(function() {
        var id = $(this).data('id');
        var newValue = $(this).val();

        // AJAX request to update the type of review value
        $.ajax({
            url: '/REOC/update_type_review.php', // Replace with your PHP script to handle the update
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
        fetch(`/REOC/check_certificate_status.php?rti_id=${rtiId}`, {
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
        fetch("/REOC/update_status.php", {
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
                    window.location.href = window.location.href; // Refresh page
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



$(document).ready(function () {
    $(".toggle-btn").click(function () {
        var button = $(this);
        var researchId = button.data("id");
        var currentToggle = button.data("toggle");
        var newToggle = currentToggle == 1 ? 0 : 1; // Toggle value

        $.ajax({
            url: "/REOC/update_toggle.php",
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
</html>