<?php
require __DIR__.'/../config/koneksi.php'; require __DIR__.'/../includes/layout.php'; need('siswa');
$uid = $_SESSION['u']['id'];
$q = $pdo->prepare('SELECT status,COUNT(*) FROM pengaduan WHERE user_id=? GROUP BY status'); $q->execute([$uid]); $c = $q->fetchAll(PDO::FETCH_KEY_PAIR);
$l = $pdo->prepare('SELECT p.*,k.nama kategori FROM pengaduan p JOIN kategori k ON k.id=p.kategori_id WHERE user_id=? ORDER BY tanggal DESC LIMIT 5'); $l->execute([$uid]);
head('Halo, '.explode(' ', $_SESSION['u']['nama'])[0]);
echo '<div class="stats">'; foreach (STATUS as $s) echo '<div class="stat"><b>'.($c[$s] ?? 0).'</b>'.$s.'</div>'; echo '</div>';
echo '<div class="card"><h2>Pengaduan terbaru</h2><table><tr><th>Judul</th><th>Kategori</th><th>Status</th><th></th></tr>'; $n = 0;
foreach ($l as $r) { $n++; echo '<tr><td>'.e($r['judul']).'</td><td>'.e($r['kategori']).'</td><td>'.badge($r['status']).'</td><td><a href="detail.php?id='.$r['id'].'">Lihat</a></td></tr>'; }
echo '</table>'; if (!$n) echo '<p class="empty">Belum ada pengaduan. <a href="buat.php">Buat pengaduan pertama</a>.</p>'; echo '</div>'; foot();
