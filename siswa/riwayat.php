<?php
require __DIR__.'/../config/koneksi.php'; require __DIR__.'/../includes/layout.php'; need('siswa');
$l = $pdo->prepare('SELECT p.*,k.nama kategori FROM pengaduan p JOIN kategori k ON k.id=p.kategori_id WHERE user_id=? ORDER BY tanggal DESC'); $l->execute([$_SESSION['u']['id']]);
head('Riwayat Pengaduan'); echo '<div class="card"><table><tr><th>Judul</th><th>Kategori</th><th>Tanggal</th><th>Status</th><th></th></tr>'; $n = 0;
foreach ($l as $r) { $n++; echo '<tr><td>'.e($r['judul']).'</td><td>'.e($r['kategori']).'</td><td>'.date('d M Y', strtotime($r['tanggal'])).'</td><td>'.badge($r['status']).'</td><td><a href="detail.php?id='.$r['id'].'">Detail</a></td></tr>'; }
echo '</table>'; if (!$n) echo '<p class="empty">Belum ada pengaduan.</p>'; echo '</div>'; foot();
