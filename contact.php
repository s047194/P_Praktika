<?php
include "partials/header.php";
include "partials/navigation.php";

// Klaidos / sėkmės pranešimai
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Apsauga nuo XSS ir tiesioginis vartotojo duomenų tvarkymas
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $msg = trim($_POST['message']);

    if (!empty($name) && !empty($email) && !empty($msg)) {
        // Paruošiame SQL užklausą
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $msg);

        if ($stmt->execute()) {
            $message = "Žinutė sėkmingai išsiųsta. Ačiū!";
        } else {
            $message = "Įvyko klaida siunčiant žinutę. Bandykite dar kartą.";
        }

        $stmt->close();
    } else {
        $message = "Prašome užpildyti visus laukus.";
    }
}
?>

<div class="contact-wrapper">
    <div class="contact-header">
        <h1>Kontaktai</h1>
        <p>Jei kilo klausimų – susisiekite!</p>
    </div>

    <div class="contact-container">
        <div class="contact-box">
            <ul class="contact-info">
                <li><img src="icons/email.svg" alt="El. paštas"> kristupas@flipbook.lt</li>
                <li><img src="icons/phone.svg" alt="Telefonas"> +37065555555</li>
                <li><img src="icons/info.svg" alt="Vardas"> Kristupas <br> Pažymos nr.: 0000000</li>
                <li><img src="icons/facebook.svg" alt="Facebook"> Flipbook.lt</li>
                <li><img src="icons/instagram.svg" alt="Instagram"> Flipbook_lt</li>
            </ul>
        </div>

        <div class="contact-box">
            <h2>Parašykite mums</h2>

            <?php if ($message): ?>
                <p class="form-message"><?= $message ?></p>
            <?php endif; ?>

            <form action="#" method="post" class="contact-form">
                <div class="form-row">
                    <input type="text" name="name" placeholder="Vardas" required value="<?= isset($name) ? htmlspecialchars($name) : '' ?>">
                    <input type="email" name="email" placeholder="El. paštas" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
                </div>
                <textarea name="message" placeholder="Žinutė" rows="5" required><?= isset($msg) ? htmlspecialchars($msg) : '' ?></textarea>
                <button type="submit">Siųsti</button>
            </form>
        </div>
    </div>
</div>

<?php include "partials/footer.php"; ?>
