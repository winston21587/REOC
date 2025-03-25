<?php

// require_once 'Submit.php';

// $obj = new Submit();

// $dateforconsult = $obj->getAvailableConsultation();
// if(!empty($dateforconsult)){
//     $date = $dateforconsult['next_appointment_date'];
//     $consultID = $dateforconsult['id'];
// echo '<h1>'. $date. ' id:'. $consultID .'</h1>';
// $obj->setconsultSchedStatus($consultID);

// }else{
//     echo 'No appointment available';
// }

// $id = 60;
// $obj->setAppointment($id,$consultID,$date);




// $ar[0] = true;
// $ar[1] = false;
// $ar[2] = true;

// if(in_array(false,$ar)){   // flag setter 
//     echo 'false';
// }else{
//     echo 'true';
// }

if(isset($_POST["submit"])){
    foreach($_POST["researcher_first_name"] as $item){
    echo"<pre>";
    echo $item;
    echo"</pre>";
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Site</title>
</head>

<body>
    <form method="post">
        
        <div class="wrap-input100SN validate-input m-b-18" data-validate="Required">
            <span class="label-input100">Full Name</span>
            <input class="input100" type="text" name="researcher_first_name[]" placeholder="First Name" required>
            <span class="focus-input100"></span>
        </div>
        <div class="wrap-input100FN validate-input m-b-18" data-validate="Required">
            <input class="input100" type="text" name="researcher_last_name[]" placeholder="Last Name" required>
            <span class="focus-input100"></span>
        </div>
        <div class="wrap-input100MI">
            <input class="input100" type="text" name="researcher_middle_initial[]" placeholder="M.I." maxlength="2">
            <span class="focus-input100"></span>
        </div>
        </div>
        <label>Co-researchers</label>
        <div id="co-researcher-container"></div>
        <button type="button" onclick="addCoResearcher()" class="cobtn">Add
            Co-researcher</button><br>

            <input type="submit" value="submit" name="submit">
    </form>

</body>

</html>
<script>
function addCoResearcher() {
    const container = document.getElementById('co-researcher-container');
    const div = document.createElement('div');
    div.innerHTML = `
               
                <input type="text" name="researcher_first_name[]" placeholder="First Name" required>
                <input type="text" name="researcher_last_name[]" placeholder="Last Name" required>
                <input type="text" name="researcher_middle_initial[]" placeholder="M.I." maxlength="2">
                <button type="button" onclick="removeCoResearcher(this)">Remove</button>
            `;
    container.appendChild(div);
}

function removeCoResearcher(button) {
    button.parentElement.remove();
}
</script>


<div class="button-container">
        <!-- container not wrapped properly -->




        <!-- Edit Faculty Display Button -->
        <button class="action-button" onclick="openFacultyModal()">Edit Faculty </button>

        <!-- Faculty Modal -->
        <div class="modal" id="facultyModal" style="display:none;">
            <div class="modal-content">
                <span class="close" onclick="closeFacultyModal()"
                    style="cursor:pointer; margin-left:420px;">&times;</span>
                <h2>Edit Faculty Display</h2>

                <!-- Form for uploading picture -->
                <form id="facultyForm" action="edit_faculty.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="faculty_id" value="1"> <!-- Default ID for the faculty -->

                    <!-- Current Picture -->
                    <div id="current-picture-container">
                        <?php if ($current_picture1): ?>
                        <img id="current-picture" style="width:1000%; height: 1000%;"
                            src="Faculty Members/<?php echo $current_picture1; ?>" alt="Current Picture"
                            style="width: 200px;">

                        <?php else: ?>
                        <p>No picture available</p>
                        <?php endif; ?>
                    </div>

                    <!-- New Picture Upload -->
                    <label for="faculty_picture">Upload New Picture:</label>
                    <input type="file" name="faculty_picture" id="faculty_picture"><br>

                    <!-- Submit Button -->
                    <button type="button" id="remove-picture" onclick="removePicture()" style="margin-top:20px;">Remove
                        Picture</button>
                    <button type="submit" class="action-button">Save Changes</button>
                </form>
            </div>
        </div>

        <!-- Edit Schedule Display Button -->
        <button class="printbtn" onclick="openScheduleModal()">Edit Schedule</button>
        <!-- Schedule Modal -->
        <div class="modal1" id="scheduleModal" style="display:none;">
            <div class="modal-content">
                <span class="close" onclick="closeScheduleModal()"
                    style="cursor:pointer; margin-left:420px;">&times;</span>
                <h2>Edit Schedule Display</h2>

                <!-- Form for uploading picture -->
                <form id="scheduleForm" action="edit_schedule.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="schedule_id" value="1"> <!-- Default ID for the schedule -->

                    <!-- Current Picture -->
                    <div id="current-picture-container">
                        <?php if ($current_picture2): ?>
                        <img id="current-picture" style="width:1000%; height: 1000%;"
                            src="Schedules/<?php echo $current_picture2; ?>" alt="Current Picture"
                            style="width: 200px;">

                        <?php else: ?>
                        <p>No picture available</p>
                        <?php endif; ?>
                    </div>

                    <!-- New Picture Upload -->
                    <label for="schedule_picture">Upload New Picture:</label>
                    <input type="file" name="schedule_picture" id="schedule_picture"><br>

                    <!-- Submit Button -->
                    <button type="button" id="remove-picture" onclick=" removeSchedulePicture()"
                        style="margin-top:20px;">Remove Picture</button>
                    <button type="submit" class="action-button">Save Changes</button>
                </form>
            </div>
        </div>

        <button onclick="openvmModal()">Edit Vision and Mission</button>


        <!-- Form to edit Vision, Mission, and Goals -->
        <div class="modal2" id="vmForm">
        <div class="modal-content">
            <form action="edit_vm.php" method="post">
                    <span class="close" onclick="closevmModal()">&times;</span>
            <?php
            // Fetch the vision, mission, and goals from the database
            require 'dbConnCode.php'; // Include your database connection
            $sql_vm = "SELECT * FROM vision_mission";
            $result_vm = $conn->query($sql_vm);
            // Check if any Vision, Mission, or Goals exist
            if ($result_vm && $result_vm->num_rows > 0) {
                while ($row = $result_vm->fetch_assoc()) {
                    // Check for Vision, Mission, or Goals and display accordingly
                    if ($row['statement_type'] == 'Vision') {
                        echo "<label>Vision:</label><br>";
                        echo "<textarea name='content[]' rows='4' cols='50'>" . htmlspecialchars($row['content']) . "</textarea><br>";
                        echo "<input type='hidden' name='id[]' value='" . $row['id'] . "'><br>";
                    } elseif ($row['statement_type'] == 'Mission') {
                        echo "<label>Mission:</label><br>";
                        echo "<textarea name='content[]' rows='4' cols='50'>" . htmlspecialchars($row['content']) . "</textarea><br>";
                        echo "<input type='hidden' name='id[]' value='" . $row['id'] . "'><br>";
                    } elseif ($row['statement_type'] == 'Goals') {
                        echo "<label>Goals:</label><br>";
                        echo "<textarea name='content[]' rows='4' cols='50'>" . htmlspecialchars($row['content']) . "</textarea><br>";
                        echo "<input type='hidden' name='id[]' value='" . $row['id'] . "'><br>";
                    }
                }
            } else {
                // No Vision, Mission, or Goals exist, allow user to create new entries
                echo "<label>Vision:</label><br>";
                echo "<textarea name='content[]' rows='4' cols='50' placeholder='Enter your vision here...'></textarea><br>";
                echo "<input type='hidden' name='id[]' value='new_vision'><br>"; // Placeholder for new Vision ID
            
                echo "<label>Mission:</label><br>";
                echo "<textarea name='content[]' rows='4' cols='50' placeholder='Enter your mission here...'></textarea><br>";
                echo "<input type='hidden' name='id[]' value='new_mission'><br>"; // Placeholder for new Mission ID
            
                echo "<label>Goals:</label><br>";
                echo "<textarea name='content[]' rows='4' cols='50' placeholder='Enter your goals here...'></textarea><br>";
                echo "<input type='hidden' name='id[]' value='new_goals'><br>"; // Placeholder for new Goals ID
            }
            ?>
                    <input type="submit" value="Save Changes">
                </form>
        </div>
        </div>

        <a href="Colleges.php">
            <button>Manage Colleges</button>
        </a>

        <a href="dynamic_datas.php">
            <button>Manage Datas</button>
        </a>

        <!-- Button to Open Modal -->
        <button id="editFaqBtn">Edit FAQ</button>

        <!-- FAQ Modal -->
        <div id="faqModal" class="modal3">
            <div class="modal-content">
                <span class="close" onclick="closeFaqModal()">&times;</span>
                <h2>Manage FAQ</h2>

                <!-- Form to Edit/Add FAQ -->
                <form id="faqForm">
                    <input type="hidden" id="faqId">
                    <label for="question">Question:</label>
                    <textarea id="question" required></textarea>

                    <label for="answer">Answer:</label>
                    <textarea id="answer" required></textarea>

                    <button type="submit">Save</button>
                </form>

                <!-- FAQ List -->
                <h3>Existing FAQs</h3>
                <ul id="faqList"></ul>
            </div>
        </div>

    </div>