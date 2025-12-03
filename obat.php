<?php
// obat.php
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

$active = 'obat';
$msg = '';

// proses tambah
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {
    $kode = trim($_POST['kode']);
    $nama = trim($_POST['nama']);
    $kategori = trim($_POST['kategori']);
    $harga = (int) $_POST['harga'];
    $stok = (int) $_POST['stok'];
    $expired = $_POST['expired'] ?: null;

    if ($kode === '' || $nama === '') {
        $msg = "Kode & nama wajib diisi.";
    } else {
        $sql = "INSERT INTO obat (kode,nama,kategori,harga,stok,expired) VALUES (?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssiss', $kode, $nama, $kategori, $harga, $stok, $expired);
        $stmt->execute();
        $msg = "Obat berhasil ditambahkan.";
    }
}

// proses edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
    $id = (int) $_POST['id'];
    $kode = trim($_POST['kode']);
    $nama = trim($_POST['nama']);
    $kategori = trim($_POST['kategori']);
    $harga = (int) $_POST['harga'];
    $stok = (int) $_POST['stok'];
    $expired = $_POST['expired'] ?: null;

    $sql = "UPDATE obat SET kode=?, nama=?, kategori=?, harga=?, stok=?, expired=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssissi', $kode, $nama, $kategori, $harga, $stok, $expired, $id);
    $stmt->execute();
    $msg = "Obat berhasil diupdate.";
}

// proses delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM obat WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    header("Location: obat.php");
    exit();
}

// ambil data obat
$result = $conn->query("SELECT * FROM obat ORDER BY nama ASC");

include 'header.php';
?>
<div class="d-flex">
    <?php include 'sidebar.php'; ?>

    <div class="content p-4 w-100">
        <div class="container-fluid">

            <h3 class="mb-3 fw-bold">Data Obat</h3>

            <?php if ($msg): ?>
                <div class="alert alert-info"><?= htmlspecialchars($msg); ?></div>
            <?php endif; ?>

            <!-- Form -->
            <?php if (isset($_GET['edit'])):
                $eid = (int) $_GET['edit'];
                $er = $conn->prepare("SELECT * FROM obat WHERE id=?");
                $er->bind_param('i', $eid);
                $er->execute();
                $edf = $er->get_result()->fetch_assoc();
            ?>
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <h5>Edit Obat</h5>
                        <form method="post">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?= $edf['id']; ?>">

                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <input name="kode" class="form-control" value="<?= htmlspecialchars($edf['kode']); ?>">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <input name="nama" class="form-control" value="<?= htmlspecialchars($edf['nama']); ?>">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <input name="kategori" class="form-control" value="<?= htmlspecialchars($edf['kategori']); ?>">
                                </div>
                                <div class="col-md-1 mb-2">
                                    <input name="harga" type="number" class="form-control" value="<?= $edf['harga']; ?>">
                                </div>
                                <div class="col-md-1 mb-2">
                                    <input name="stok" type="number" class="form-control" value="<?= $edf['stok']; ?>">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <input name="expired" type="date" class="form-control" value="<?= $edf['expired']; ?>">
                                </div>
                            </div>

                            <button class="btn btn-primary">Update</button>
                            <a href="obat.php" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>

            <?php else: ?>

                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <h5>Tambah Obat</h5>
                        <form method="post">
                            <input type="hidden" name="action" value="add">
                            <div class="row">
                                <div class="col-md-2 mb-2"><input name="kode" class="form-control" placeholder="Kode"></div>
                                <div class="col-md-3 mb-2"><input name="nama" class="form-control" placeholder="Nama Obat"></div>
                                <div class="col-md-2 mb-2"><input name="kategori" class="form-control" placeholder="Kategori"></div>
                                <div class="col-md-1 mb-2"><input name="harga" type="number" class="form-control" placeholder="Harga"></div>
                                <div class="col-md-1 mb-2"><input name="stok" type="number" class="form-control" placeholder="Stok"></div>
                                <div class="col-md-2 mb-2"><input name="expired" type="date" class="form-control"></div>
                                <div class="col-md-1 mb-2"><button class="btn btn-success w-100">Tambah</button></div>
                            </div>
                        </form>
                    </div>
                </div>

            <?php endif; ?>

            <!-- TABEL MODERN -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table-modern">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Expired</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php $i = 1; while($row = $result->fetch_assoc()): 

                                    // Badges
                                    $stok = (int)$row['stok'];
                                    if ($stok > 20) $badge = "<span class='badge-stok badge-hijau'>$stok</span>";
                                    else if ($stok > 10) $badge = "<span class='badge-stok badge-kuning'>$stok</span>";
                                    else $badge = "<span class='badge-stok badge-merah'>$stok</span>";

                                    // Expired highlight
                                    $exp = $row['expired'];
                                    $expClass = '';
                                    if ($exp && strtotime($exp) < time()) {
                                        $expClass = "text-danger fw-bold";
                                    } elseif ($exp && strtotime($exp) < strtotime('+30 days')) {
                                        $expClass = "text-warning fw-bold";
                                    }
                                ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td><?= htmlspecialchars($row['kode']); ?></td>
                                    <td><?= htmlspecialchars($row['nama']); ?></td>
                                    <td><span class="badge bg-primary"><?= htmlspecialchars($row['kategori']); ?></span></td>
                                    <td>Rp <?= number_format($row['harga']); ?></td>
                                    <td><?= $badge; ?></td>
                                    <td class="<?= $expClass; ?>"><?= $row['expired']; ?></td>
                                    <td>
                                        <a href="obat.php?edit=<?= $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                        <a href="obat.php?delete=<?= $row['id']; ?>" onclick="return confirm('Hapus obat ini?')" class="btn btn-sm btn-danger">Hapus</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>

                                <?php if ($i === 1): ?>
                                    <tr><td colspan="8" class="text-center">Belum ada data obat.</td></tr>
                                <?php endif; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>