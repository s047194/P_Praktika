<?php
include "partials/header.php";
include "partials/navigation.php";
//if(is_user_logged_in()){
//    header('Location: admin.php');
//    exit;
//}
$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $sql = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) === 1){
        $user = mysqli_fetch_assoc($result);
        if(password_verify($password, $user["password"])){
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $user["username"];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            if ($user['role'] === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: index.php");
            }
            exit;
        }else{
            $error = "Wrong password";
        }
    }else{
        $error = "Invalid username";
    }
}


?>
    <div class="container">
        <div class="form-container">
    <form method="POST" action="">
        <h2>Login</h2>
        <?php if($error): ?>
            <p style="color: red">
                <?php echo $error; ?>
            </p>
        <?php endif; ?>
        <label for="username">Username:</label><br>
        <input placeholder="Enter your username" type="text" name="username" required><br><br>

        <label for="password">Password:</label><br>
        <input placeholder="Enter your password" type="password" name="password" required><br><br>

        <input type="submit" value="Login">
    </form>
            <p class="register-link">
                Neturite paskyros? <a href="register.php">Registruotis</a>
            </p>
        </div>
    </div>
<?php
include "partials/footer.php";
?>
<?php
mysqli_close($conn);
?>