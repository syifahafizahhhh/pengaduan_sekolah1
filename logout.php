<?php
require __DIR__.'/config/koneksi.php';
session_destroy(); header('Location: '.$base.'/login.php');
