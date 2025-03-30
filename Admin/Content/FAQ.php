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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["save_faq"])) {
            $question = clean($_POST['question']);
            $answer = clean($_POST['answer']);
            $faq_id = isset($_POST['faq_id']) ? $_POST['faq_id'] : null;
    
            if ($faq_id) {
                $admin->updateFAQ($faq_id, $question, $answer);
                $_SESSION['message'] = "FAQ Updated Successfully!";
            } else {
                $admin->addFAQ($question, $answer);
                $_SESSION['message'] = "FAQ Added Successfully!";
            }
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    
        if (isset($_POST["delete_faq"])) {
            $faq_id = isset($_POST['faq_delete_inp']) ? $_POST['faq_delete_inp'] : null;
            if ($faq_id) {
                $admin->deleteFAQ($faq_id);
                $_SESSION['message'] = "FAQ Deleted Successfully!";
            }
    
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }
    


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-FAQ</title>
    <link rel="icon" type="image/x-icon" href="../../img/reoclogo1.jpg">
    <link rel="stylesheet" href="../../sidebar/sidebar.css">
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="../../css/admin-FAQ.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<!-- sweet alert -->

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
        <h2>FAQ Manager</h2>
        <div class="faq-container">
            <div class="faq-header">
                <button class="faq-btn-add" onclick="openFaq()">Add FAQ</button>
            </div>
            <?php foreach ($admin->fetchFAQ() as $faq): ?>
            <div class='faq-item'>
                <div class="edit-faq-icon"
                    onclick="openFaq(<?= $faq['id'] ?>,'<?= addslashes($faq['question']) ?>', '<?= addslashes($faq['answer']) ?>')">
                    <svg class="w-[29px] h-[29px] text-gray-800 dark:text-white" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.779 17.779 4.36 19.918 6.5 13.5m4.279 4.279 8.364-8.643a3.027 3.027 0 0 0-2.14-5.165 3.03 3.03 0 0 0-2.14.886L6.5 13.5m4.279 4.279L6.499 13.5m2.14 2.14 6.213-6.504M12.75 7.04 17 11.28" />
                    </svg>
                </div>
                <div class="content-QA">
                    <h3><strong>Q: </strong><?= clean($faq['question']) ?></h3>
                    <p><strong>A: </strong><?= clean($faq['answer']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="faqModal">
            <div class="faqModal-content">
                <span class="closeFaq" onclick="closeFaq()">&times;</span>
                <h2 id="faqModalTitle">ADD FAQ</h2>
                <form method="POST">
                    <input type="hidden" name="faq_id" id="faq_id">
                    <label for="question">Question:</label>
                    <input type="text" name="question" id="question" required>
                    <label for="answer">Answer:</label>
                    <textarea name="answer" id="answer" required></textarea>
                    <button type="submit" name="save_faq" id="faqSubmitButton">Confirm</button>
                </form>
                <button class="deleteBtn">Delete</button>
            </div>
        </div>


        <div class="deleteFaqComfirmation">
                <div class="deleteFaqComfirmation-content">
                    <span class="closeDeleteBtn" onclick="closeDeleteBtn()">&times;</span>
                    <h2>Are you sure you want to delete this FAQ?</h2>
                    <form method="POST">
                        <input type="hidden" name="faq_delete_inp" id="faq_delete_inp">
                        <button type="submit" name="delete_faq" class="confirmDeleteBtn">Yes</button>
                    </form>
                    <button class="cancelDeleteBtn" onclick="closeDeleteBtn()">No</button>
                </div>

            </div>
    </main>
</body>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const faqItems = document.querySelectorAll(".faq-item");

    faqItems.forEach(item => {
        const editIcon = item.querySelector(".edit-faq-icon");

        item.addEventListener("mouseenter", function() {
            if (editIcon) {
                editIcon.style.opacity = "1";
                editIcon.style.visibility = "visible";
            }
        });

        item.addEventListener("mouseleave", function() {
            if (editIcon) {
                editIcon.style.opacity = "0";
                editIcon.style.visibility = "hidden";
            }
        });
    });
});

function openFaq(id = null, question = '', answer = '') {
    const modal = document.querySelector('.faqModal');
    const hiddenInput = modal.querySelector('#faq_id');
    const questionInput = modal.querySelector('#question');
    const answerInput = modal.querySelector('#answer');
    const modalTitle = document.querySelector('#faqModalTitle');
    const submitButton = document.querySelector('#faqSubmitButton');
    deleteBtn = document.querySelector('.deleteBtn');
    modal.style.display = 'block';

    if (id !== null) {
        hiddenInput.value = id;
        questionInput.value = question;
        answerInput.value = answer;
        modalTitle.textContent = "EDIT FAQ";
        submitButton.textContent = "Update";    
        deleteBtn.style.display = 'block';
        deleteBtn.onclick = function() {
            faq_delete_inp = document.querySelector('#faq_delete_inp');
            deleteFaqComfirmation = document.querySelector('.deleteFaqComfirmation');
            deleteFaqComfirmation.style.display = 'block';
            faq_delete_inp.value = id;
        }


    } else {
        hiddenInput.value = null;
        questionInput.value = '';
        answerInput.value = '';
        modalTitle.textContent = "ADD FAQ";
        submitButton.textContent = "Confirm";
    }
}

function closeFaq() {
    const modal = document.querySelector('.faqModal');
    deleteFaqComfirmation = document.querySelector('.deleteFaqComfirmation');
    deleteFaqComfirmation.style.display = 'none';
    modal.style.display = 'none';
}
function closeDeleteBtn() {
    deleteFaqComfirmation = document.querySelector('.deleteFaqComfirmation');
    deleteFaqComfirmation.style.display = 'none';
}
</script>

</html>