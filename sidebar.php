<?php
?>
<style>

.sidebar {
    width: 240px;
    min-height: 100vh;
    background: linear-gradient(180deg, #4F46E5, #6D28D9);
    color: white !important;
    position: fixed;
    left: 0;
    top: 0;
    padding-top: 15px;
    box-shadow: 2px 0 10px rgba(0,0,0,0.25);
}

/* brand */
.sidebar-brand h5 {
    font-weight: 700;
    margin-top: 8px;
}

/* nav */
.sidebar .nav-link {
    border-radius: 8px;
    padding: 10px 15px;
    transition: 0.25s;
    font-weight: 500;
}

/* hover */
.sidebar .nav-link:hover {
    background: rgba(255,255,255,0.15);
    color: #fff !important;
    transform: translateX(4px);
}

/* active menu */
.sidebar .nav-link.active {
    background: #ffffff;
    color: #4F46E5 !important;
    font-weight: 700;
}

/* icon warna */
.sidebar .bi {
    font-size: 1.1rem;
}
</style>

<div class="sidebar text-white">

    <div class="sidebar-brand text-center py-3">
        <img src="assets/img/logo.png" alt="logo" width="60" class="d-block mx-auto mb-2">
        <h5>ApotekKU</h5>
    </div>

    <ul class="nav flex-column p-3">

        <li class="nav-item mb-2">
            <a class="nav-link <?php echo ($active=='dashboard') ? 'active' : 'text-white'; ?>" 
               href="dashboard.php">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>

        <li class="nav-item mb-2">
            <a class="nav-link <?php echo ($active=='obat') ? 'active' : 'text-white'; ?>" 
               href="obat.php">
                <i class="bi bi-box-seam me-2"></i> Data Obat
            </a>
        </li>

        <li class="nav-item mb-2">
            <a class="nav-link <?php echo ($active=='transaksi') ? 'active' : 'text-white'; ?>" 
               href="transaksi.php">
                <i class="bi bi-receipt me-2"></i> Transaksi
            </a>
        </li>

        <li class="nav-item mb-2">
            <a class="nav-link <?php echo ($active=='riwayat') ? 'active' : 'text-white'; ?>" 
               href="riwayat.php">
                <i class="bi bi-clock-history me-2"></i> Riwayat Penjualan
            </a>
        </li>

        <li class="nav-item mt-3">
            <a class="nav-link text-white" href="logout.php">
                <i class="bi bi-box-arrow-right me-2"></i> Keluar
            </a>
        </li>

    </ul>
</div>
