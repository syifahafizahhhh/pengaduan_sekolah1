<?php
require __DIR__.'/config/koneksi.php'; require __DIR__.'/includes/layout.php';
if (!empty($_SESSION['u'])) { header('Location: '.$base.'/'.$_SESSION['u']['role'].'/dashboard.php'); exit; }
$err = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $un = trim($_POST['username'] ?? ''); $pw = $_POST['password'] ?? '';
  if ($un === '' || $pw === '') $err = 'Username dan password wajib diisi.';
  else {
    $q = $pdo->prepare('SELECT * FROM users WHERE username=?'); $q->execute([$un]); $u = $q->fetch();
    if ($u && password_verify($pw, $u['password'])) {
      session_regenerate_id(true); $_SESSION['u'] = ['id'=>$u['id'],'nama'=>$u['nama'],'role'=>$u['role']];
      header('Location: '.$base.'/'.$u['role'].'/dashboard.php'); exit;
    }
    $err = 'Username atau password salah.';
  }
}
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Masuk | MIMIN</title><link rel="stylesheet" href="<?= $base ?>/assets/style.css"></head>
<body class="login"><section class="hero"><div class="logo">MIMIN</div><h1>Sarana sekolah yang rusak, dilaporkan hari ini, ditangani esok hari.</h1><p>Kirim pengaduan, pantau statusnya, dan baca umpan balik dari admin dalam satu tempat.</p></section>
<section class="box"><form method="post"><h2>Masuk ke akun</h2><?php if ($err): ?><div class="flash err"><?= e($err) ?></div><?php endif; ?>
<label>Username<input name="username" value="<?= e($_POST['username'] ?? '') ?>" autofocus required></label>
<label>Password<input type="password" name="password" required></label><button class="btn">Masuk</button>
<p class="hint">Akun contoh: admin / admin123 &nbsp;|&nbsp; murid1 / murid123</p></form></section></body></html>
