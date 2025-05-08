<?php
// Ganti 'adminpass' dengan password yang ingin kamu hash
$password = 'adminpass';

// Generate hash
$hashed = password_hash($password, PASSWORD_DEFAULT);

echo "Password: $password\n";
echo "Hashed: $hashed\n";
