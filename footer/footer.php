<?php

?>
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<style>
/* Footer container */
.footer {
    width: 100%;
    background-color: #fff;
}

/* Background image with dark overlay */
.footer-background {
    position: relative;
    background-image: url('./img/wmsu3.jpg'); /* Add your background image URL */
    background-size: cover;
    background-position: center;
    padding: 50px 0;
    text-align: center;
    color: white;
}

.footer-background::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.6); /* Dark overlay */
    z-index: 1;
}

.footer-content {
    position: relative;
    z-index: 2;
    padding: 20px;
}

.footer-content h2 {
    font-size: 2em;
    margin-bottom: 10px;
}

.footer-content p {
    font-size: 1.1em;
    margin: 5px 0;
}

.footer-content a {
    color: #fff;
    text-decoration: none;
}

.footer-content a:hover {
    text-decoration: underline;
}

/* Bottom footer with dark reddish background */
.footer-bottom {
    background-color: #7a3b3b;
    color: white;
    padding: 20px 0;
    text-align: center;
}

.social-icons {
    display: flex;
    justify-content: center;
    gap: 20px;
}

.social-icon {
    color: white;
    font-size: 1.5em;
    text-decoration: none;
}

.social-icon:hover {
    color:rgb(0, 0, 0);
}

/* Responsive Design */
@media (max-width: 768px) {
    .footer-content h2 {
        font-size: 1.5em;
    }
    .footer-content p {
        font-size: 1em;
    }
    .social-icons {
        flex-direction: column;
    }
    .social-icon {
        margin-bottom: 10px;
    }
}

</style>

<footer class="footer">
    <div class="footer-background">
        <div class="footer-content">
            <h2>WMSUReoc</h2>
            <p>Location: 2nd Floor, Research Center, Western Mindanao State University, Normal Road, Baliwasan, Zamboanga City</p>
            <p>Email: <a href="mailto:reoc@wmsu.edu.ph">reoc@wmsu.edu.ph</a></p>
            <p>Number: <a href="tel:+639976323622">0997 632 3622</a></p>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="social-icons">
            <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
        </div>
    </div>
</footer>
