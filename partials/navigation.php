<nav>
    <ul>
        <li><a class="<?php echo setActiveClass('index.php');?>" href="index.php">Home</a></li>

        <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li><a class="<?php echo setActiveClass('admin.php');?>" href="admin.php">Admin</a></li>
            <?php elseif($_SESSION['role'] === 'user'): ?>
                <li><a class="<?php echo setActiveClass('upload.php');?>" href="upload.php">Užsisakyti</a></li>
            <?php endif; ?>

        <?php else: ?>
            <li><a class="<?php echo setActiveClass('register.php');?>" href="register.php">Register</a></li>
            <li><a class="<?php echo setActiveClass('login.php');?>" href="login.php">Login</a></li>
        <?php endif; ?>
        <li><a class="<?php echo setActiveClass('contact.php');?>" href="contact.php">Contacts</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li class="dropdown">
                <a href="#" class="dropbtn" onclick="toggleDropdown(event)">Profilis ▾</a>
                <div id="dropdownMenu" class="dropdown-content">
                    <a href="account.php">Paskyra</a>
                    <a href="dashboard.php">Mano užsakymai</a>
                    <a href="logout.php">Atsijungti</a>
                </div>
            </li>
        <?php endif; ?>
    </ul>
</nav>
<script>
    function toggleDropdown(event) {
        event.preventDefault(); // neperkrauna puslapio
        document.getElementById("dropdownMenu").classList.toggle("show");
    }

    // Paspaudus kitur, uždaryti dropdown
    window.onclick = function(event) {
        if (!event.target.matches('.dropbtn')) {
            const dropdowns = document.getElementsByClassName("dropdown-content");
            for (const d of dropdowns) {
                if (d.classList.contains('show')) {
                    d.classList.remove('show');
                }
            }
        }
    }
</script>