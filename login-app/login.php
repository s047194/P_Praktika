<?php
include "partials/header.php";
include "partials/navigation.php";
if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true){
    header('Location: admin.php');
    exit;
}
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
            header("location: admin.php");
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
    <?php if($error): ?>
        <p style="color: red">
            <?php echo $error; ?>
        </p>
    <?php endif; ?>
        <div class="form-container">
    <form method="POST" action="">
        <label for="username">Username:</label><br>
        <input placeholder="Enter your username" type="text" name="username" required><br><br>

        <label for="password">Password:</label><br>
        <input placeholder="Enter your password" type="password" name="password" required><br><br>

        <input type="submit" value="Login">
    </form>
        </div>
    </div>
<?php
include "partials/footer.php";
?>
<?php
mysqli_close($conn);
?>