CREATE TABLE cliente (
    cliente_id varchar(36) NOT NULL DEFAULT (UUID()),    
    cliente_nome varchar(255) NOT NULL,
    cliente_email varchar(255) NOT NULL UNIQUE,
    cliente_telefone varchar(14) NOT NULL,        
    cliente_ativo tinyint(1) NOT NULL,
    cliente_endereco varchar(255) DEFAULT NULL,
    cliente_cep varchar(8) DEFAULT NULL,
    cliente_cpf varchar(14) NOT NULL UNIQUE,
    cliente_assinaturas int NOT NULL,
    cliente_deputado_id int NOT NULL UNIQUE,
    cliente_deputado_nome varchar(255) NOT NULL,
    cliente_deputado_estado varchar(2) NOT NULL,
    cliente_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    cliente_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (cliente_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO cliente (cliente_id, cliente_nome, cliente_email, cliente_telefone, cliente_ativo, cliente_endereco, cliente_cep, cliente_cpf, cliente_assinaturas, cliente_deputado_id, cliente_deputado_nome, cliente_deputado_estado) 
VALUES ('1', 'CLIENTE SISTEMA', 'email@email.com', '000000', 1, 'Sem endereço', '00000000', '00000000000', 2, 0, 'deputado', 'DF');

CREATE TABLE usuario (
    usuario_id varchar(36) NOT NULL DEFAULT (UUID()),
    usuario_nome varchar(255) NOT NULL,
    usuario_email varchar(255) NOT NULL UNIQUE,
    usuario_telefone varchar(20) NOT NULL,
    usuario_senha varchar(255) NOT NULL,
    usuario_nivel int NOT NULL,
    usuario_ativo tinyint(1) NOT NULL,
    usuario_aniversario date NOT NULL,
    usuario_foto varchar(255) DEFAULT NULL,
    usuario_cliente varchar(36) NOT NULL,
    usuario_token varchar(255) DEFAULT NULL,
    usuario_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (usuario_id),
    CONSTRAINT fk_cliente FOREIGN KEY (usuario_cliente) REFERENCES cliente(cliente_id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;


INSERT INTO usuario (usuario_id, usuario_nome, usuario_email, usuario_telefone, usuario_senha, usuario_nivel, usuario_ativo, usuario_aniversario, usuario_cliente) 
VALUES ('1', 'USUÁRIO SISTEMA', 'email@email.com', '000000', 'sd9fasdfasd9fasd89fsad9f8', 1, 1, '2000-01-01', '1');

CREATE TABLE orgaos_tipos (
    orgao_tipo_id varchar(36) NOT NULL DEFAULT (UUID()),
    orgao_tipo_nome varchar(255) NOT NULL UNIQUE,
    orgao_tipo_descricao text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    orgao_tipo_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    orgao_tipo_criado_por varchar(36) NOT NULL,
    orgao_tipo_cliente varchar(36) NOT NULL,
    orgao_tipo_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (orgao_tipo_id),
    CONSTRAINT fk_orgao_tipo_criado_por FOREIGN KEY (orgao_tipo_criado_por) REFERENCES usuario(usuario_id),
    CONSTRAINT fk_orgao_tipo_cliente FOREIGN KEY (orgao_tipo_cliente) REFERENCES cliente(cliente_id)

) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_cliente) VALUES (1, 'Tipo não informado', 'Sem tipo definido', 1, 1);

CREATE TABLE orgaos (
    orgao_id varchar(36) NOT NULL DEFAULT (UUID()),
    orgao_nome text NOT NULL,
    orgao_email varchar(255) NOT NULL UNIQUE,
    orgao_telefone varchar(255) DEFAULT NULL,
    orgao_endereco text,
    orgao_bairro text,
    orgao_municipio varchar(255) NOT NULL,
    orgao_estado varchar(255) NOT NULL,
    orgao_cep varchar(255) DEFAULT NULL,
    orgao_tipo varchar(36) NOT NULL,
    orgao_informacoes text,
    orgao_site text,
    orgao_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    orgao_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    orgao_criado_por varchar(36) NOT NULL,
    orgao_cliente varchar(36) NOT NULL,
    PRIMARY KEY (orgao_id),
    CONSTRAINT fk_orgao_criado_por FOREIGN KEY (orgao_criado_por) REFERENCES usuario(usuario_id),
    CONSTRAINT fk_orgao_tipo FOREIGN KEY (orgao_tipo) REFERENCES orgaos_tipos(orgao_tipo_id),
    CONSTRAINT fk_orgao_cliente FOREIGN KEY (orgao_cliente) REFERENCES cliente(cliente_id)

) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO orgaos (orgao_id, orgao_nome, orgao_email, orgao_municipio, orgao_estado, orgao_tipo, orgao_criado_por, orgao_cliente) VALUES (1, 'Órgão não informado', 'email@email', 'municipio', 'estado', 1, 1, 1);

CREATE TABLE pessoas_tipos (
    pessoa_tipo_id varchar(36) NOT NULL DEFAULT (UUID()),
    pessoa_tipo_nome varchar(255) NOT NULL UNIQUE,
    pessoa_tipo_descricao text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    pessoa_tipo_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    pessoa_tipo_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    pessoa_tipo_criado_por varchar(36) NOT NULL,
    pessoa_tipo_cliente varchar(36) NOT NULL,
    PRIMARY KEY (pessoa_tipo_id),
    CONSTRAINT fk_pessoa_tipo_criado_por FOREIGN KEY (pessoa_tipo_criado_por) REFERENCES usuario (usuario_id),
    CONSTRAINT fk_essoa_tipo_cliente FOREIGN KEY (pessoa_tipo_cliente) REFERENCES cliente (cliente_id)

) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO pessoas_tipos (pessoa_tipo_id, pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_cliente) VALUES (1, 'Sem tipo definido', 'Sem tipo definido', 1, 1);

CREATE TABLE pessoas_profissoes (
    pessoas_profissoes_id varchar(36) NOT NULL DEFAULT (UUID()),
    pessoas_profissoes_nome varchar(255) NOT NULL UNIQUE,
    pessoas_profissoes_descricao text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    pessoas_profissoes_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    pessoas_profissoes_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    pessoas_profissoes_criado_por varchar(36) NOT NULL,
    pessoas_profissoes_cliente varchar(36) NOT NULL,
    PRIMARY KEY (pessoas_profissoes_id),
    CONSTRAINT fk_pessoas_profissoes_criado_por FOREIGN KEY (pessoas_profissoes_criado_por) REFERENCES usuario(usuario_id),
    CONSTRAINT fk_pessoa_profissao_cliente FOREIGN KEY (pessoas_profissoes_cliente) REFERENCES cliente (cliente_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
s

INSERT INTO pessoas_profissoes (pessoas_profissoes_id, pessoas_profissoes_nome, pessoas_profissoes_descricao,pessoas_profissoes_criado_por, pessoas_profissoes_cliente) VALUES (1, 'Profissão não informada', 'Profissão não informada', 1, 1);

CREATE TABLE pessoas (
    pessoa_id varchar(36) NOT NULL DEFAULT (UUID()),
    pessoa_nome varchar(255) NOT NULL,
    pessoa_aniversario varchar(255) NOT NULL,
    pessoa_email varchar(255) NOT NULL UNIQUE,
    pessoa_telefone varchar(255) DEFAULT NULL,
    pessoa_endereco text DEFAULT NULL,
    pessoa_bairro text,
    pessoa_municipio varchar(255) NOT NULL,
    pessoa_estado varchar(255) NOT NULL,
    pessoa_cep varchar(255) DEFAULT NULL,
    pessoa_sexo varchar(255) DEFAULT NULL,
    pessoa_facebook varchar(255) DEFAULT NULL,
    pessoa_instagram varchar(255) DEFAULT NULL,
    pessoa_x varchar(255) DEFAULT NULL,
    pessoa_informacoes text DEFAULT NULL,
    pessoa_profissao varchar(36) NOT NULL,
    pessoa_cargo varchar(255) DEFAULT NULL,
    pessoa_tipo varchar(36) NOT NULL,
    pessoa_orgao varchar(36) NOT NULL,
    pessoa_foto text DEFAULT NULL,
    pessoa_criada_em timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    pessoa_atualizada_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    pessoa_criada_por varchar(36) NOT NULL,
    pessoa_cliente varchar(36) NOT NULL,
    PRIMARY KEY (pessoa_id),
    CONSTRAINT fk_pessoa_criada_por FOREIGN KEY (pessoa_criada_por) REFERENCES usuario(usuario_id),
    CONSTRAINT fk_pessoa_tipo FOREIGN KEY (pessoa_tipo) REFERENCES pessoas_tipos(pessoa_tipo_id),
    CONSTRAINT fk_pessoa_profissao FOREIGN KEY (pessoa_profissao) REFERENCES pessoas_profissoes(pessoas_profissoes_id),
    CONSTRAINT fk_pessoa_orgao FOREIGN KEY (pessoa_orgao) REFERENCES orgaos(orgao_id),
    CONSTRAINT fk_pessoa_cliente FOREIGN KEY (pessoa_cliente) REFERENCES cliente(cliente_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;


CREATE VIEW view_usuarios AS SELECT * FROM usuario INNER JOIN cliente ON usuario.usuario_cliente = cliente.cliente_id;
CREATE VIEW view_orgaos_tipos AS SELECT orgaos_tipos.*, usuario.usuario_nome, cliente.cliente_nome FROM orgaos_tipos INNER JOIN usuario on orgaos_tipos.orgao_tipo_criado_por = usuario.usuario_id INNER JOIN cliente ON orgaos_tipos.orgao_tipo_cliente = cliente.cliente_id;
CREATE VIEW view_pessoas_tipos AS SELECT pessoas_tipos.*, usuario.usuario_nome, cliente.cliente_nome FROM pessoas_tipos INNER JOIN usuario ON pessoa_tipo_criado_por = usuario.usuario_id INNER JOIN cliente ON pessoas_tipos.pessoa_tipo_cliente = cliente.cliente_id;
CREATE VIEW view_pessoas_profissoes AS SELECT pessoas_profissoes.*, usuario.usuario_nome, cliente.cliente_nome FROM pessoas_profissoes INNER JOIN usuario ON pessoas_profissoes.pessoas_profissoes_criado_por = usuario.usuario_id INNER JOIN cliente ON pessoas_profissoes.pessoas_profissoes_cliente = cliente.cliente_id;CREATE VIEW view_orgaos AS SELECT orgaos.*, orgaos_tipos.orgao_tipo_nome, usuario.usuario_nome, cliente.cliente_nome FROM orgaos INNER JOIN orgaos_tipos ON orgaos.orgao_tipo = orgaos_tipos.orgao_tipo_id INNER JOIN usuario ON orgaos.orgao_criado_por = usuario.usuario_id INNER JOIN cliente ON orgaos.orgao_cliente = cliente_id;
CREATE VIEW view_pessoas AS SELECT pessoas.*, usuario.usuario_nome, cliente.cliente_nome, pessoas_tipos.pessoa_tipo_nome, pessoas_profissoes.pessoas_profissoes_nome, orgaos.orgao_nome FROM pessoas INNER JOIN usuario ON pessoas.pessoa_criada_por = usuario.usuario_id INNER JOIN cliente ON pessoas.pessoa_cliente = cliente.cliente_id INNER JOIN pessoas_tipos ON pessoas.pessoa_tipo = pessoas_tipos.pessoa_tipo_id INNER JOIN pessoas_profissoes ON pessoas.pessoa_profissao = pessoas_profissoes.pessoas_profissoes_id INNER JOIN orgaos ON pessoas.pessoa_orgao = orgaos.orgao_id;
