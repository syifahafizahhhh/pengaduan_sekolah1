<?php
require __DIR__.'/../config/koneksi.php'; require __DIR__.'/../includes/layout.php'; need('admin');
$id = (int)($_GET['id'] ?? 0);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $st = $_POST['status'] ?? ''; $cat = trim($_POST['catatan'] ?? '');
  $ok = $pdo->prepare('SELECT COUNT(*) FROM pengaduan WHERE id=?'); $ok->execute([$id]);
  if (in_array($st, STATUS, true) && $ok->fetchColumn()) {
    $pdo->prepare('UPDATE pengaduan SET status=? WHERE id=?')->execute([$st, $id]);
    $pdo->prepare('INSERT INTO riwayat_status(pengaduan_id,admin_id,status,catatan) VALUES(?,?,?,?)')->execute([$id, $_SESSION['u']['id'], $st, $cat ?: 'Status diperbarui.']);
    flash('Status dan umpan balik berhasil disimpan.');
  } else flash('Gagal menyimpan: status tidak valid.');
  header("Location: detail.php?id=$id"); exit;
}
head('Kelola Pengaduan'); $p = detail_view($pdo, $id);
if ($p) { echo '<form class="card form" method="post"><h2>Perbarui status</h2><label>Status<select name="status">'; foreach (STATUS as $s) echo '<option'.($s == $p['status'] ? ' selected' : '').'>'.$s.'</option>';
  echo '</select></label><label>Umpan balik untuk siswa<textarea name="catatan" rows="3"></textarea></label><button class="btn">Simpan perubahan</button></form>'; }
echo '<a href="dashboard.php">&larr; Kembali</a>'; foot();
