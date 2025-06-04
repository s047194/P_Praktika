<?php
include "partials/header.php";
include "partials/navigation.php";
if (!is_user_logged_in()) {
    redirect("login.php");
}

$user_id = $_SESSION["user_id"];

$order_result = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");
?>

<h1>Mano užsakymai</h1>
<div class="container">
    <?php if (isset($_SESSION["message"])): ?>
        <div class="notification <?php echo $_SESSION['msg_type'] ?>">
            <?php
            echo $_SESSION['message'];
            unset($_SESSION['message']);
            unset($_SESSION['msg_type']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($order_result) > 0): ?>
        <table class="user-table">
            <thead>
            <tr>
                <th>Užsakymo ID</th>
                <th>Pavadinimas</th>
                <th>Statusas</th>
                <th>Sukurta</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($order = mysqli_fetch_assoc($order_result)) : ?>
                <tr>
                    <td data-label="Užsakymo ID"><?php echo $order['id']; ?></td>
                    <td data-label="Pavadinimas"><?php echo htmlspecialchars($order['title']); ?></td>
                    <td data-label="Statusas"><?php echo ucfirst($order['status']); ?></td>
                    <td data-label="Sukurta"><?php echo full_month_date($order['created_at']); ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Neturite jokių užsakymų.</p>
    <?php endif; ?>
</div>

<?php include "partials/footer.php"; ?>
