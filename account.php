<?php
include "partials/header.php";
include "partials/navigation.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// Gaunam esamą vartotojo informaciją
$stmt = $conn->prepare("SELECT username, email, password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $current_hashed_password);
$stmt->fetch();
$stmt->close();

// Atnaujiname bendrą informaciją
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['update_info'])) {
        $new_name = htmlspecialchars(trim($_POST["name"]));
        $new_email = htmlspecialchars(trim($_POST["email"]));

        if (!empty($new_name) && !empty($new_email)) {
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $new_name, $new_email, $user_id);

            if ($stmt->execute()) {
                $message = "Informacija atnaujinta sėkmingai.";
                $name = $new_name;
                $email = $new_email;
            } else {
                $message = "Klaida atnaujinant duomenis.";
            }

            $stmt->close();
        } else {
            $message = "Visi laukai privalomi.";
        }
    }

    // Slaptažodžio keitimas
    if (isset($_POST['change_password'])) {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $repeat_password = $_POST['repeat_password'];

        if (password_verify($old_password, $current_hashed_password)) {
            if ($new_password === $repeat_password) {
                if (strlen($new_password) >= 6) {
                    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->bind_param("si", $new_hashed_password, $user_id);
                    if ($stmt->execute()) {
                        $message = "Slaptažodis sėkmingai pakeistas.";
                    } else {
                        $message = "Klaida keičiant slaptažodį.";
                    }
                    $stmt->close();
                } else {
                    $message = "Naujas slaptažodis turi būti bent 6 simboliai.";
                }
            } else {
                $message = "Nauji slaptažodžiai nesutampa.";
            }
        } else {
            $message = "Neteisingas dabartinis slaptažodis.";
        }
    }
}
?>

<div class="account-container">
    <h2>Mano paskyra</h2>

    <?php if ($message): ?>
        <p class="form-message"><?= $message ?></p>
    <?php endif; ?>

    <!-- Informacijos atnaujinimo forma -->
    <form action="account.php" method="post" class="account-form">
        <input type="hidden" name="update_info" value="1">
        <label for="name">Vardas:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>

        <label for="email">El. paštas:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

        <button type="submit">Atnaujinti informaciją</button>
    </form>

    <!-- Slaptažodžio keitimo forma -->
    <h3>Keisti slaptažodį</h3>
    <form action="account.php" method="post" class="account-form">
        <input type="hidden" name="change_password" value="1">
        <label for="old_password">Dabartinis slaptažodis:</label>
        <input type="password" id="old_password" name="old_password" required>

        <label for="new_password">Naujas slaptažodis:</label>
        <input type="password" id="new_password" name="new_password" required>

        <label for="repeat_password">Pakartokite naują slaptažodį:</label>
        <input type="password" id="repeat_password" name="repeat_password" required>

        <button type="submit">Keisti slaptažodį</button>
    </form>
</div>

<?php include "partials/footer.php"; ?>
