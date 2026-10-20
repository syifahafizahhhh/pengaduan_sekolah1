<?php
require __DIR__.'/../config/koneksi.php'; require __DIR__.'/../includes/layout.php'; need('siswa');
$err = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $j = trim($_POST['judul'] ?? ''); $k = (int)($_POST['kategori'] ?? 0); $lo = trim($_POST['lokasi'] ?? ''); $d = trim($_POST['deskripsi'] ?? ''); $foto = null;
  if ($j === '' || !$k || $lo === '' || $d === '') $err = 'Judul, kategori, lokasi, dan deskripsi wajib diisi.';
  elseif (!empty($_FILES['foto']['name'])) {
    $f = $_FILES['foto']; $ext = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][@mime_content_type($f['tmp_name'])] ?? null;
    if ($f['error'] || !$ext) $err = 'Foto harus berformat JPG, PNG, atau WEBP.';
    elseif ($f['size'] > 2*1024*1024) $err = 'Ukuran foto maksimal 2 MB.';
    else { $foto = bin2hex(random_bytes(8)).'.'.$ext; move_uploaded_file($f['tmp_name'], __DIR__.'/../uploads/'.$foto); }
  }
  if (!$err) {
    $pdo->prepare('INSERT INTO pengaduan(user_id,kategori_id,judul,lokasi,deskripsi,foto) VALUES(?,?,?,?,?,?)')->execute([$_SESSION['u']['id'],$k,$j,$lo,$d,$foto]);
    $pdo->prepare('INSERT INTO riwayat_status(pengaduan_id,status,catatan) VALUES(?,?,?)')->execute([$pdo->lastInsertId(),'Menunggu','Pengaduan dikirim.']);
    flash('Pengaduan berhasil dikirim.'); header('Location: riwayat.php'); exit;
  }
}
head('Buat Pengaduan'); if ($err) echo '<div class="flash err">'.e($err).'</div>';
echo '<form class="card form" method="post" enctype="multipart/form-data"><label>Judul pengaduan<input name="judul" value="'.e($_POST['judul'] ?? '').'" required></label><label>Kategori sarana<select name="kategori" required><option value="">Pilih kategori</option>';
foreach ($pdo->query('SELECT * FROM kategori') as $k) echo '<option value="'.$k['id'].'"'.(($_POST['kategori'] ?? '') == $k['id'] ? ' selected' : '').'>'.e($k['nama']).'</option>';
echo '</select></label><label>Lokasi<input name="lokasi" value="'.e($_POST['lokasi'] ?? '').'" required></label><label>Deskripsi masalah<textarea name="deskripsi" rows="5" required>'.e($_POST['deskripsi'] ?? '').'</textarea></label><label>Foto bukti (opsional, maks 2 MB)<input type="file" name="foto" accept="image/*"></label><button class="btn">Kirim pengaduan</button></form>'; foot();
