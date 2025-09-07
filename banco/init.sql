DROP DATABASE IF EXISTS pokemon;
CREATE DATABASE pokemon;
USE pokemon;

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

-- View

CREATE VIEW vw_pokemon AS
SELECT p.id, p.imagem, p.nome, p.altura, p.peso, p.descricao,
    (
        SELECT JSON_ARRAYAGG(t.nome)
        FROM tem_tipo tt
        JOIN tipo t ON t.id = tt.id_tipo
        WHERE tt.id_pokemon = p.id
        ORDER BY t.nome
    ) AS tipos,
    (
        SELECT JSON_ARRAYAGG(h.nome)
        FROM tem_habilidade th
        JOIN habilidade h ON h.id = th.id_habilidade
        WHERE th.id_pokemon = p.id
        ORDER BY h.nome
    ) AS habilidades
FROM pokemon p;

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
SET @cnt_tipo := 0;
SELECT COUNT(*) INTO cnt_tipo FROM tem_tipo WHERE id_tipo = p_dlt_tipo;
IF cnt_tipo > 0 THEN
SET @err_message := CONCAT('Erro, ', cnt_tipo, ' pokemons utilizam esse tipo.');
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = @err_message;
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
SET @cnt_habilidade := 0;
SELECT COUNT(*) INTO cnt_habilidade FROM tem_habilidade WHERE id_habilidade = p_dlt_habilidade;
IF cnt_habilidade > 0 THEN
SET @err_message := CONCAT('Erro, ', cnt_habilidade, ' pokemons utilizam essa habilidade.');
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = @err_message;
END IF;
DELETE FROM habilidade WHERE id = p_dlt_habilidade;
END //

-- CRUD pokemon


CREATE PROCEDURE novo_pokemon(
IN p_path_image VARCHAR(255),
IN p_nv_nome VARCHAR(30),
IN p_nv_altura FLOAT(4,1),
IN p_nv_peso FLOAT(5,2),
IN p_nv_descricao VARCHAR(255),
IN p_nv_tipo JSON,
IN p_nv_habilidade JSON
)
BEGIN
DECLARE cnt_pokemon INT;
DECLARE v_nv_id INT;
DECLARE i INT DEFAULT 0;
SELECT COUNT(*) INTO cnt_pokemon FROM pokemon WHERE nome = p_nv_nome;
IF cnt_pokemon > 0 THEN 
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Esse pokemon já foi cadastrado.';
END IF;
INSERT INTO pokemon(imagem, nome, altura, peso, descricao) VALUES (p_path_image, p_nv_nome, p_nv_altura, p_nv_peso, p_nv_descricao);
SELECT LAST_INSERT_ID() INTO v_nv_id;
WHILE i < JSON_LENGTH(p_nv_tipo) DO
SELECT COUNT(*) INTO cnt_pokemon FROM tipo WHERE id = JSON_UNQUOTE(JSON_EXTRACT(p_nv_tipo, CONCAT('$[',i,']')));
IF cnt_pokemon = 0 THEN
DELETE FROM tem_tipo WHERE id_pokemon = v_nv_id;
DELETE FROM pokemon WHERE id = v_nv_id;
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Esse id tipo não existe.';
END IF;
INSERT INTO tem_tipo(id_pokemon, id_tipo) VALUES (v_nv_id, JSON_EXTRACT(p_nv_tipo, CONCAT('$[',i,']')));
SET i = i + 1;
SET cnt_pokemon = 0;
END WHILE;
SET i = 0;
WHILE i < JSON_LENGTH(p_nv_habilidade) DO
SELECT COUNT(*) INTO cnt_pokemon FROM habilidade WHERE id = JSON_UNQUOTE(JSON_EXTRACT(p_nv_habilidade, CONCAT('$[',i,']')));
IF cnt_pokemon = 0 THEN
DELETE FROM tem_tipo WHERE id_pokemon = v_nv_id;
DELETE FROM pokemon WHERE id = v_nv_id;
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Esse id habilidade não existe.';
END IF;
INSERT INTO tem_habilidade(id_pokemon, id_habilidade) VALUES (v_nv_id, JSON_EXTRACT(p_nv_habilidade, CONCAT('$[',i,']')));
SET i = i + 1;
SET cnt_pokemon = 0;
END WHILE;
END //

CREATE PROCEDURE atualizar_pokemon(
IN p_id INT,
IN p_nv_imagem VARCHAR(255),
IN p_nv_nome VARCHAR(30),
IN p_nv_altura FLOAT(4,1),
IN p_nv_peso FLOAT(5,2),
IN p_nv_descricao VARCHAR(255),
IN p_nv_tipo JSON,
IN p_nv_habilidade JSON
)
BEGIN
DECLARE cnt_pokemon INT;
DECLARE i INT DEFAULT 0;
SELECT COUNT(*) INTO cnt_pokemon FROM pokemon WHERE id = p_id;
IF cnt_pokemon = 0 THEN
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Não existe um pokemon com esse id.';
END IF;
SET cnt_pokemon := 0;
SELECT COUNT(*) INTO cnt_pokemon FROM pokemon WHERE nome = p_nv_nome AND id != p_id;
IF cnt_pokemon > 0 THEN
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Já existe um pokemon com esse nome.';
END IF;
UPDATE pokemon SET imagem = p_nv_imagem, nome = p_nv_nome, altura = p_nv_altura, peso = p_nv_peso, descricao = p_nv_descricao WHERE id = p_id;
DELETE FROM tem_tipo WHERE id_pokemon = p_id;
DELETE FROM tem_habilidade WHERE id_pokemon = p_id;
WHILE i < JSON_LENGTH(p_nv_tipo) DO
SELECT COUNT(*) INTO cnt_pokemon FROM tipo WHERE id = JSON_UNQUOTE(JSON_EXTRACT(p_nv_tipo, CONCAT('$[',i,']')));
IF cnt_pokemon = 0 THEN
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Esse id tipo não existe.';
END IF;
INSERT INTO tem_tipo(id_pokemon, id_tipo) VALUES (p_id, JSON_EXTRACT(p_nv_tipo, CONCAT('$[',i,']')));
SET i = i + 1;
SET cnt_pokemon = 0;
END WHILE;
SET i = 0;
WHILE i < JSON_LENGTH(p_nv_habilidade) DO
SELECT COUNT(*) INTO cnt_pokemon FROM habilidade WHERE id = JSON_UNQUOTE(JSON_EXTRACT(p_nv_habilidade, CONCAT('$[',i,']')));
IF cnt_pokemon = 0 THEN
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Esse id habilidade não existe.';
END IF;
INSERT INTO tem_habilidade(id_pokemon, id_habilidade) VALUES (p_id, JSON_EXTRACT(p_nv_habilidade, CONCAT('$[',i,']')));
SET i = i + 1;
SET cnt_pokemon = 0;
END WHILE;
END //

CREATE PROCEDURE deletar_pokemon(
IN p_dlt_pokemon INT
)
BEGIN
DECLARE cnt_pokemon INT;
SELECT COUNT(*) INTO cnt_pokemon FROM pokemon WHERE id = p_dlt_pokemon;
IF cnt_pokemon = 0 THEN
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Esse ID não existe.';
END IF;
DELETE FROM tem_tipo WHERE id_pokemon = p_dlt_pokemon;
DELETE FROM tem_habilidade WHERE id_pokemon = p_dlt_pokemon;
DELETE FROM pokemon WHERE id = p_dlt_pokemon;
END //


DELIMITER ;
--Caso necessario

DROP PROCEDURE novo_tipo;
DROP PROCEDURE atualizar_tipo;
DROP PROCEDURE deletar_tipo;

DROP PROCEDURE nova_habilidade;
DROP PROCEDURE atualizar_habilidade;
DROP PROCEDURE deletar_habilidade;

DROP PROCEDURE novo_pokemon;
DROP PROCEDURE atualizar_pokemon;
DROP PROCEDURE deletar_pokemon;