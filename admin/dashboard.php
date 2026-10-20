<?php
require __DIR__.'/../config/koneksi.php'; require __DIR__.'/../includes/layout.php'; need('admin');
$w = []; $a = [];
if ($q = trim($_GET['q'] ?? '')) { $w[] = '(p.judul LIKE ? OR p.lokasi LIKE ?)'; $a[] = "%$q%"; $a[] = "%$q%"; }
foreach (['status'=>'p.status','kategori'=>'p.kategori_id','murid'=>'p.user_id'] as $k => $c) if (($_GET[$k] ?? '') !== '') { $w[] = "$c=?"; $a[] = $_GET[$k]; }
if (!empty($_GET['tanggal'])) { $w[] = 'DATE(p.tanggal)=?'; $a[] = $_GET['tanggal']; }
if (!empty($_GET['bulan'])) { $w[] = "DATE_FORMAT(p.tanggal,'%Y-%m')=?"; $a[] = $_GET['bulan']; }
$s = $pdo->prepare('SELECT p.*,u.nama murid,k.nama kategori FROM pengaduan p JOIN users u ON u.id=p.user_id JOIN kategori k ON k.id=p.kategori_id'.($w ? ' WHERE '.implode(' AND ', $w) : '').' ORDER BY p.tanggal DESC'); $s->execute($a);
$c = $pdo->query('SELECT status,COUNT(*) FROM pengaduan GROUP BY status')->fetchAll(PDO::FETCH_KEY_PAIR);
function opts($rows, $sel){ $o = '<option value="">Semua</option>'; foreach ($rows as $id => $n) $o .= '<option value="'.e($id).'"'.($sel !== '' && $sel == $id ? ' selected' : '').'>'.e($n).'</option>'; return $o; }
head('Semua Pengaduan');
echo '<div class="stats">'; foreach (STATUS as $x) echo '<div class="stat"><b>'.($c[$x] ?? 0).'</b>'.$x.'</div>'; echo '</div>';
echo '<form class="card filter" method="get"><label>Cari<input name="q" value="'.e($q).'" placeholder="Judul atau lokasi"></label><label>Status<select name="status">'.opts(array_combine(STATUS, STATUS), $_GET['status'] ?? '').'</select></label><label>Kategori<select name="kategori">'.opts($pdo->query('SELECT id,nama FROM kategori')->fetchAll(PDO::FETCH_KEY_PAIR), $_GET['kategori'] ?? '').'</select></label><label>Murid<select name="murid">'.opts($pdo->query("SELECT id,nama FROM users WHERE role='siswa'")->fetchAll(PDO::FETCH_KEY_PAIR), $_GET['murid'] ?? '').'</select></label><label>Tanggal<input type="date" name="tanggal" value="'.e($_GET['tanggal'] ?? '').'"></label><label>Bulan<input type="month" name="bulan" value="'.e($_GET['bulan'] ?? '').'"></label><button class="btn">Terapkan</button></form>';
echo '<div class="card"><table><tr><th>Tanggal</th><th>Judul</th><th>Pelapor</th><th>Kategori</th><th>Status</th><th></th></tr>'; $n = 0;
foreach ($s as $r) { $n++; echo '<tr><td>'.date('d M Y', strtotime($r['tanggal'])).'</td><td>'.e($r['judul']).'</td><td>'.e($r['murid']).'</td><td>'.e($r['kategori']).'</td><td>'.badge($r['status']).'</td><td><a href="detail.php?id='.$r['id'].'">Kelola</a></td></tr>'; }
echo '</table>'; if (!$n) echo '<p class="empty">Tidak ada pengaduan yang cocok dengan filter.</p>'; echo '</div>'; foot();
