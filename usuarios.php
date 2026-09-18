<?php

require 'funcoes.php';

$id = (int) ($_GET['id'] ?? 0);
$erros = [];
$usuario = ['usu_nome' => '', 'usu_email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir'])) {
    $stmt = $pdo->prepare('DELETE FROM usuarios WHERE usu_id = ?');
    $stmt->execute([(int) $_POST['excluir']]);
    avisar('Usuário excluído junto com as tarefas dele.');
    irPara('usuarios.php');
}

if ($id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE usu_id = ?');
    $stmt->execute([$id]);
    $encontrado = $stmt->fetch();

    if (!$encontrado) {
        avisar('Usuário não encontrado.', 'erro');
        irPara('usuarios.php');
    }

    $usuario = $encontrado;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario['usu_nome'] = trim($_POST['usu_nome'] ?? '');
    $usuario['usu_email'] = trim($_POST['usu_email'] ?? '');

    if ($usuario['usu_nome'] === '') {
        $erros[] = 'Informe o nome.';
    }

    if (!filter_var($usuario['usu_email'], FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'Informe um e-mail válido.';
    }

    if (!$erros) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM usuarios WHERE usu_email = ? AND usu_id <> ?');
        $stmt->execute([$usuario['usu_email'], $id]);

        if ($stmt->fetchColumn() > 0) {
            $erros[] = 'Esse e-mail já está cadastrado.';
        }
    }

    if (!$erros) {
        if ($id > 0) {
            $stmt = $pdo->prepare('UPDATE usuarios SET usu_nome = ?, usu_email = ? WHERE usu_id = ?');
            $stmt->execute([$usuario['usu_nome'], $usuario['usu_email'], $id]);
            avisar('Usuário atualizado.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO usuarios (usu_nome, usu_email) VALUES (?, ?)');
            $stmt->execute([$usuario['usu_nome'], $usuario['usu_email']]);
            avisar('Cadastro concluído com sucesso.');
        }

        irPara('usuarios.php');
    }
}

$lista = $pdo->query(
    'SELECT u.usu_id, u.usu_nome, u.usu_email, COUNT(t.tar_id) AS total_tarefas
     FROM usuarios u
     LEFT JOIN tarefas t ON t.usu_id = u.usu_id
     GROUP BY u.usu_id, u.usu_nome, u.usu_email
     ORDER BY u.usu_nome'
)->fetchAll();

$titulo = $id > 0 ? 'Editar usuário' : 'Usuários';
require 'topo.php';
?>
<div class="cabecalho-pagina">
    <h1><?= $id > 0 ? 'Editar usuário' : 'Cadastro de usuários' ?></h1>
</div>

<?php if ($erros): ?>
    <ul class="alerta alerta-erro">
        <?php foreach ($erros as $erro): ?>
            <li><?= e($erro) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form class="painel" method="post" action="usuarios.php<?= $id > 0 ? '?id=' . $id : '' ?>">
    <div class="grade">
        <label>
            Nome
            <input type="text" name="usu_nome" maxlength="40" value="<?= e($usuario['usu_nome']) ?>" required>
        </label>

        <label>
            E-mail
            <input type="email" name="usu_email" maxlength="80" value="<?= e($usuario['usu_email']) ?>" required>
        </label>
    </div>

    <div class="linha-botoes">
        <button class="btn btn-cheio" type="submit"><?= $id > 0 ? 'Salvar alterações' : 'Cadastrar usuário' ?></button>
        <?php if ($id > 0): ?>
            <a class="btn btn-leve" href="usuarios.php">Cancelar</a>
        <?php endif; ?>
    </div>
</form>

<h2 class="subtitulo">Usuários cadastrados</h2>

<?php if (!$lista): ?>
    <p class="raia-vazia">Nenhum usuário cadastrado ainda.</p>
<?php else: ?>
    <div class="tabela-rolagem">
        <table class="tabela">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Tarefas</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lista as $linha): ?>
                    <tr>
                        <td><?= e($linha['usu_nome']) ?></td>
                        <td><?= e($linha['usu_email']) ?></td>
                        <td><?= (int) $linha['total_tarefas'] ?></td>
                        <td class="celula-acoes">
                            <a class="btn btn-leve" href="usuarios.php?id=<?= (int) $linha['usu_id'] ?>">Editar</a>
                            <form method="post" action="usuarios.php" onsubmit="return confirm(<?= e(json_encode('Excluir ' . $linha['usu_nome'] . ' e todas as tarefas dele?', JSON_UNESCAPED_UNICODE)) ?>);">
                                <input type="hidden" name="excluir" value="<?= (int) $linha['usu_id'] ?>">
                                <button class="btn btn-perigo" type="submit">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php require 'rodape.php'; ?>
