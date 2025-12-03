<?php
// riwayat.php
session_start();
require_once 'config/db.php';

$timeout = 60;

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Auto logout
if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > $timeout) {
        session_unset();
        session_destroy();
        header("Location: index.php?timeout=1");
        exit();
    }
}
$_SESSION['last_activity'] = time();

// Sidebar highlight
$active = 'riwayat';

// Ambil data transaksi
$q = "SELECT t.*, o.nama AS nama_obat 
      FROM transaksi t
      LEFT JOIN obat o ON t.id_obat = o.id
      ORDER BY t.tanggal DESC";
$res = $conn->query($q);

include 'header.php';
?>

<div class="d-flex">
    <?php include 'sidebar.php'; ?>

    <div class="content p-4 w-100">
        <div class="container-fluid">

            <h3 class="mb-4 fw-bold text-dark">
                <i class="bi bi-clock-history me-2"></i> Riwayat Penjualan
            </h3>

            <div class="card shadow-sm border-0">
                <div class="card-body table-responsive">

                    <!-- TABEL MODERN -->
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Obat</th>
                                <th>Qty</th>
                                <th>Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php 
                            $i = 1; 
                            while ($row = $res->fetch_assoc()): 
                            ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $row['tanggal']; ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_obat'] ?: '—'); ?></td>
                                    <td><?php echo (int)$row['qty']; ?></td>
                                    <td>Rp <?php echo number_format($row['total'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endwhile; ?>

                            <?php if ($i === 1): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-3">
                                        Belum ada transaksi.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
