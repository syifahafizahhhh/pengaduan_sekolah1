<?php
require __DIR__.'/../config/koneksi.php'; require __DIR__.'/../includes/layout.php'; need('siswa');
head('Detail Pengaduan'); detail_view($pdo, (int)($_GET['id'] ?? 0), $_SESSION['u']['id']); echo '<a href="riwayat.php">&larr; Kembali</a>'; foot();
