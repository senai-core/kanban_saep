<?php

require 'funcoes.php';

$id = (int) ($_GET['id'] ?? 0);
$erros = [];

$tarefa = [
    'tar_titulo' => '',
    'tar_descricao' => '',
    'tar_setor' => '',
    'tar_prioridade' => 'baixa',
    'tar_data_cadastro' => date('Y-m-d'),
    'tar_status' => 'a fazer',
    'usu_id' => 0,
];

if ($id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM tarefas WHERE tar_id = ?');
    $stmt->execute([$id]);
    $encontrada = $stmt->fetch();

    if (!$encontrada) {
        avisar('Tarefa não encontrada.', 'erro');
        irPara('index.php');
    }

    $tarefa = $encontrada;
}

$usuarios = $pdo->query('SELECT usu_id, usu_nome FROM usuarios ORDER BY usu_nome')->fetchAll();
$idsUsuarios = array_column($usuarios, 'usu_id');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tarefa = [
        'tar_titulo' => trim($_POST['tar_titulo'] ?? ''),
        'tar_descricao' => trim($_POST['tar_descricao'] ?? ''),
        'tar_setor' => trim($_POST['tar_setor'] ?? ''),
        'tar_prioridade' => $_POST['tar_prioridade'] ?? '',
        'tar_data_cadastro' => $_POST['tar_data_cadastro'] ?? '',
        'tar_status' => $_POST['tar_status'] ?? '',
        'usu_id' => (int) ($_POST['usu_id'] ?? 0),
    ];

    if ($tarefa['tar_titulo'] === '') {
        $erros[] = 'Informe o título da tarefa.';
    }

    if ($tarefa['tar_descricao'] === '') {
        $erros[] = 'Informe a descrição da tarefa.';
    }

    if ($tarefa['tar_setor'] === '') {
        $erros[] = 'Informe o setor.';
    }

    if (!isset(PRIORIDADES[$tarefa['tar_prioridade']])) {
        $erros[] = 'Escolha uma prioridade válida.';
    }

    if (!isset(STATUS[$tarefa['tar_status']])) {
        $erros[] = 'Escolha um status válido.';
    }

    $data = DateTime::createFromFormat('Y-m-d', $tarefa['tar_data_cadastro']);
    if (!$data || $data->format('Y-m-d') !== $tarefa['tar_data_cadastro']) {
        $erros[] = 'Informe uma data de cadastro válida.';
    }

    if (!in_array($tarefa['usu_id'], $idsUsuarios)) {
        $erros[] = 'Escolha o usuário responsável.';
    }

    if (!$erros) {
        $valores = [
            $tarefa['tar_titulo'],
            $tarefa['tar_descricao'],
            $tarefa['tar_setor'],
            $tarefa['tar_prioridade'],
            $tarefa['tar_data_cadastro'],
            $tarefa['tar_status'],
            $tarefa['usu_id'],
        ];

        if ($id > 0) {
            $valores[] = $id;
            $stmt = $pdo->prepare(
                'UPDATE tarefas
                 SET tar_titulo = ?, tar_descricao = ?, tar_setor = ?, tar_prioridade = ?,
                     tar_data_cadastro = ?, tar_status = ?, usu_id = ?
                 WHERE tar_id = ?'
            );
            $stmt->execute($valores);
            avisar('Tarefa atualizada.');
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO tarefas
                    (tar_titulo, tar_descricao, tar_setor, tar_prioridade, tar_data_cadastro, tar_status, usu_id)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute($valores);
            avisar('Tarefa cadastrada com sucesso.');
        }

        irPara('index.php');
    }
}

$titulo = $id > 0 ? 'Editar tarefa' : 'Nova tarefa';
require 'topo.php';
?>
<div class="cabecalho-pagina">
    <h1><?= e($titulo) ?></h1>
</div>

<?php if (!$usuarios): ?>
    <p class="alerta alerta-erro">Cadastre um usuário antes de criar tarefas. <a href="usuarios.php">Ir para usuários</a></p>
<?php endif; ?>

<?php if ($erros): ?>
    <ul class="alerta alerta-erro">
        <?php foreach ($erros as $erro): ?>
            <li><?= e($erro) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form class="painel" method="post">
    <label>
        Título
        <input type="text" name="tar_titulo" maxlength="60" value="<?= e($tarefa['tar_titulo']) ?>" required>
    </label>

    <label>
        Descrição
        <textarea name="tar_descricao" rows="4" required><?= e($tarefa['tar_descricao']) ?></textarea>
    </label>

    <div class="grade">
        <label>
            Setor
            <input type="text" name="tar_setor" maxlength="40" value="<?= e($tarefa['tar_setor']) ?>" required>
        </label>

        <label>
            Usuário responsável
            <select name="usu_id" required>
                <option value="">Selecione</option>
                <?php foreach ($usuarios as $usuario): ?>
                    <option value="<?= (int) $usuario['usu_id'] ?>" <?= (int) $usuario['usu_id'] === (int) $tarefa['usu_id'] ? 'selected' : '' ?>><?= e($usuario['usu_nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Prioridade
            <select name="tar_prioridade">
                <?php foreach (PRIORIDADES as $valor => $rotulo): ?>
                    <option value="<?= e($valor) ?>" <?= $valor === $tarefa['tar_prioridade'] ? 'selected' : '' ?>><?= e($rotulo) ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Status
            <select name="tar_status">
                <?php foreach (STATUS as $valor => $rotulo): ?>
                    <option value="<?= e($valor) ?>" <?= $valor === $tarefa['tar_status'] ? 'selected' : '' ?>><?= e($rotulo) ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Data de cadastro
            <input type="date" name="tar_data_cadastro" value="<?= e($tarefa['tar_data_cadastro']) ?>" required>
        </label>
    </div>

    <div class="linha-botoes">
        <button class="btn btn-cheio" type="submit"><?= $id > 0 ? 'Salvar alterações' : 'Cadastrar tarefa' ?></button>
        <a class="btn btn-leve" href="index.php">Voltar</a>
    </div>
</form>
<?php require 'rodape.php'; ?>
