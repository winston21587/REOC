<?php ?>
<script src="https://cdn.tailwindcss.com"></script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer</title> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content {
            flex: 1;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="content p-8">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-4">I MISS HERRRRR</h1>
        <p class="text-center text-gray-600 max-w-2xl mx-auto">balik kana pls</p>
    </div>

    <footer class="bg-white border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="col-span-1 lg:col-span-2">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-red-600 rounded-lg flex items-center justify-center mr-3">
                            <div class="text-white font-bold text-xs text-center">LOGO</div>
                        </div>
                        <span class="text-xl font-bold text-gray-800">REOC Portal</span>
                    </div>
                    <p class="text-gray-600 mb-4 max-w-md">
                        The Research Ethics Oversight Committee ensures ethical standards in academic research. 
                        Our online portal streamlines the submission and review process for researchers and committee members.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-red-600 transition">
                            <i class="fab fa-facebook-f text-lg"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-red-600 transition">
                            <i class="fab fa-instagram text-lg"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-600 hover:text-red-600 transition">Submit Research</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-red-600 transition">Review Status</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-red-600 transition">Committee Members</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-red-600 transition">Ethics Guidelines</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-red-600 transition">FAQs</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Contact Us</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt text-red-600 mt-1 mr-3"></i>
                            <span class="text-gray-600">Research Ethics Office, Academic Building, University Campus</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone text-red-600 mr-3"></i>
                            <span class="text-gray-600">+1 (555) 123-4567</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope text-red-600 mr-3"></i>
                            <span class="text-gray-600">ethics.committee@university.edu</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock text-red-600 mr-3"></i>
                            <span class="text-gray-600">Mon - Fri: 9:00 AM - 5:00 PM</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-200 mt-10 pt-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-500 text-sm mb-4 md:mb-0">
                        &copy; 2023 Research Ethics Oversight Committee. All rights reserved.
                    </p>
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-500 hover:text-red-600 text-sm transition">Privacy Policy</a>
                        <a href="#" class="text-gray-500 hover:text-red-600 text-sm transition">Terms of Service</a>
                        <a href="#" class="text-gray-500 hover:text-red-600 text-sm transition">Accessibility</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>