<?php

require_once __DIR__ . '/../helpers.php';

// Выносим данных из $_POST в отдельные переменные

$avatarPath = null;
$name = $_POST['FIO'] ?? null;
$email = $_POST['email'] ?? null;
$password = $_POST['parol'] ?? null;
$passwordConfirmation = $_POST['parol_confirmation'] ?? null;
$avatar = $_FILES['photo'] ?? null;

// Выполняем валидацию полученных данных с формы

if (empty($name)) {
    setValidationError('FIO', 'Неверное имя');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setValidationError('email', 'Указана неправильная почта');
}

if (empty($password)) {
    setValidationError('parol', 'Пароль пустой');
}

if ($password !== $passwordConfirmation) {
    setValidationError('parol', 'Пароли не совпадают');
}

if (!empty($avatar)) {
    $types = ['image/jpeg', 'image/png'];

    if (!in_array($avatar['type'], $types)) {
        setValidationError('photo', 'Изображение профиля имеет неверный тип');
    }

    if (($avatar['size'] / 1000000) >= 1) {
        setValidationError('photo', 'Изображение должно быть меньше 1 мб');
    }
}

// Если список с ошибками валидации не пустой, то производим редирект обратно на форму

if (!empty($_SESSION['validation'])) {
    setOldValue('FIO', $name);
    setOldValue('email', $email);
    redirect('/sait.loc/register.php');
}

//  Загружаем аватарку, если она была отправлена в форме

if (!empty($avatar)) {
    $avatarPath = uploadFile($avatar, 'photo');
}

$pdo = getPDO();

$query = "INSERT INTO client (FIO, Email, Photo, Parol) VALUES (:name, :email, :avatar, :password)";

$params = [
    'FIO' => $name,
    'email' => $email,
    'photo' => $avatarPath,
    'parol' => password_hash($password, PASSWORD_DEFAULT)
];

$stmt = $pdo->prepare($query);

try {
    $stmt->execute($params);
} catch (\Exception $e) {
    die($e->getMessage());
}

redirect('/sait.loc/home.php');
