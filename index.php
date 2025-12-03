<?php
// index.php
session_start();
require_once 'config/db.php';

// jika sudah login -> redirect
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}

// proses login
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $remember = isset($_POST['remember']);

    if ($username === '' || $password === '') {
        $msg = "Username & password wajib diisi.";
    } else {
        $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows === 1) {
            $user = $res->fetch_assoc();
            // password stored as MD5 in demo SQL
            if (md5($password) === $user['password']) {
                // berhasil
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['last_activity'] = time();

                if ($remember) {
                    setcookie('apotek_user', $user['username'], time() + (86400 * 30), "/");
                } else {
                    setcookie('apotek_user', '', time() - 3600, "/");
                }

                header("Location: dashboard.php");
                exit();
            } else {
                $msg = "Username atau password salah.";
            }
        } else {
            $msg = "Username atau password salah.";
        }
    }
}
include 'header.php';
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body">
                    <h4 class="card-title mb-3 text-center">Login ApotekKU</h4>

                    <?php if (isset($_GET['timeout'])): ?>
                        <div class="alert alert-warning">Session habis. Silakan login ulang.</div>
                    <?php endif; ?>

                    <?php if ($msg): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($msg); ?></div>
                    <?php endif; ?>

                    <form method="post" action="">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input name="username" type="text" class="form-control"
                                   value="<?php echo htmlspecialchars($_COOKIE['apotek_user'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input name="password" type="password" class="form-control">
                        </div>
                        <div class="mb-3 form-check">
                            <input name="remember" type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-success">Login</button>
                        </div>
                    </form>

                    <hr>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
