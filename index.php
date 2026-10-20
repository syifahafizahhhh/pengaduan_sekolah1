<?php
require __DIR__.'/config/koneksi.php';
header('Location: '.$base.(empty($_SESSION['u']) ? '/login.php' : '/'.$_SESSION['u']['role'].'/dashboard.php'));
