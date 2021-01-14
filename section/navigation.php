<?php 
    if(isset($_SESSION['id'])) { 
        require_once('admin_nav.php');
        require_once('admin_sidebar.php');
    } else {
?>

<nav>
    <div class="index-wrapper">
        <div class="navigation d-flex jc-sb ai-c">
            <div class="logo">
                <a href="<?php base(); ?>index">Logo</a>
            </div>
            <ul class="navigation__menu">
                <li><a href="<?php base(); ?>index" class="navigation__link">Početna</a></li>            
                <li><a href="<?php base(); ?>login" class="navigation__link">Prijava</a></li>
                <li><a href="<?php base(); ?>register" class="navigation__link">Napravi nalog</a></li>
            </ul>
        </div>
    </div>
</nav>

<?php } ?>