<?php $aviso = pegarAviso(); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo) ?> | Quadro de Tarefas</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <header class="barra">
        <strong class="logo">Quadro de Tarefas</strong>
        <nav class="menu">
            <a href="index.php">Gerenciar tarefas</a>
            <a href="tarefa.php">Nova tarefa</a>
            <a href="usuarios.php">Usuários</a>
        </nav>
    </header>
    <main class="conteudo">
        <?php if ($aviso): ?>
            <p class="alerta alerta-<?= e($aviso['tipo']) ?>"><?= e($aviso['texto']) ?></p>
        <?php endif; ?>
