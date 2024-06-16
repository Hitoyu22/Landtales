<?php
global $pdo;

function addUser($firstname, $lastname, $password, $email, $gender, $pdo)
{
    $passwordHash = password_hash($password,  PASSWORD_BCRYPT);

    $sql = "
        INSERT INTO users (firstname, lastname, email, password, gender)
        VALUES (:firstname, :lastname, :email, :password, :gender)
    ";

    $preparedSql = $pdo->prepare($sql);
    return $preparedSql->execute([
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $email,
        'password' => $passwordHash,
        'gender' => $gender,
    ]);
}

function getUser($id, $select, $pdo)
{
    $sql = "SELECT $select FORM users WHERE id=$id";
    $sql = $pdo->prepare($sql);
    $sql->execute();
}
function getAllUsers()
{

}

function updateUser($id)
{

}