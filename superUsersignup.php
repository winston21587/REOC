<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superuser Signup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f5f5;
        }

        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        button {
            padding: 10px 20px;
            margin: 10px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        form {
            margin-top: 20px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Superuser Signup</h2>
    
    <!-- Admin and Reviewer selection -->
    <div>
        <button onclick="redirectToForm('Admin')">Create Admin Account</button>
        <button onclick="redirectToForm('Reviewer')">Create Reviewer Account</button>
    </div>
        
</div>

<script>
    function redirectToForm(role) {
        // Prevent potential XSS by encoding the role
        const safeRole = encodeURIComponent(role);

         // Redirect to the appropriate PHP file based on the role
         if (safeRole === 'Admin') {
            window.location.href = 'superSendcode.php?role=' + safeRole; // For Admin
        } else if (safeRole === 'Reviewer') {
            window.location.href = 'superSendcoderRev.php?role=' + safeRole; // For Reviewer
        } else {
            alert("Invalid role."); // Show an alert if the role is invalid
        }
    }
</script>


</body>
</html>
