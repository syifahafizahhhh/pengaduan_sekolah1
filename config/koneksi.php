<?php
session_start();
try {
  $pdo = new PDO('mysql:host=localhost;dbname=MIMIN;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
} catch (PDOException $e) { die('Koneksi gagal. Pastikan MySQL Laragon aktif dan database MIMIN sudah diimpor.'); }
$root = rtrim(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'])), '/');
$base = str_replace($root, '', str_replace('\\', '/', dirname(__DIR__)));
