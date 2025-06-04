<?php
include "partials/header.php";
include "partials/navigation.php";
if(!is_user_logged_in()){
    redirect("login.php");
}
$order_result = mysqli_query($conn, "SELECT o.id, u.username, o.title, o.status, o.created_at 
                                     FROM orders o 
                                     JOIN users u ON o.user_id = u.id 
                                     ORDER BY o.created_at DESC");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Egzistuojantys naudotojų POST blokai ...

    // UPDATE order status
    if (isset($_POST["edit_order"])) {
        $order_id = mysqli_real_escape_string($conn, $_POST["order_id"]);
        $status = mysqli_real_escape_string($conn, $_POST["status"]);

        $query = "UPDATE orders SET status = '$status' WHERE id = $order_id";
        $query_status = check_query(mysqli_query($conn, $query));

        if ($query_status === true) {
            $_SESSION["message"] = "Užsakymo statusas atnaujintas sėkmingai.";
            $_SESSION["msg_type"] = "success";
        } else {
            $_SESSION["message"] = $query_status;
            $_SESSION["msg_type"] = "error";
        }
        redirect("admin.php");
    }

    // DELETE order
    if (isset($_POST["delete_order"])) {
        $order_id = mysqli_real_escape_string($conn, $_POST["order_id"]);

        $query = "DELETE FROM orders WHERE id = $order_id";
        $query_status = check_query(mysqli_query($conn, $query));

        if ($query_status === true) {
            $_SESSION["message"] = "Užsakymas sėkmingai ištrintas.";
            $_SESSION["msg_type"] = "success";
        } else {
            $_SESSION["message"] = $query_status;
            $_SESSION["msg_type"] = "error";
        }

        redirect("admin.php");
    }
    if (isset($_POST["start_order"])) {
        $order_id = intval($_POST["order_id"]);

        // Gaunam užsakymo duomenis
        $query = "SELECT * FROM orders WHERE id = $order_id LIMIT 1";
        $result = mysqli_query($conn, $query);
        if (!$result || mysqli_num_rows($result) === 0) {
            $_SESSION["message"] = "Užsakymas nerastas.";
            $_SESSION["msg_type"] = "error";
            redirect("admin.php");
        }

        $order = mysqli_fetch_assoc($result);

        // Absoliutus kelias į video failą
        $videoPath = realpath($order['video_path']);
        if (!$videoPath || !file_exists($videoPath)) {
            $_SESSION["message"] = "❌ Video failas nerastas: " . htmlspecialchars($order['video_path']);
            $_SESSION["msg_type"] = "error";
            redirect("admin.php");
        }

        $outputDir = 'frames/order_' . $order_id;

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        $outputPattern = $outputDir . DIRECTORY_SEPARATOR . "frame_%04d.jpg";
        $command = "ffmpeg -i " . escapeshellarg($videoPath) . " -vf fps=4 \"$outputPattern\"";

        exec($command . " 2>&1", $output, $return_code);


        if ($return_code === 0) {
            $update = "UPDATE orders SET status = 'vykdoma' WHERE id = $order_id";
            mysqli_query($conn, $update);

            $_SESSION["message"] = "✅ Užsakymas #$order_id pradėtas. Nuotraukos sukurtos.";
            $_SESSION["msg_type"] = "success";
        } else {
            $error_output = implode("\n", $output);
            $_SESSION["message"] = "❌ ffmpeg klaida. Nepavyko apdoroti video:<br><pre>$error_output</pre>";
            $_SESSION["msg_type"] = "error";
        }


        redirect("admin.php");
    }
    if (isset($_POST["export_order"])) {
        $order_id = intval($_POST["order_id"]);
        $frameDir = "frames/order_$order_id";

        if (!is_dir($frameDir)) {
            $_SESSION["message"] = "❌ Kadrai nerasti. Pirmiausia paspauskite Start.";
            $_SESSION["msg_type"] = "error";
            redirect("admin.php");
        }

        $images = glob("$frameDir/*.jpg");
        if (!$images) {
            $_SESSION["message"] = "❌ Kadrai nerasti kataloge.";
            $_SESSION["msg_type"] = "error";
            redirect("admin.php");
        }

        // Pradėk PDF generavimą
        include('lib/fpdf.php'); // Užtikrink, kad biblioteka įkelta

        $pdf = new FPDF();
        foreach ($images as $img) {
            $pdf->AddPage();
            $pdf->Image($img, 10, 10, 190); // 190 plotis (tinka A4 formatui)
        }

        $pdfName = "exported_pdf/order_$order_id.pdf";
        if (!is_dir("exported_pdf")) {
            mkdir("exported_pdf", 0777, true);
        }

        $pdf->Output("F", $pdfName);

        // ✅ Atnaujink užsakymo statusą į "Gamyba"
        $stmt = $conn->prepare("UPDATE orders SET status = 'Gamyba' WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $stmt->close();

        $_SESSION["message"] = "✅ PDF sukurtas: <a href='$pdfName' target='_blank'>Parsisiųsti</a>";
        $_SESSION["msg_type"] = "success";
        redirect("admin.php");
    }

}


?>

<h1>All Orders</h1>
<div class="container">
    <?php if(isset($_SESSION["message"])): ?>
        <div class="notification <?php echo $_SESSION['msg_type'] ?>">
            <?php echo $_SESSION['message'];
            unset($_SESSION['message']);
            unset($_SESSION['msg_type']);
            ?>
        </div>
    <?php endif;?>

    <table class="user-table">
        <thead>
        <tr>
            <th>Order ID</th>
            <th>Username</th>
            <th>Title</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while($order = mysqli_fetch_assoc($order_result)) : ?>
            <tr>
                <td><?php echo $order['id']; ?></td>
                <td><?php echo htmlspecialchars($order['username']); ?></td>
                <td><?php echo htmlspecialchars($order['title']); ?></td>
                <td><?php echo ucfirst($order['status']); ?></td>
                <td><?php echo full_month_date($order['created_at']); ?></td>
                <td>
                    <!-- Edit status form -->
                    <form method="POST" style="display:inline-block;">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <select name="status" required>
                            <option value="laukiama" <?php if($order['status'] == 'laukiama') echo 'selected'; ?>>laukiama</option>
                            <option value="vykdoma" <?php if($order['status'] == 'vykdoma') echo 'selected'; ?>>vykdoma</option>
                            <option value="Gamyba" <?php if($order['status'] == 'Gamyba') echo 'selected'; ?>>Gamyba</option>
                            <option value="užbaigta" <?php if($order['status'] == 'užbaigta') echo 'selected'; ?>>užbaigta</option>
                        </select>
                        <button class="edit" type="submit" name="edit_order">Update</button>
                    </form>

                    <form method="POST" style="display:inline-block;">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <button class="start" type="submit" name="start_order">Start</button>
                    </form>

                    <form method="POST" style="display:inline-block;">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <button class="export" type="submit" name="export_order">Export</button>
                    </form>

                    <!-- Delete form -->
                    <form method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this order?');">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <button class="delete" type="submit" name="delete_order">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>


<?php include "partials/footer.php";
mysqli_close($conn);
?>
