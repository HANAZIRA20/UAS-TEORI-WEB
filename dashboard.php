<?php

// Set zona waktu agar sinkron dengan MySQL
date_default_timezone_set('Asia/Makassar');

session_start();
require_once 'config/db.php';

// Auto logout 60 detik
$timeout = 60;
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > $timeout) {
        session_unset();
        session_destroy();
        header("Location: index.php?timeout=1");
        exit();
    }
}
$_SESSION['last_activity'] = time();

$active = 'dashboard';

// =========== DATA RINGKAS ==============
$today = date('Y-m-d');

// Total transaksi hari ini
$q_trans = "SELECT COUNT(*) AS total_trans 
            FROM transaksi 
            WHERE DATE(tanggal) = '$today'";
$res_trans = $conn->query($q_trans)->fetch_assoc();

// Pendapatan hari ini
$q_pend = "SELECT SUM(total) AS pendapatan 
           FROM transaksi 
           WHERE DATE(tanggal) = '$today'";
$res_pend = $conn->query($q_pend)->fetch_assoc();

// Total obat
$q_obat = "SELECT COUNT(*) AS total_obat FROM obat";
$res_obat = $conn->query($q_obat)->fetch_assoc();

// =========== DATA GRAFIK 7 HARI ==========
$gq = "SELECT DATE(tanggal) AS tgl, SUM(total) AS total_harian
       FROM transaksi
       GROUP BY DATE(tanggal)
       ORDER BY DATE(tanggal) ASC
       LIMIT 7";
$gres = $conn->query($gq);

$label = [];
$nilai = [];

while ($r = $gres->fetch_assoc()) {
    $label[] = $r['tgl'];
    $nilai[] = $r['total_harian'];
}

// =========== STOK MINIMAL ==========
$stok_q = "SELECT * FROM obat WHERE stok <= 10 ORDER BY stok ASC";
$stok_res = $conn->query($stok_q);


// =========================================
// ============     TAMPILAN     ===========
// =========================================

include 'header.php';
?>

<div class="d-flex">

    <?php include 'sidebar.php'; ?>

    <div class="content p-4 w-100">

        <h3 class="mb-4 fw-bold text-primary">
            Selamat Datang, <?php echo ucfirst($_SESSION['username']); ?> 👋
        </h3>

        <!-- =================== KARTU RINGKAS ==================== -->
        <div class="row g-4 mb-4">

            <div class="col-md-4">
                <div class="card-custom">
                    <h5 class="text-muted">Total Transaksi Hari Ini</h5>
                    <h2 class="fw-bold text-primary mt-2">
                        <?php echo $res_trans['total_trans']; ?>
                    </h2>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-custom">
                    <h5 class="text-muted">Pendapatan Hari Ini</h5>
                    <h2 class="fw-bold text-success mt-2">
                        Rp <?php echo number_format($res_pend['pendapatan'] ?: 0); ?>
                    </h2>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-custom">
                    <h5 class="text-muted">Jumlah Obat Tersedia</h5>
                    <h2 class="fw-bold text-info mt-2">
                        <?php echo $res_obat['total_obat']; ?>
                    </h2>
                </div>
            </div>

        </div>

        <!-- =================== STOK MINIM ==================== -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header text-white" style="background:#EF4444;">
                <strong>⚠ Stok Hampir Habis</strong>
            </div>

            <div class="card-body">

                <?php if ($stok_res->num_rows > 0): ?>
                <ul class="list-group">

                    <?php while ($s = $stok_res->fetch_assoc()): ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <?php echo $s['nama']; ?>
                        <span class="badge bg-danger rounded-pill">
                            Stok: <?php echo $s['stok']; ?>
                        </span>
                    </li>
                    <?php endwhile; ?>

                </ul>

                <?php else: ?>
                    <p class="text-success fw-semibold">Semua stok aman ✔</p>
                <?php endif; ?>

            </div>
        </div>

        <!-- =================== GRAFIK ==================== -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header text-white" style="background:#6366F1;">
                <strong>📊 Grafik Penjualan 7 Hari Terakhir</strong>
            </div>
            <div class="card-body">
                <canvas id="grafikPenjualan" height="120"></canvas>
            </div>
        </div>

    </div>
</div>

<?php include 'footer.php'; ?>

<!-- =================== SCRIPT CHART ==================== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('grafikPenjualan');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($label); ?>,
        datasets: [{
            label: 'Total Penjualan (Rp)',
            data: <?php echo json_encode($nilai); ?>,
            borderWidth: 3,
            borderColor: '#4F46E5',
            backgroundColor: 'rgba(79, 70, 229, 0.25)',
            tension: 0.35,
            pointRadius: 5,
            pointBackgroundColor: '#4F46E5',
        }]
    },
    options: {
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
