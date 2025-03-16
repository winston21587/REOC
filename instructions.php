<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructions</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/swiper.css">
    <link rel="icon" type="image/x-icon" href="./img/reoclogo1.jpg">
</head>
<style>
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
  }

  .header, .footer {
    background-color: #f8f9fa;
    padding: 20px;
    text-align: center;
  }

  .header .brand {
    display: flex;
    align-items: center;
  }

  .header .brand .logo {
    height: 50px;
    margin-right: 10px;
  }

  .header .navigation {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .header .navigation-items a {
    margin: 0 10px;
    text-decoration: none;
    color: #000;
  }

  .header .logout-button {
    background-color: #dc3545;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-left: 20px;
  }

  .header .logout-button:hover {
    background-color: #c82333;
  }

  .vision {
    text-align: center;
    margin: 20px 0;
    font-size: 24px;
  }

  .section1 {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
  }

  .card-content1 {
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    max-width: 900px; /* Increased width */
    margin: 0 auto;
  }

  .card-content1 ul {
    list-style-type: disc;
    padding-left: 20px;
  }

  .footer {
    background-color: #343a40;
    color: #fff;
    padding: 20px;
    text-align: center;
  }

  .footer__redes-wrapper {
    list-style: none;
    padding: 0;
  }

  .footer__redes-wrapper li {
    margin: 10px 0;
  }

  .footer__link {
    color: #fff;
    text-decoration: none;
  }

  .footer__link:hover {
    text-decoration: underline;
  }

  @media screen and (max-width: 1440px) {
    .container {
        width: 70%;
        background: #f8d7da;
    }
  }

  @media screen and (max-width: 1024px) {
    .container {
        width: 80%;
        background: #d1ecf1;
    }
  }

  @media screen and (max-width: 768px) {
    .header .brand .reoc {
      display: none;
    }

    .header .navigation {
      flex-direction: column;
    }

    .header .navigation-items {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .header .navigation-items a {
      margin: 10px 0;
    }

    .vision {
      font-size: 20px;
    }

    .card-content1 {
      padding: 15px;
    }

    .card-content1 ul {
      padding-left: 15px;
    }

    .name-fields {
        flex-direction: column;
    }

    .name-fields .wrap-input100SN, 
    .name-fields .wrap-input100FN, 
    .name-fields .wrap-input100MI {
        width: 100%;
    }

    .wrap-input100, 
    .wrap-input200, 
    .wrap-input1001 {
        width: 100%;
    }

    .login100-form {
        padding: 20px;
    }

    .login100-form-btn, 
    .login100-form-btn1, 
    .login100-form-btn2 {
        width: 100%;
        margin: 10px 0;
    }
  }

  @media screen and (max-width: 425px) {
    .container {
        width: 95%;
        background: #ffebcd;
        padding: 15px;
    }

    h2 {
        font-size: 6vw;
    }

    p {
        font-size: 5vw;
    }

    .header, .footer {
        padding: 10px;
    }

    .content {
        padding: 10px;
    }

    .logout-button {
        padding: 8px 16px;
        margin-left: 10px;
    }

    .wrap-input100, 
    .wrap-input200, 
    .wrap-input1001 {
        width: 100%;
    }

    .name-fields {
        flex-direction: column;
    }

    .name-fields .wrap-input100SN, 
    .name-fields .wrap-input100FN, 
    .name-fields .wrap-input100MI {
        width: 100%;
    }

    .login100-form {
        padding: 20px;
    }

    .login100-form-btn, 
    .login100-form-btn1, 
    .login100-form-btn2 {
        width: 100%;
        margin: 10px 0;
    }
  }

  @media screen and (max-width: 375px) {
    .container {
        width: 100%;
        background: #c3e6cb;
        padding: 12px;
    }

    h2 {
        font-size: 7vw;
    }

    p {
        font-size: 5.5vw;
    }
  }
</style>
<body>

<?php 
  include './navbar/navbar.php';
?>

  <h1 class="vision">REOC Application Instructions</h1>
  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-md-10"> <!-- Increased column width -->
        <div class="card">
          <div class="card-body">
            <h2 class="card-title text-center" style="background-color: #800000; color: white; padding: 10px; border-radius: 8px;">Instructions</h2>
            <p class="card-text">1. Download copies of the Application Form and the applicable Assessment Forms in the Downloadables tab.</p>
            <p class="card-text">2. Comply relevant documents for Research Ethics Review with responding file type:</p>
            <ul>
              <li>Application Form for Research Ethics Review - WMSU-REOC-FR-001 (with researcher/s signature in pdf file)</li>
              <li>Research Protocol/Proposal (with page and line number in pdf file)</li>
              <li>Technical Review Clearance (pdf file)</li>
              <li>Data collection instrument/s (with page and line number in pdf file)</li>
              <li>Informed Consent/Assent (with page and line number in pdf file)</li>
              <li>Curriculum Vitae of Researcher/s (pdf file)</li>
              <li>Completed Study Protocol Assessment Form - WMSU-REOC-FR-004 (fill up the required details with asterisks in word file)</li>
              <li>Completed Informed Consent Assessment Form - WMSU-REOC-FR-005 (fill up the required details with asterisks in word file)</li>
              <li>Completed Exempt Review Assessment Form - WMSU-REOC-FR-006 (fill up the required details with asterisks in word file)</li>
              <li>Other documents (NCIP Clearance, MOA, MOU, etc. in pdf file)</li>
            </ul>
            <p class="card-text">3. Upon accomplishment of google form wait for further notification from WMSU REOC thru your GMAIL for the Research Ethics Review documents hardcopies submission appointment date.</p>
            <p class="card-text">4. On the hardcopies submission appointment date, documents must be placed in a long expanded envelope following the color coding (see College/Institution Section) with printed seal.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <footer class="footer">
    <div class="owl-carousel">
      <a href="#" class="gallery__photo">
        <img src="img/wmsu55.jpg" alt="" />
      </a>
      <a href="#" class="gallery__photo">
        <img src="img/wmsu11.jpg" alt="" />
      </a>
      <a href="#" class="gallery__photo">
        <img src="img/reoc11.jpg" alt="" />
      </a>
      <a href="#" class="gallery__photo">
        <img src="img/wmsu22.jpg" alt="" />
      </a>
      <a href="#" class="gallery__photo">
        <img src="img/reoc22.jpg" alt="" />
      </a>
      <a href="#" class="gallery__photo">
        <img src="img/wmsu44.jpg" alt="" />
      </a>
    </div>
    <div class="footer__redes">
      <ul class="footer__redes-wrapper">
        <li>
          <a href="#" class="footer__link">
            <i class=""></i>
            Normal Road, Baliwasan, Z.C.
          </a>
        </li>
        <li>
          <a href="#" class="footer__link">
            <i class=""></i>
            09112464566
          </a>
        </li>
        <li>
          <a href="#" class="footer__link">
            <i class=""></i>
            wmsureoc@gmail.com
          </a>
        </li>
        <li>
          <a href="#" class="footer__link">
            <i class="fab fa-phone-alt"></i>
          </a>
        </li>
      </ul>
    </div>
    <div class="separador"></div>
    <p class="footer__texto">RESEARCH ETHICS OVERSITE COMMITTEE - WMSU</p>
  </footer>

  <script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>
  <script src='https://unpkg.com/feather-icons'></script>
  <script src="footer.js"></script>
  <script src="./script.js"></script>
  <script src="./js/main.js"></script>
  <script src="./js/swiper.js"></script>
  <script src="./js/footer.js"></script>
  <script src="./js/faq.js"></script>
</body>
</html>