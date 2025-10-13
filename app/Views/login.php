<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR ARCHIVING SYSTEM</title>
    <link rel="stylesheet" href="/cdi/deped/public/css/style.css">
</head>
<body class="login">
<div class="logo">
    <img src="/cdi/deped/public/uploads/pics/matatag.png" alt="MATATAG Logo" class="logo-image">
    <img src="/cdi/deped/public/uploads/pics/deped.png" alt="DepEd Logo" class="logo-image">
</div>

<h1>DEPED ARCHIVING SYSTEM</h1>

<div class="card">
    <h2>Login</h2>

<?php if (session()->getFlashdata('error')): ?>
    <p class="error-message"><?= session()->getFlashdata('error') ?></p>
<?php endif; ?>
  <form method="post" action="<?= site_url('login/auth') ?>">
        <input type="text" name="username" placeholder="Enter your Username" required>
        <input type="password" name="password" placeholder="Enter your Password" required>
        <button type="submit" name="login">Login</button>
    
    </form>
</div>
</body>


    <style>
        /* ---------- Styles for Login Page ---------- */
    .login {
        margin: 0;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        background-image: url('/cdi/deped/public/uploads/pics/background.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        font-family: Arial, sans-serif;
        color: white;
    }
    
    /* Styles for the logo alignment */
.login .logo {
    position: absolute;
    top: 29%;
    left: 35%;
    transform: translate(-50%, -50%);
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
            rgba(225, 31, 30, 8) 10%,
            rgba(95, 32, 110, 0.7) 50%,   
            rgba(0, 51, 160, 0.7) 70%     
        );
        z-index: 1;
        pointer-events: none;
    }

    .login h1 {
        position: absolute;
        top: 300px;
        left: 200px;
        font-size: 4em;
        font-weight: bold;
        z-index: 2;
        user-select: none;
        cursor: default;
    }

    .login .card {
        position: absolute;
        top: 100px;
        right: 150px;
        width: 350px;
        height: 500px;
        padding: 30px;
        background-color: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        z-index: 2;
    }

    .login .card h2 {
        margin: 0 0 20px;
        font-size: 2em;
        color: #0033A0;
        user-select: none;
        cursor: default;
    }

    .login .card form {
        width: 100%;
        padding-top: 60px;
        max-width: 320px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
    }

    .login .card input,
    .login .card button {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 1.2em;
        box-sizing: border-box;
    }

    .login .card button {
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
    </style>
</html>
