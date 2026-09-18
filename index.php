<?php

require 'funcoes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tarefaId = (int) ($_POST['tar_id'] ?? 0);
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'status') {
        $novoStatus = $_POST['tar_status'] ?? '';

        if (!isset(STATUS[$novoStatus])) {
            avisar('Status inválido.', 'erro');
            irPara('index.php');
        }

        $stmt = $pdo->prepare('UPDATE tarefas SET tar_status = ? WHERE tar_id = ?');
        $stmt->execute([$novoStatus, $tarefaId]);
        avisar('Tarefa movida para "' . STATUS[$novoStatus] . '".');
    }

    if ($acao === 'excluir') {
        $stmt = $pdo->prepare('DELETE FROM tarefas WHERE tar_id = ?');
        $stmt->execute([$tarefaId]);
        avisar($stmt->rowCount() > 0 ? 'Tarefa excluída.' : 'Tarefa não encontrada.', $stmt->rowCount() > 0 ? 'ok' : 'erro');
    }

    irPara('index.php');
}

$tarefas = $pdo->query(
    "SELECT t.*, u.usu_nome
     FROM tarefas t
     INNER JOIN usuarios u ON u.usu_id = t.usu_id
     ORDER BY FIELD(t.tar_prioridade, 'alta', 'media', 'baixa'), t.tar_data_cadastro"
)->fetchAll();

$colunas = array_fill_keys(array_keys(STATUS), []);

foreach ($tarefas as $tarefa) {
    $colunas[$tarefa['tar_status']][] = $tarefa;
}

$titulo = 'Gerenciar tarefas';
require 'topo.php';
?>
<div class="cabecalho-pagina">
    <h1>Gerenciar tarefas</h1>
    <a class="btn btn-cheio" href="tarefa.php">+ Nova tarefa</a>
</div>

<section class="quadro">
    <?php foreach ($colunas as $status => $lista): ?>
        <div class="raia raia-<?= e(str_replace(' ', '-', $status)) ?>">
            <h2 class="raia-titulo">
                <?= e(STATUS[$status]) ?>
                <span class="contador"><?= count($lista) ?></span>
            </h2>

            <?php if (!$lista): ?>
                <p class="raia-vazia">Nenhuma tarefa aqui.</p>
            <?php endif; ?>

            <?php foreach ($lista as $tarefa): ?>
                <article class="cartao">
                    <div class="cartao-topo">
                        <span class="selo selo-<?= e($tarefa['tar_prioridade']) ?>"><?= e(PRIORIDADES[$tarefa['tar_prioridade']]) ?></span>
                        <time><?= e(date('d/m/Y', strtotime($tarefa['tar_data_cadastro']))) ?></time>
                    </div>

                    <h3><?= e($tarefa['tar_titulo']) ?></h3>
                    <p class="cartao-texto"><?= nl2br(e($tarefa['tar_descricao'])) ?></p>

                    <dl class="cartao-info">
                        <dt>Setor</dt>
                        <dd><?= e($tarefa['tar_setor']) ?></dd>
                        <dt>Responsável</dt>
                        <dd><?= e($tarefa['usu_nome']) ?></dd>
                    </dl>

                    <form class="mover" method="post">
                        <input type="hidden" name="acao" value="status">
                        <input type="hidden" name="tar_id" value="<?= (int) $tarefa['tar_id'] ?>">
                        <select name="tar_status" aria-label="Novo status">
                            <?php foreach (STATUS as $valor => $rotulo): ?>
                                <option value="<?= e($valor) ?>" <?= $valor === $tarefa['tar_status'] ? 'selected' : '' ?>><?= e($rotulo) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-leve" type="submit">Alterar status</button>
                    </form>

                    <div class="cartao-acoes">
                        <a class="btn btn-leve" href="tarefa.php?id=<?= (int) $tarefa['tar_id'] ?>">Editar</a>
                        <form method="post" onsubmit="return confirm(<?= e(json_encode('Excluir a tarefa "' . $tarefa['tar_titulo'] . '"?', JSON_UNESCAPED_UNICODE)) ?>);">
                            <input type="hidden" name="acao" value="excluir">
                            <input type="hidden" name="tar_id" value="<?= (int) $tarefa['tar_id'] ?>">
                            <button class="btn btn-perigo" type="submit">Excluir</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</section>
<?php require 'rodape.php'; ?>
