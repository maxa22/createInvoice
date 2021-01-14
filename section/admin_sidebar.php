<aside class="sidebar">
    <div class="sidebar__user">
        <img src="<?php base(); ?>images/avatar.png" alt="">
        <h4><?php echo $_SESSION['name']; ?></h4>
    </div>
    <ul class="mt-s">
        <li>
            <a href="<?php base(); ?>firms" class="sidebar__menu-link d-flex ai-c">
                <span class="d-iblock">
                    <span class="sidebar__menu-icon">
                        <i class="fas fa-tachometer-alt"></i>
                    </span>
                </span>
                <span  class="sidebar__menu-text">Firme</span>
            </a>
        </li>
        <li>
            <a href="<?php base(); ?>add_firm" class="sidebar__menu-link d-flex ai-c">
                <span class="d-iblock">
                    <span class="sidebar__menu-icon">
                        <i class="fas fa-plus-circle"></i>
                    </span>
                </span>
                <span class="sidebar__menu-text">Dodajte firmu</span>
            </a>
        </li>
        <li>
            <a href="<?php base(); ?>clients" class="sidebar__menu-link d-flex ai-c">
                <span class="d-iblock">
                    <span class="sidebar__menu-icon">
                        <i class="fas fa-users"></i>
                    </span>
                </span>
                <span class="sidebar__menu-text">Klijenti</span>
            </a>
        </li>
        <li>
            <a href="<?php base(); ?>add_client" class="sidebar__menu-link d-flex ai-c">
                <span class="d-iblock">
                    <span class="sidebar__menu-icon">
                        <i class="fas fa-user-plus"></i>
                    </span>
                </span>
                <span class="sidebar__menu-text">Dodaj klijenta</span>
            </a>
        </li>
        <li>
            <a href="<?php base(); ?>bills" class="sidebar__menu-link d-flex ai-c">
                <span class="d-iblock">
                    <span class="sidebar__menu-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </span>
                </span>
                <span class="sidebar__menu-text">Fiskalni računi</span>
            </a>
        </li>
        <li>
            <a href="<?php base(); ?>add_bill" class="sidebar__menu-link d-flex ai-c">
                <span class="d-iblock">
                    <span class="sidebar__menu-icon">
                        <i class="fas fa-plus"></i>
                    </span>
                </span>
                <span class="sidebar__menu-text">Dodaj fiskalni račun</span>
            </a>
        </li>
        <li>
            <a href="<?php base(); ?>invoices" class="sidebar__menu-link d-flex ai-c">
                <span class="d-iblock">
                    <span class="sidebar__menu-icon">
                        <i class="fas fa-file-invoice"></i>
                    </span>
                </span>
                <span class="sidebar__menu-text">Fakture</span>
            </a>
        </li>
        <li>
            <a href="<?php base(); ?>add_invoice" class="sidebar__menu-link d-flex ai-c">
                <span class="d-iblock">
                    <span class="sidebar__menu-icon">
                        <i class="fas fa-plus"></i>
                    </span>
                </span>
                <span class="sidebar__menu-text">Kreiraj fakturu</span>
            </a>
        </li>
    </ul>
</aside>
<div class="sidebar-overlay"></div>