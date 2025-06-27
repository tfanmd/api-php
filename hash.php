<?php
$password_saya = 'admin123';
$hash = password_hash($password_saya, PASSWORD_DEFAULT);
echo $hash;
