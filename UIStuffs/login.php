<?php ?>
<script src="https://cdn.tailwindcss.com"></script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">REOC Portal</h1>
                <p class="text-gray-600">Research Ethics Oversight Committee</p>
            </div>
            
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Welcome back!</h2>
                <p class="text-gray-600">How do I get started with my research ethics review?</p>
            </div>
            
            <form class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition" placeholder="Enter your email">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition" placeholder="Enter your password">
                </div>
                
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <label for="remember" class="ml-2 text-sm text-gray-700">Remember me</label>
                    </div>
                    <a href="forgot-password.html" class="text-sm text-red-600 hover:text-red-700 font-medium">Forgot Password?</a>
                </div>
                
                <button type="submit" class="w-full bg-red-600 text-white py-3 rounded-lg font-semibold hover:bg-red-700 transition focus:ring-2 focus:ring-red-500 focus:ring-offset-2">Login</button>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-gray-600">Don't have an account? <a href="create-account.html" class="text-red-600 hover:text-red-700 font-medium">Sign up</a></p>
            </div>
        </div>
        
        <div class="bg-red-50 p-4 text-center">
            <p class="text-red-800 font-medium">Important research ethics reviews are waiting for you. Login now!</p>
        </div>
    </div>
</body>
</html>