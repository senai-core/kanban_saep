# Quadro de Tarefas (SAEP)

Sistema de gerenciamento de tarefas no estilo kanban, feito em PHP puro com MySQL para a prova prática do SAEP.

## Funcionalidades

- Cadastro, edição e exclusão de usuários (nome e e-mail)
- Cadastro e edição de tarefas com título, descrição, setor, prioridade, data, status e usuário responsável
- Quadro com as colunas **A fazer**, **Fazendo** e **Pronto**
- Troca de status direto no cartão da tarefa
- Exclusão com confirmação

## Como rodar (XAMPP)

1. Inicie o Apache e o MySQL no painel do XAMPP.
2. Abra o phpMyAdmin e importe o arquivo `create_db.sql` (cria o banco `db_saep` com dados de exemplo).
3. Copie a pasta `kanban_saep` para `C:\xampp\htdocs\`.
4. Acesse `http://localhost/kanban_saep`.

A conexão está em `config.php` (usuário `root`, sem senha, host `localhost`).

## Autor

Guilherme Wohl
