<?php

require_once __DIR__ . '/../config/core.php';

$valid = array('success' => false, 'messages' => '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userName = trim($_POST['userName'] ?? '');
    $upassword = $_POST['upassword'] ?? '';
    $uemail = trim($_POST['uemail'] ?? '');

    if ($userName === '' || $upassword === '') {
        $valid['messages'] = 'Username and password are required.';
    } else {
        $passwordHash = password_hash($upassword, PASSWORD_DEFAULT);
        $stmt = $connect->prepare('INSERT INTO users (username, password, email) VALUES (?, ?, ?)');

        if ($stmt) {
            $stmt->bind_param('sss', $userName, $passwordHash, $uemail);
            if ($stmt->execute()) {
                $valid['success'] = true;
                $valid['messages'] = 'Successfully Added';
            } else {
                $valid['messages'] = 'Error while adding the user: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $valid['messages'] = 'Unable to prepare the database request.';
        }
    }
}

$connect->close();
echo json_encode($valid);
