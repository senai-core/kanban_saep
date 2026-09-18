DROP DATABASE IF EXISTS db_saep;
CREATE DATABASE db_saep CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_saep;

CREATE TABLE usuarios (
    usu_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    usu_nome VARCHAR(40) NOT NULL,
    usu_email VARCHAR(80) NOT NULL UNIQUE
);

CREATE TABLE tarefas (
    tar_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    tar_titulo VARCHAR(60) NOT NULL,
    tar_descricao TEXT NOT NULL,
    tar_setor VARCHAR(40) NOT NULL,
    tar_prioridade ENUM('baixa', 'media', 'alta') NOT NULL DEFAULT 'baixa',
    tar_data_cadastro DATE NOT NULL,
    tar_status ENUM('a fazer', 'fazendo', 'pronto') NOT NULL DEFAULT 'a fazer',
    usu_id INT NOT NULL,
    CONSTRAINT fk_tarefa_usuario FOREIGN KEY (usu_id) REFERENCES usuarios (usu_id) ON DELETE CASCADE
);

INSERT INTO usuarios (usu_nome, usu_email) VALUES
    ('Marina Kowalski', 'marina.kowalski@fabrica.com.br'),
    ('Diego Tavares', 'diego.tavares@fabrica.com.br'),
    ('Renata Hoffmann', 'renata.hoffmann@fabrica.com.br');

INSERT INTO tarefas (tar_titulo, tar_descricao, tar_setor, tar_prioridade, tar_data_cadastro, tar_status, usu_id) VALUES
    ('Calibrar torno CNC', 'Conferir a folga do eixo X e registrar as medidas no caderno de manutenção.', 'Usinagem', 'alta', CURDATE(), 'a fazer', 1),
    ('Inventário do almoxarifado', 'Contar parafusos, porcas e arruelas e atualizar a planilha de estoque.', 'Almoxarifado', 'media', CURDATE(), 'fazendo', 2),
    ('Treinamento de NR-12', 'Organizar a turma de novos operadores para o treinamento de segurança em máquinas.', 'Segurança do Trabalho', 'alta', CURDATE(), 'a fazer', 3),
    ('Trocar filtro do compressor', 'Substituir o filtro de ar do compressor 2 e anotar a data da troca.', 'Manutenção', 'baixa', CURDATE(), 'pronto', 1),
    ('Relatório de refugo semanal', 'Levantar as peças descartadas na semana e separar por motivo.', 'Qualidade', 'media', CURDATE(), 'fazendo', 3),
    ('Atualizar quadro de turnos', 'Montar a escala do mês que vem com as férias já aprovadas.', 'Recursos Humanos', 'baixa', CURDATE(), 'pronto', 2);
