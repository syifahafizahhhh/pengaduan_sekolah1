<?php
const STATUS = ['Menunggu','Diproses','Selesai','Ditolak'];
function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function need($role){ global $base; if (empty($_SESSION['u']) || $_SESSION['u']['role'] !== $role) { header("Location: $base/login.php"); exit; } }
function badge($s){ return '<span class="b b-'.strtolower($s).'">'.e($s).'</span>'; }
function flash($m){ $_SESSION['f'] = $m; }
function head($t){
  global $base; $u = $_SESSION['u']; $r = $u['role']; $cur = basename($_SERVER['PHP_SELF']);
  $nav = $r == 'admin' ? ['dashboard.php'=>'Semua Pengaduan'] : ['dashboard.php'=>'Dashboard','buat.php'=>'Buat Pengaduan','riwayat.php'=>'Riwayat Pengaduan'];
  echo '<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.e($t).' | MIMIN</title><link rel="stylesheet" href="'.$base.'/assets/style.css"></head><body class="app"><aside><div class="logo">MIMIN<small>Pengaduan Sarana Sekolah</small></div><nav>';
  foreach ($nav as $f => $l) echo '<a href="'.$base.'/'.$r.'/'.$f.'"'.($cur == $f ? ' class="on"' : '').'>'.$l.'</a>';
  echo '</nav><div class="me">'.e($u['nama']).'<span>'.ucfirst($r).'</span><a href="'.$base.'/logout.php">Keluar</a></div></aside><main><h1>'.e($t).'</h1>';
  if (!empty($_SESSION['f'])) { echo '<div class="flash">'.e($_SESSION['f']).'</div>'; unset($_SESSION['f']); }
}
function foot(){ echo '</main></body></html>'; }
function detail_view($pdo, $id, $uid = null){
  global $base;
  $q = $pdo->prepare('SELECT p.*,u.nama murid,k.nama kategori FROM pengaduan p JOIN users u ON u.id=p.user_id JOIN kategori k ON k.id=p.kategori_id WHERE p.id=?'.($uid ? ' AND p.user_id=?' : ''));
  $q->execute($uid ? [$id, $uid] : [$id]); $p = $q->fetch();
  if (!$p) { echo '<div class="card">Pengaduan tidak ditemukan.</div>'; return null; }
  echo '<div class="card"><h2>'.e($p['judul']).' '.badge($p['status']).'</h2><dl><dt>Pelapor</dt><dd>'.e($p['murid']).'</dd><dt>Kategori</dt><dd>'.e($p['kategori']).'</dd><dt>Lokasi</dt><dd>'.e($p['lokasi']).'</dd><dt>Tanggal</dt><dd>'.date('d M Y, H:i', strtotime($p['tanggal'])).'</dd></dl><p>'.nl2br(e($p['deskripsi'])).'</p>';
  if ($p['foto']) echo '<img class="foto" src="'.$base.'/uploads/'.e($p['foto']).'" alt="Foto bukti">';
  echo '</div><div class="card"><h2>Perkembangan penanganan</h2><ul class="tl">';
  $h = $pdo->prepare('SELECT r.*,a.nama admin FROM riwayat_status r LEFT JOIN users a ON a.id=r.admin_id WHERE pengaduan_id=? ORDER BY r.id DESC'); $h->execute([$id]);
  foreach ($h as $r) echo '<li>'.badge($r['status']).' <small>'.date('d M Y, H:i', strtotime($r['created_at'])).($r['admin'] ? ' oleh '.e($r['admin']) : '').'</small><p>'.e($r['catatan']).'</p></li>';
  echo '</ul></div>'; return $p;
}
