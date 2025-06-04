<?php
include "partials/header.php";
include "partials/navigation.php";
?>
<div class="container">
<div class="hero">
    <div class="hero-content">
        <h1>Sveiki atvykę!</h1>
        <p>Paverskite savo video į nepakartojamą prisiminimą</p>
        <div class="hero-buttons">
            <?php if(!is_user_logged_in()): ?>

            <a class="btn" href="login.php">Login</a>
            <a class="btn" href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
</div>
</div>
<?php include "partials/footer.php"; ?>

