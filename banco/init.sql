-- Create tables
CREATE TABLE IF NOT EXISTS pokemon(
id INTEGER AUTO_INCREMENT PRIMARY KEY,
imagem VARCHAR(255),
nome VARCHAR(20),
altura FLOAT(4,1),
peso FLOAT(5,2),
descricao VARCHAR(255));

CREATE TABLE IF NOT EXISTS habilidade(
id INTEGER AUTO_INCREMENT PRIMARY KEY,
nome VARCHAR(30),
descricao VARCHAR(255));

CREATE TABLE IF NOT EXISTS tipo(
id INTEGER AUTO_INCREMENT PRIMARY KEY,
nome VARCHAR(30));

CREATE TABLE IF NOT EXISTS tem_habilidade(
id INTEGER AUTO_INCREMENT PRIMARY KEY,
id_habilidade INTEGER,
id_pokemon INTEGER,
FOREIGN KEY (id_habilidade) REFERENCES habilidade(id),
FOREIGN KEY (id_pokemon) REFERENCES pokemon(id));

CREATE TABLE IF NOT EXISTS tem_tipo(
id INTEGER AUTO_INCREMENT PRIMARY KEY,
id_tipo INTEGER,
id_pokemon INTEGER,
FOREIGN KEY (id_tipo) REFERENCES tipo(id),
FOREIGN KEY (id_pokemon) REFERENCES pokemon(id));

-- Procedures
DELIMITER //

-- CRUD tipo
CREATE PROCEDURE novo_tipo(
IN p_nv_tipo VARCHAR(30)
)
BEGIN
DECLARE cnt_tipo INT;
SELECT COUNT(*) INTO cnt_tipo FROM tipo WHERE nome=p_nv_tipo;
IF cnt_tipo > 0 THEN
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Já existe esse tipo cadastrado';
END IF;
INSERT INTO tipo(nome) VALUES (p_nv_tipo);
END //

CREATE PROCEDURE atualizar_tipo(
IN p_id INT,
IN p_nv_tipo VARCHAR(30)
)
BEGIN
DECLARE cnt_tipo INT;
SELECT COUNT(*) INTO cnt_tipo FROM tipo WHERE id = p_id;
IF cnt_tipo = 0 THEN
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Esse id não foi existe.';
END IF;
SET @cnt_tipo := 0;
SELECT COUNT(*) INTO cnt_tipo FROM tipo WHERE nome = p_nv_tipo AND id != p_id;
IF cnt_tipo > 0 THEN
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Esse tipo já está cadastrado.';
END IF;
UPDATE tipo SET nome = p_nv_tipo WHERE id = p_id;
END //

CREATE PROCEDURE deletar_tipo(
IN p_dlt_tipo INT
)
BEGIN
DECLARE cnt_tipo INT;
SELECT COUNT(*) INTO cnt_tipo FROM tipo WHERE id = p_dlt_tipo;
IF cnt_tipo = 0 THEN
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Esse ID não existe.';
END IF;
DELETE FROM tipo WHERE id = p_dlt_tipo;
END //

CREATE PROCEDURE listar_tipo()
BEGIN
SELECT nome FROM tipo;
END //

-- CRUD habilidade

CREATE PROCEDURE nova_habilidade(
IN p_nv_nome VARCHAR(30),
IN p_nv_descricao VARCHAR(255)
)
BEGIN
DECLARE cnt_habilidade INT;
SELECT COUNT(*) INTO cnt_habilidade FROM habilidade WHERE nome = p_nv_nome;
IF cnt_habilidade > 0 THEN
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Essa habilidade já foi cadastrada.';
END IF;
INSERT INTO habilidade(nome, descricao) VALUES (p_nv_nome, p_nv_descricao);
END //

CREATE PROCEDURE atualizar_habilidade(
IN p_id INT,
IN p_nv_nome VARCHAR(30),
IN p_nv_descricao VARCHAR(255)
)
BEGIN
DECLARE cnt_habilidade INT;
SELECT COUNT(*) INTO cnt_habilidade FROM habilidade WHERE id = p_id;
IF cnt_habilidade = 0 THEN
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Não existe uma habilidade com esse id.';
END IF;
SET @cnt_habilidade := 0;
SELECT COUNT(*) INTO cnt_habilidade FROM habilidade WHERE nome = p_nv_nome AND id != p_id;
IF cnt_habilidade > 0 THEN
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Já existe uma habilidade com esse nome.';
END IF;
UPDATE habilidade SET nome = p_nv_nome, descricao = p_nv_descricao WHERE id = p_id;
END //

CREATE PROCEDURE deletar_habilidade(
IN p_dlt_habilidade INT
)
BEGIN
DECLARE cnt_habilidade INT;
SELECT COUNT(*) INTO cnt_habilidade FROM habilidade WHERE id  = p_dlt_habilidade;
IF cnt_habilidade = 0 THEN
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Esse ID não foi encontrado.';
END IF;
DELETE FROM habilidade WHERE id = p_dlt_habilidade;
END //

DELIMITER ;
--Caso necessario
DROP PROCEDURE novo_tipo;
DROP PROCEDURE atualizar_tipo;
DROP PROCEDURE deletar_tipo;

DROP PROCEDURE nova_habilidade;
DROP PROCEDURE atualizar_habilidade;
DROP PROCEDURE deletar_habilidade;
