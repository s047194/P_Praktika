<?php
include "partials/header.php";
include "partials/navigation.php";
ini_set('display_errors', 0);

if (is_user_logged_in()) {
    header('Location: admin.php');
    exit;
}
$username = "";
$email = "";
$password = "";
$confirm_password = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        if (user_exists($conn, $username)) {
            $error = "Username already exists";
        } else {
            if (check_query(create_user($conn, $username, $email, $password))) {
                redirect("login.php");
                } else {
                $error = "DATA NOT INSERTED, error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<div class="container">
    <div class="form-container">
        <form method="POST" action="">
            <h2>Create Account</h2>
            <?php if ($error): ?>
                <p style="color: red">
                    <?php echo $error; ?>
                </p>
            <?php endif; ?>

            <label for="username">Username:</label>
            <input value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" placeholder="Enter your username" type="text" name="username" required>

            <label for="email">Email:</label>
            <input value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" placeholder="Enter your email" type="email" name="email" required>

            <label for="password">Password:</label>
            <input placeholder="Enter your password" type="password" name="password" required>

            <label for="confirm_password">Confirm Password:</label>
            <input placeholder="Confirm your password" type="password" name="confirm_password" required>

            <input type="submit" value="Register">
        </form>
    </div>
</div>

<?php include "partials/footer.php"; ?>
<?php mysqli_close($conn); ?>
