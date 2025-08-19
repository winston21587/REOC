<?php
session_start();
$id = $_GET['id'];

require_once '../../class/Admin.php';
$admin = new admin();

$researchData = $admin->getResearchtitle($id);


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Fill Review Form</title>
</head>
<style>
  #checkAllBtn {
    background: #fff;
    color: #333;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 7px 18px;
    font-size: 15px;
    cursor: pointer;
    margin-bottom: 15px;
    transition: border-color 0.2s, color 0.2s;
}

#checkAllBtn:hover, #checkAllBtn:focus {
    border-color: #888;
    color: #007bff;
    outline: none;
}
  body {
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
    margin: 0;
    padding: 20px;
  }

  h2 {
    color: #333;
    margin-top: 30px;
    justify-self: center;
  }

  form {
    background-color: #fff;
    padding: 25px;
    border-radius: 10px;
    max-width: 700px;
    margin: auto;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  }

  label {
    display: block;
    margin-bottom: 10px;
    font-weight: 500;
  }

  input[type="text"],
  select {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    box-sizing: border-box;
  }

  input[type="checkbox"] {
    margin-right: 10px;
    transform: scale(1.2);
  }

  button[type="submit"] {
    background-color: #007bff;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 20px;
    transition: background-color 0.3s ease;
  }

  button[type="submit"]:hover {
    background-color: #0056b3;
  }

  /* Spacing for grouped checkbox sections */
  .checkbox-group {
    margin-bottom: 20px;
  }

  @media (max-width: 768px) {
    form {
      padding: 15px;
    }

    input[type="text"],
    select {
      font-size: 14px;
    }

    button[type="submit"] {
      width: 100%;
    }
  }
</style>

<body>
  <h2>Generate Filled Research Ethics Review Form</h2>
  <form action="generate_RC_letter.php" method="POST" target="_blank">
    <label>Paper entitled:</label>
    <input type="text" name="title" value="<?= $researchData['study_protocol_title'] ?>">
    <input type="hidden" name="id" value="<?= $researchData['id'] ?>">
    <input type="hidden" name="email" value="<?= $researchData['email'] ?>">
    <label>Review Type:</label>
    <select name="review_type">
      <option value="EXEMPTED">EXEMPTED</option>
      <option value="EXPEDITED">EXPEDITED</option>
      <option value="FULL REVIEW">FULL REVIEW</option>
    </select>
    <div class="checkbox-group">
          <button type="button" id="checkAllBtn" style="margin-bottom:15px;">Check All</button>

    <h2>In the Protocol/Proposal:</h2>
    
    <label><input type="checkbox" name="ethics_review_1[]" value="1"> Anonymity/Confidentiality of the data</label><br>
    
    <label><input type="checkbox" name="ethics_review_1[]" value="2"> Plan on processing personal data, access, disposal and terms of use (Data Privacy Act of 2012)</label><br>
    
    <label><input type="checkbox" name="ethics_review_1[]" value="3"> Measures to protect privacy of participants</label><br>
    
    <label><input type="checkbox" name="ethics_review_1[]" value="4"> Appropriate mechanisms/interventions in place to address the vulnerability issues</label><br>
    
    <label><input type="checkbox" name="ethics_review_1[]" value="5"> Measures to mitigate the risks</label><br>
    
    <label><input type="checkbox" name="ethics_review_1[]" value="6"> Disclosure of conflict of Interest</label><br>
    
    
    
    <h2>In the Informed Consent:</h2>

<label><input type="checkbox" name="ethics_review_2[]" value="1"> Purpose of the study</label><br>

<label><input type="checkbox" name="ethics_review_2[]" value="2"> Expected duration of participation</label><br>

<label><input type="checkbox" name="ethics_review_2[]" value="3"> Adequate process for ensuring that consent is voluntary</label><br>

<label><input type="checkbox" name="ethics_review_2[]" value="4"> Procedures to be carried out</label><br>

<label><input type="checkbox" name="ethics_review_2[]" value="5"> Mechanisms in cases of discomforts and risks</label><br>

<label><input type="checkbox" name="ethics_review_2[]" value="6"> Benefits to the participants</label><br>

<label><input type="checkbox" name="ethics_review_2[]" value="7"> Compensations/reimbursement of expenses</label><br>

<label><input type="checkbox" name="ethics_review_2[]" value="8"> Withdrawal of participants from the study anytime without penalty</label><br>

<label><input type="checkbox" name="ethics_review_2[]" value="9"> Duties and responsibilities of participants</label><br>

<label><input type="checkbox" name="ethics_review_2[]" value="10"> Extent of confidentiality</label><br>

<label><input type="checkbox" name="ethics_review_2[]" value="11"> Ensuring that the language used in the instrument can be understood by the respondents/participants (Translate the instrument to the respondents/participants’ languages)</label><br>

<label><input type="checkbox" name="ethics_review_2[]" value="12"> Contact person</label><br>

<label><input type="checkbox" name="ethics_review_2[]" value="13"> Include REOC contact details</label><br>
</div>

    <label>Number of Sets to Submit:</label>
    <input type="text" name="num_sets">

    <label>Envelope Type:</label>
    <input type="text" name="envelope_type">



    <div class="checkbox-group" >
    <h2>Recommended Actions:</h2>

    <label><input type="checkbox" name="Recommended_Actions[]" value="1"> Pls. Incorporate required information </label><br>
    <label><input type="checkbox" name="Recommended_Actions[]" value="2"> For Payment at the University Cashier</label><br>

    </div>
    <label for="extraNotes">Extra Notes:</label>
    <textarea name="extraNotes" id="extraNotes" cols="90" rows="7" style="resize: none;"></textarea>
    <button type="submit" name="action" value="view">View PDF</button>
    <button type="submit" name="action" value="mail">Send to Email</button>
  </form>


</body>
</html>
<script>
document.getElementById('checkAllBtn').addEventListener('click', function() {
    // Get all checkboxes inside the form
    const form = this.closest('form');
    const checkboxes = form.querySelectorAll('input[type="checkbox"]');
    // Check if all are already checked
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    // Toggle all
    checkboxes.forEach(cb => cb.checked = !allChecked);
    // Change button text accordingly
    this.textContent = allChecked ? 'Check All' : 'Uncheck All';
});
</script>