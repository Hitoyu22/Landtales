<?php
global $pdo;

include_once('../model/user.php');

if ($_POST['firstname'] && $_POST['lastname'] && $_POST['password']) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];

    $allGender = ['man', 'woman', 'helicopter'];
    $error = false;
    $passwordError = false;


    if ( isset($firstname) && strlen($firstname) < 2) {
        $error = true;
    }
    if ( isset($lastname) && strlen($lastname) < 2) {
        $error = true;
    }
    if (isset($email) && !preg_match('/^[\w\-\.]+@([\w-]+\.)+[\w-]{2,4}$/', $email) ) {
        $error = true;
    }
    if (isset($password) && !preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
        $passwordError = true;
    }
    if (isset($gender) && !in_array($gender, $allGender) ) {
        $error = true;
    }
    if ($error) {
        $location = 'Location: addUser.php?';

        if ($passwordError) {
            $location = $location . 'password=0&';
        }

        header($location);
    }
}

$success = addUser($firstname, $lastname, $password, $email, $gender, $pdo);

if ($success) {
    $location = 'Location: /account';
}

include('../view/register_view.php');
