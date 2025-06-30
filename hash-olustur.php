<?php
$yeniSifre = 'doctoraltansecurity123';

$hashedSifre = password_hash($yeniSifre, PASSWORD_DEFAULT);

echo "<b>Yeni Şifreniz:</b> " . htmlspecialchars($yeniSifre) . "<br><br>";
echo "<b>Veritabanına Kopyalanacak Hash Kodu:</b><br>";
echo '<textarea rows="4" cols="70" readonly style="font-size:14px; padding:5px; margin-top:5px;">' . $hashedSifre . '</textarea>';
?>