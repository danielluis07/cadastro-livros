CREATE TABLE usuarios (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL,
    `senha` VARCHAR(255) NOT NULL
);

CREATE TABLE livros (
    `codigo` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(200),
    `autor` VARCHAR(200),
    `editora` VARCHAR(100),
    `ano` INT(4)
);
