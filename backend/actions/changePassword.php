<?php

require_once __DIR__ . '/../config/core.php';

$valid = array('success' => false, 'messages' => '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['password'] ?? '';
    $newPassword = $_POST['npassword'] ?? '';
    $confirmPassword = $_POST['cpassword'] ?? '';
    $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);

    if (!$userId || $currentPassword === '' || $newPassword === '') {
        $valid['messages'] = 'All password fields are required.';
    } elseif ($newPassword !== $confirmPassword) {
        $valid['messages'] = 'New password does not match with Confirm password.';
    } else {
        $stmt = $connect->prepare('SELECT password FROM users WHERE user_id = ? LIMIT 1');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        $currentValid = false;
        if ($user) {
            $currentValid = password_verify($currentPassword, $user['password'])
                || hash_equals($user['password'], md5($currentPassword));
        }

        if (!$currentValid) {
            $valid['messages'] = 'Current password is incorrect.';
        } else {
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $update = $connect->prepare('UPDATE users SET password = ? WHERE user_id = ?');
            $update->bind_param('si', $newHash, $userId);

            if ($update->execute()) {
                $valid['success'] = true;
                $valid['messages'] = 'Successfully Updated';
            } else {
                $valid['messages'] = 'Error while updating the password.';
            }
            $update->close();
        }
    }
}

$connect->close();
echo json_encode($valid);
