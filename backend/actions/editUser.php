<?php

require_once __DIR__ . '/../config/core.php';

$valid = array('success' => false, 'messages' => '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $edituserName = trim($_POST['edituserName'] ?? '');
    $editPassword = $_POST['editPassword'] ?? '';
    $userid = filter_input(INPUT_POST, 'userid', FILTER_VALIDATE_INT);

    if ($edituserName === '' || !$userid) {
        $valid['messages'] = 'Username and user ID are required.';
    } else {
        if ($editPassword !== '') {
            $passwordHash = password_hash($editPassword, PASSWORD_DEFAULT);
            $stmt = $connect->prepare('UPDATE users SET username = ?, password = ? WHERE user_id = ?');
            if ($stmt) {
                $stmt->bind_param('ssi', $edituserName, $passwordHash, $userid);
            }
        } else {
            $stmt = $connect->prepare('UPDATE users SET username = ? WHERE user_id = ?');
            if ($stmt) {
                $stmt->bind_param('si', $edituserName, $userid);
            }
        }

        if (isset($stmt) && $stmt) {
            if ($stmt->execute()) {
                $valid['success'] = true;
                $valid['messages'] = 'Successfully Updated';
            } else {
                $valid['messages'] = 'Error while updating user information.';
            }
            $stmt->close();
        } else {
            $valid['messages'] = 'Unable to prepare the database request.';
        }
    }
}

$connect->close();
echo json_encode($valid);
