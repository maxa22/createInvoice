<?php
    session_start();
    if(isset($_GET['page'])) {
        if($_GET['page'] == 'render_pdf') {
            require 'pages/render_pdf.php';
            exit();
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once('section/head.php'); ?>
<body>
    <!-- Navigation, same for every page -->
    <?php require_once('section/navigation.php'); ?>
    <!-- MAIN SECTION OF PAGE -->
    <?php require 'section/main.php'; ?>

    <?php 
    if(!isset($_GET['page'])) {
        require('pages/index.php');
    } 
    ?>

<?php if(isset($_SESSION['id'])) { ?>
    <script src="<?php base(); ?>javascript/sidebar_toggle.js"></script>
<?php } ?>
</body>
</html>
