<?php

session_start();

require __DIR__ . '/config.php';

const PRIORIDADES = [
    'baixa' => 'Baixa',
    'media' => 'Média',
    'alta' => 'Alta',
];

const STATUS = [
    'a fazer' => 'A fazer',
    'fazendo' => 'Fazendo',
    'pronto' => 'Pronto',
];

function e($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function avisar($texto, $tipo = 'ok')
{
    $_SESSION['aviso'] = ['texto' => $texto, 'tipo' => $tipo];
}

function pegarAviso()
{
    $aviso = $_SESSION['aviso'] ?? null;
    unset($_SESSION['aviso']);
    return $aviso;
}

function irPara($pagina)
{
    header('Location: ' . $pagina);
    exit;
}
