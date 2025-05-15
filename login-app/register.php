<?php
include "partials/header.php";
include "partials/navigation.php";
ini_set('display_errors', 0);
$error = "";
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
}
if($password !== $confirm_password){
    $error = "passwords do not match";
}else{
    $sql = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) === 1){
        $error = "username already exists";
    }else{
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$passwordHash', '$email')";
        if(mysqli_query($conn, $sql)){
            echo "DATA INSERTED";
        }else{
            echo "DATA NOT INSERTED, error: " . mysqli_error($conn);
        }
    }
}

?>
    <div class="container">
<h2>Register</h2>
<?php if($error): ?>
<p style="color: red">
    <?php echo $error; ?>
</p>
<?php endif; ?>
<div class="form-container">
<form method="POST" action="">
            <label for="username">Username:</label><br>
            <input placeholder="Enter your username" type="text" name="username" required><br><br>

            <label for="email">Email:</label><br>
            <input placeholder="Enter your email" type="email" name="email" required><br><br>

            <label for="password">Password:</label><br>
            <input placeholder="Enter your password" type="password" name="password" required><br><br>

            <label for="confirm_password">Confirm Password:</label><br>
            <input placeholder="Confirm your password" type="password" name="confirm_password" required><br><br>

            <input type="submit" value="Register">
        </form>
</div>
    </div>
<?php
include "partials/footer.php";
?>
<?php
mysqli_close($conn);
?>