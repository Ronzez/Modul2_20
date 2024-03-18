<?php

require_once __DIR__ . '/../helpers.php';

$email = $_POST['email'] ?? null;
$password = $_POST['parol'] ?? null;

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setOldValue('email', $email);
    setValidationError('email', 'Неверный формат электронной почты');
    setMessage('error', 'Ошибка валидации');
    redirect('/');
}

$user = findUser($email);

if (!$user) {
    $admin = findAdmin($email);
    redirect('/sait.loc/admin.php');
}

if (!$user && !$admin) {
    setMessage('error', "Пользователь $email не найден");
    redirect('/sait.loc/index.php');
}

if (!password_verify($password, $user['parol'])) {
    setMessage('error', 'Неверный пароль');
    redirect('/sait.loc/index.php');
}

$_SESSION['user']['id'] = $user['id'];

redirect('/sait.loc/home.php');