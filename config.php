<?php

$host = 'localhost';
$banco = 'db_saep';
$usuarioBanco = 'root';
$senhaBanco = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$banco;charset=utf8mb4", $usuarioBanco, $senhaBanco, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $erro) {
    exit('Erro ao conectar no banco: ' . $erro->getMessage());
}
