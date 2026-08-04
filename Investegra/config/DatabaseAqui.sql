CREATE DATABASE Projeto_Final;
USE Projeto_Final;
 
CREATE TABLE usuarios (
id INT AUTO_INCREMENT PRIMARY KEY,
 
nome VARCHAR(100) NOT NULL,
 
email VARCHAR(150) NOT NULL UNIQUE,
 
documento VARCHAR(20) NOT NULL,
 
senha VARCHAR(255) NOT NULL,
 
tipo_usuario ENUM('PF','PJ') NOT NULL,
 
perfil_risco ENUM(
'Conservador',
'Moderado',
'Arrojado'
) DEFAULT 'Moderado',
 
criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
 
select * from usuarios;
 
CREATE TABLE carteiras (
 
id INT AUTO_INCREMENT PRIMARY KEY,
 
nome VARCHAR(100) NOT NULL,
 
descricao TEXT,
 
usuario_id INT NOT NULL,
 
criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 
FOREIGN KEY (usuario_id)
REFERENCES usuarios(id)
);
 
select * from carteiras;
 
CREATE TABLE ativos (
 
id INT AUTO_INCREMENT PRIMARY KEY,
 
carteira_id INT NOT NULL,
 
ticker VARCHAR(10) NOT NULL,
 
quantidade INT NOT NULL,
 
preco_medio DECIMAL(10,2),
 
categoria varchar(50),
 
FOREIGN KEY (carteira_id)
REFERENCES carteiras(id)
 
);
 
ALTER TABLE ativos
 
ADD data_compra DATE,
 
ADD observacao TEXT;
 
select * from ativos;
 
CREATE TABLE historico_patrimonio(
 
id INT AUTO_INCREMENT PRIMARY KEY,
 
usuario_id INT NOT NULL,
 
valor_total DECIMAL(15,2) NOT NULL,
 
data_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
 
FOREIGN KEY (usuario_id)
REFERENCES usuarios(id)
 
);