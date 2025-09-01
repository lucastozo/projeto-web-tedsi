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

DELIMITER //
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
	IN p_ant_tipo VARCHAR(30),
    IN p_nv_tipo VARCHAR(30)
)
BEGIN
DECLARE cnt_tipo INT;
SELECT COUNT(*) INTO cnt_tipo FROM tipo WHERE nome = p_ant_tipo;
IF cnt_tipo = 0 THEN
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Esse tipo não foi cadastrado.';
END IF;
SET @cnt_tipo := 0;
SELECT COUNT(*) INTO cnt_tipo FROM tipo WHERE nome = p_nv_tipo;
IF cnt_tipo > 0 THEN
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Esse tipo já está cadastrado.';
END IF;
UPDATE tipo SET nome = p_nv_tipo WHERE nome = p_ant_tipo;
END //

CREATE PROCEDURE deletar_tipo(
IN p_dlt_tipo VARCHAR(30)
)
BEGIN
DECLARE cnt_tipo INT;
SELECT COUNT(*) INTO cnt_tipo FROM tipo WHERE nome = p_dlt_tipo;
IF cnt_tipo = 0 THEN
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Esse tipo não foi cadastrado.';
END IF;
DELETE FROM tipo WHERE nome = p_dlt_tipo;
END //

CREATE PROCEDURE listar_tipo()
BEGIN
SELECT nome FROM tipo;
END //
