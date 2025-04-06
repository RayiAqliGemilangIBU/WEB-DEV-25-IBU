<?php
// Dummy data pengguna
$users = [
    ['id' => 1, 'name' => 'User Satu', 'email' => 'user1@example.com'],
    ['id' => 2, 'name' => 'User Dua', 'email' => 'user2@example.com'],
    ['id' => 3, 'name' => 'User Tiga', 'email' => 'user3@example.com']
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Daftar Pengguna</title>
</head>
<body>
    <h2>Daftar Pengguna</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Email</th>
        </tr>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo $user['id']; ?></td>
            <td><?php echo htmlspecialchars($user['name']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
