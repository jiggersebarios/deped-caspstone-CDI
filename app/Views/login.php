</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR ARCHIVING SYSTEM</title>
    <link rel="stylesheet" href="/deped/public/css/style.css">
</head>
<body class="login">
<div class="login-container">
    <div class="branding-column">
        <div class="logo">
            <img src="/deped/public/uploads/pics/matatag.png" alt="MATATAG Logo" class="logo-image">
            <img src="/deped/public/uploads/pics/deped.png" alt="DepEd Logo" class="logo-image">
        </div>
        <h1>DEPED ARCHIVING SYSTEM</h1>
    </div>
</div>
<div class="card">
    <div class="mobile-branding">
        <img src="/deped/public/uploads/pics/deped.png" alt="DepEd Logo" class="logo-image">
        <h3>DEPED ARCHIVING SYSTEM</h3>
    </div>

    <h2>Login</h2>

<?php if (session()->getFlashdata('error')): ?>
    <p class="error-message"><?= session()->getFlashdata('error') ?></p>
<?php endif; ?>
  <form method="post" action="<?= site_url('login/auth') ?>" id="loginForm">
        <input type="text" name="username" placeholder="Enter your Username" required>
        <input type="password" name="password" placeholder="Enter your Password" required>
       <button type="submit" name="login" id="loginButton">Login</button>


    </form>
    <div class="terms-container">
        <input type="checkbox" id="termsCheckbox" name="terms" required form="loginForm">
        <label for="termsCheckbox">I agree to the <span class="terms-link">Terms and Conditions</span>.</label>
    </div>

</div>

<!-- The Modal -->
<div id="termsModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>Data Privacy Compliance</h2>
        <p>
            By checking the "I agree" box, you consent to the collection, generation, use, processing, storage, and retention of your personal information and sensitive personal information by the Department of Education for the purpose of this archiving system.
        </p>
        <p>
            You agree to comply with the <strong>Data Privacy Act of 2012 (Republic Act No. 10173)</strong> and the rules and regulations of this system. Unauthorized disclosure, sharing, or use of any data herein is strictly prohibited and will be subject to administrative and legal action.
        </p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const termsLink = document.querySelector('.terms-link');
    const modal = document.getElementById('termsModal');
    const closeButton = document.querySelector('.close-button');

    // Optional: show modal when hovering over the Terms link
    termsLink.addEventListener('mouseenter', () => modal.style.display = 'block');
    termsLink.addEventListener('mouseleave', () => modal.style.display = 'none');
    closeButton.addEventListener('click', () => modal.style.display = 'none');
});

</script>
</body>


    <style>
        /* ---------- Styles for Login Page ---------- */
    .login {
        margin: 0;
        height: 100vh; /* Set a fixed viewport height */
        width: 100vw; /* Set a fixed viewport width */
        overflow: hidden; /* Prevent scrolling */
        display: flex;
        justify-content: center;
        align-items: center;
        background-image: url('/deped/public/uploads/pics/background.jpg');
        background-size: cover;
        background-attachment: fixed; /* Keep background in place on scroll */
        background-position: center;
        background-repeat: no-repeat;
        font-family: Arial, sans-serif;
        color: white;
        padding: 20px;
        position: relative; /* Needed for the ::before pseudo-element */
    }

    .login-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        max-width: 1400px; /* Give a bit more space */
        z-index: 2;
    }

    .branding-column {
        display: flex;
        flex-direction: column;
        align-items: center; /* Align logo and h1 to the left */
        gap: 1px; /* Space between logo and h1 */
        margin-bottom: 300px;
        margin-left: 200px;
        margin-top: 90px;
        margin-left: 100px;

    }
    
    /* Hide mobile branding on desktop */
    .mobile-branding {
        display: none;
        flex-direction: column;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
        mar
    }
    .mobile-branding h3 {
        color: #0033A0;
        font-size: 1.5em;
        font-weight: bold;
    }
    .mobile-branding h3 {
        font-size: 1.2em; /* Adjusted for smaller screens */
    }

    /* Styles for the logo alignment */
.login .logo {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 50px; /* Adjust spacing between the logos */
    z-index: 2;
    user-select: none;
}

.login .logo img {
    height: 150px; /* Set the desired uniform height */
    width: auto;
    object-fit: contain;
}

    .login::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to top right, 
            rgba(225, 31, 30, 0.8) 10%,
            rgba(95, 32, 110, 0.7) 50%,   
            rgba(0, 51, 160, 0.7) 70%     
        );
        z-index: 1;
        pointer-events: none;
    }

    .login h1 {
        font-size: clamp(2em, 5vw, 4em);
        font-weight: bold;
        z-index: 2;
        user-select: none;
        cursor: default;
    }

    .login .card {
        width: 400px;
        min-height: 500px;
        padding: 60px 30px 30px;
        background-color: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        gap: 2px;
        flex-shrink: 0; /* Prevents the card from shrinking */
        z-index: 2;
        margin-right: 200px;
        margin-bottom: 100px;
        
    }

    .login .card h2 {
        font-size: 2em;
        margin: 0;
        color: #0033A0;
        user-select: none;
        cursor: default;
        margin-bottom: 40px;
    }

    .login .card form {
        width: 100%;
        max-width: 320px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
    }

    .login .card input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 1.2em;
        box-sizing: border-box;
    }
    .login .card button {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 1.2em;
        box-sizing: border-box;
    }

    .login .card button {
        top: 0;
        background-color: #0033A0;
        color: white;
        border: none;
        cursor: pointer;
    }

    .login .card button:hover {
        background-color: #002080;
    }

    .login .card p {
        margin: 10px 0;
        font-size: 1em;
        text-align: center;
    }

    .login .card a {
        color: #0033A0;
        text-decoration: none;
        font-weight: bold;
    }

    .login .card a:hover {
        text-decoration: underline;
    }

    
    .login .error-message {
        color: red;
        text-align: center;
        margin-top: 10px;
    }

    /* Terms and Conditions Styles */
    /* Terms and Conditions container */
.terms-container {
    display: flex;
    align-items: center;  /* vertically center checkbox with text */
    justify-content: center; /* horizontally center inside card */
    gap: 10px; /* space between checkbox and label */
    margin-top: 20px; /* space from form above */
    text-align: center;
    font-size: 0.9em;
}

.terms-container input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.terms-container label {
    cursor: pointer;
    color: #333; /* dark text for readability */
}

.terms-container .terms-link {
    color: #0033A0;
    text-decoration: underline;
    cursor: pointer;
}

 
   

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.5);
        color: #333;
    }

    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px 30px;
        border: 1px solid #888;
        width: 80%;
        max-width: 500px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        position: relative;
    }

    .modal-content h2 {
        color: #0033A0;
        margin-top: 0;
    }

    .close-button {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    /* ===================== RESPONSIVE ===================== */
    @media (max-width: 1632px) {
        .login h1 {
            font-size: clamp(2em, 3vw, 3em); /* Make font smaller on medium-large screens */
        }
        .login .logo img {
            height: 120px; /* Make logo smaller on medium-large screens */
        }
        .login .card {
            width: 300px; /* 25% smaller than 400px */
            min-height: 375px; /* 25% smaller than 500px */
            padding: 40px 25px 25px; /* Adjust padding for smaller card */
            font-size: clamp(1em, 1vw, 1em);;
        }
    }

    @media (max-width: 1200px) {
        .login h1 {
            left: 100px;
            font-size: clamp(2em, 4.5vw, 3.5em);
        }
        .login .card {
            right: 100px;
        }
    }

        @media (max-width: 1200px) {
        .login h1 {
            left: 100px;
            font-size: clamp(1em, 2.5vw, 2.5em);
        }
       

         .login .card {
            width: 240px; /* 25% smaller than 400px */
            min-height: 275px; /* 25% smaller than 500px */
            padding: 40px 25px 25px; /* Adjust padding for smaller card */
            font-size: clamp(.7em, .8vw, .9em);
            margin-right: 250px;
        }
    }

    @media (max-width: 995px) {
        
        .login {
            height: auto;
            min-height: 100vh;
            align-items: flex-start;
            padding-top: 5vh;

        }

        /* Hide the desktop branding column */
        .branding-column {
            display: none;
        }

        /* Show the mobile branding inside the card */
        .mobile-branding {
            display: flex;
        }
        .mobile-branding .logo-image {
            height: 80px;
        }
        .mobile-branding h3 {
            font-size: 1.2em; /* Adjusted for smaller screens */
        }

        .login-container {
            display: none; /* Hide the desktop container */
        }

        .login .card {
            width: 100%;
            max-width: 400px;
            height: auto;
            background-color: rgba(255, 255, 255, 0.9);
            margin: 0; /* Reset desktop margins */
            padding: 30px;
        }
    }

    </style>


</html>