<?php
// transaksi.php
session_start();
require_once 'config/db.php';

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

$active = 'transaksi';
$msg = '';

// ambil daftar obat
$obats = $conn->query("SELECT * FROM obat WHERE stok > 0 ORDER BY nama ASC");

// proses transaksi submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_obat = (int) $_POST['id_obat'];
    $qty = (int) $_POST['qty'];
    $kasir = $_SESSION['username'];

    if ($id_obat <= 0 || $qty <= 0) {
        $msg = "Pilih obat dan qty valid.";
    } else {
        // ambil harga & stok
        $st = $conn->prepare("SELECT harga, stok FROM obat WHERE id = ?");
        $st->bind_param('i', $id_obat);
        $st->execute();
        $res = $st->get_result()->fetch_assoc();
        if (!$res) {
            $msg = "Obat tidak ditemukan.";
        } elseif ($res['stok'] < $qty) {
            $msg = "Stok tidak cukup.";
        } else {
            $total = $res['harga'] * $qty;
            $ins = $conn->prepare("INSERT INTO transaksi (id_obat, qty, total, kasir) VALUES (?,?,?,?)");
            $ins->bind_param('iiis', $id_obat, $qty, $total, $kasir);
            if ($ins->execute()) {
                // kurangi stok
                $upd = $conn->prepare("UPDATE obat SET stok = stok - ? WHERE id = ?");
                $upd->bind_param('ii', $qty, $id_obat);
                $upd->execute();
                $msg = "Transaksi berhasil. Total: Rp " . number_format($total);
            } else {
                $msg = "Gagal menyimpan transaksi.";
            }
        }
    }
}

include 'header.php';
?>
<div class="d-flex">
    <?php include 'sidebar.php'; ?>
    <div class="content p-4 w-100">
        <div class="container-fluid">
            <h3>Transaksi Penjualan</h3>

            <?php if ($msg): ?>
                <div class="alert alert-info"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="post" id="formTransaksi">
                        <div class="row g-2">
                            <div class="col-md-5">
                                <label>Obat</label>
                                <select id="id_obat" name="id_obat" class="form-select">
                                    <option value="">-- Pilih Obat --</option>
                                    <?php while ($o = $obats->fetch_assoc()): ?>
                                        <option value="<?php echo $o['id']; ?>"
                                                data-harga="<?php echo $o['harga']; ?>"
                                                data-stok="<?php echo $o['stok']; ?>">
                                            <?php echo htmlspecialchars($o['nama']) . " (Rp ".number_format($o['harga']).") - Stok: ".$o['stok']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Qty</label>
                                <input id="qty" name="qty" type="number" class="form-control" value="1" min="1">
                            </div>
                            <div class="col-md-3">
                                <label>Total</label>
                                <input id="total" type="text" class="form-control" readonly value="Rp 0">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button class="btn btn-success w-100">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <p class="text-muted small">Catatan: setiap transaksi mengurangi stok obat.</p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
