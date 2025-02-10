
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
    cliente_deputado_id varchar(36) NOT NULL DEFAULT (UUID()) UNIQUE,
    cliente_deputado_nome varchar(255) NOT NULL,
    cliente_deputado_estado varchar(2) NOT NULL,
    cliente_deputado_tipo varchar(255) NOT NULL,
    cliente_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    cliente_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (cliente_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;


INSERT INTO cliente (cliente_id, cliente_nome, cliente_email, cliente_telefone, cliente_ativo, cliente_endereco, cliente_cep, cliente_cpf, cliente_assinaturas, cliente_deputado_id, cliente_deputado_nome, cliente_deputado_estado, cliente_deputado_tipo) 
VALUES ('1', 'CLIENTE SISTEMA', 'email@email.com', '000000', 1, 'Sem endereço', '00000000', '00000000000', 2, 0, 'deputado', 'DF', 'Deputado Federal');

CREATE TABLE usuario (
    usuario_id varchar(36) NOT NULL DEFAULT (UUID()),
    usuario_nome varchar(255) NOT NULL,
    usuario_email varchar(255) NOT NULL UNIQUE,
    usuario_telefone varchar(20) NOT NULL,
    usuario_senha varchar(255) NOT NULL,
    usuario_nivel int NOT NULL,
    usuario_ativo tinyint(1) NOT NULL,
    usuario_aniversario varchar(255) NOT NULL,
    usuario_foto varchar(255) DEFAULT NULL,
    usuario_cliente varchar(36) NOT NULL,
    usuario_token varchar(255) DEFAULT NULL,
    usuario_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (usuario_id),
    CONSTRAINT fk_cliente FOREIGN KEY (usuario_cliente) REFERENCES cliente(cliente_id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;


INSERT INTO usuario (usuario_id, usuario_nome, usuario_email, usuario_telefone, usuario_senha, usuario_nivel, usuario_ativo, usuario_aniversario, usuario_cliente) 
VALUES ('1', 'USUÁRIO SISTEMA', 'email@email.com', '000000', 'sd9fasdfasd9fasd89fsad9f8', 1, 1, '01/01', '1');

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
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_cliente) VALUES (2, 'Ministério', 'Órgão responsável por uma área específica do governo federal', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_cliente) VALUES (3, 'Autarquia Federal', 'Órgão com autonomia administrativa e financeira', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_cliente) VALUES (4, 'Empresa Pública Federal', 'Órgão que realiza atividades econômicas como públicos, correios, eletrobras..', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_cliente) VALUES (5, 'Universidade Federal', 'Instituição de ensino superior federal', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_cliente) VALUES (6, 'Polícia Federal', 'Órgão responsável pela segurança e investigação em âmbito federal', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_cliente) VALUES (7, 'Governo Estadual', 'Órgão executivo estadual responsável pela administração de um estado', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_cliente) VALUES (8, 'Assembleia Legislativa Estadual', 'Órgão legislativo estadual responsável pela criação de leis estaduais', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_cliente) VALUES (9, 'Prefeitura', 'Órgão executivo municipal responsável pela administração local', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_cliente) VALUES (10, 'Câmara Municipal', 'Órgão legislativo municipal responsável pela criação de leis municipais', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_cliente) VALUES (11, 'Entidade Civil', 'Organização sem fins lucrativos que atua em prol de causas sociais', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_cliente) VALUES (12, 'Escola estadual', 'Escolas estaduais', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_cliente) VALUES (13, 'Escola municipal', 'Escolas municipais', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_cliente) VALUES (14, 'Escola Federal', 'Escolas federais', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_cliente) VALUES (15, 'Partido Político', 'Partido Político', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_cliente) VALUES (16, 'Câmara Federal', 'Câmara Federal', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_cliente) VALUES (17, 'Senado Federal', 'Senado Federal', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_cliente) VALUES (18, 'Presidência da Repúlica', 'Presidência da Repúlica', 1, 1);
INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_cliente) VALUES (19, 'Veículo de comunicação', 'Jornais, revistas, sites de notícias, emissoras de rádio e TV', 1, 1);


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
INSERT INTO pessoas_tipos (pessoa_tipo_id, pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_cliente) VALUES (2, 'Familiares', 'Familiares do deputado', 1, 1);
INSERT INTO pessoas_tipos (pessoa_tipo_id, pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_cliente) VALUES (3, 'Empresários', 'Donos de empresa', 1, 1);
INSERT INTO pessoas_tipos (pessoa_tipo_id, pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_cliente) VALUES (4, 'Eleitores', 'Eleitores em geral', 1, 1);
INSERT INTO pessoas_tipos (pessoa_tipo_id, pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_cliente) VALUES (5, 'Imprensa', 'Jornalistas, diretores de jornais, assessoria', 1, 1);
INSERT INTO pessoas_tipos (pessoa_tipo_id, pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_cliente) VALUES (6, 'Site', 'Pessoas registradas no site', 1, 1);
INSERT INTO pessoas_tipos (pessoa_tipo_id, pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_cliente) VALUES (7, 'Amigos', 'Amigos pessoais do deputado', 1, 1);
INSERT INTO pessoas_tipos (pessoa_tipo_id, pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_cliente) VALUES (8, 'Autoridades', 'Autoridades públicas', 1, 1);

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

INSERT INTO pessoas_profissoes (pessoas_profissoes_id, pessoas_profissoes_nome, pessoas_profissoes_descricao, pessoas_profissoes_criado_por, pessoas_profissoes_cliente) 
VALUES 
(2, 'Médico', 'Profissional responsável por diagnosticar e tratar doenças', 1, 1),
(3, 'Engenheiro de Software', 'Profissional especializado em desenvolvimento e manutenção de sistemas de software', 1, 1),
(4, 'Advogado', 'Profissional que oferece consultoria e representação legal', 1, 1),
(5, 'Professor', 'Profissional responsável por ministrar aulas e orientar estudantes', 1, 1),
(6, 'Enfermeiro', 'Profissional da saúde que cuida e monitoriza pacientes', 1, 1),
(7, 'Arquiteto', 'Profissional que projeta e planeja edifícios e espaços urbanos', 1, 1),
(8, 'Contador', 'Profissional que gerencia contas e prepara relatórios financeiros', 1, 1),
(9, 'Designer Gráfico', 'Profissional especializado em criação visual e design', 1, 1),
(10, 'Jornalista', 'Profissional que coleta, escreve e distribui notícias', 1, 1),
(11, 'Chef de Cozinha', 'Profissional que planeja, dirige e prepara refeições em restaurantes', 1, 1),
(12, 'Psicólogo', 'Profissional que realiza avaliações psicológicas e oferece terapia', 1, 1),
(13, 'Fisioterapeuta', 'Profissional que ajuda na reabilitação física de pacientes', 1, 1),
(14, 'Veterinário', 'Profissional responsável pelo cuidado e tratamento de animais', 1, 1),
(15, 'Fotógrafo', 'Profissional que captura e edita imagens fotográficas', 1, 1),
(16, 'Tradutor', 'Profissional que converte textos de um idioma para outro', 1, 1),
(17, 'Administrador', 'Profissional que gerencia operações e processos em uma organização', 1, 1),
(18, 'Biólogo', 'Profissional que estuda organismos vivos e seus ecossistemas', 1, 1),
(19, 'Economista', 'Profissional que analisa dados econômicos e desenvolve modelos de previsão', 1, 1),
(20, 'Programador', 'Profissional que escreve e testa códigos de software', 1, 1),
(21, 'Cientista de Dados', 'Profissional que analisa e interpreta grandes volumes de dados', 1, 1),
(22, 'Analista de Marketing', 'Profissional que desenvolve e implementa estratégias de marketing', 1, 1),
(23, 'Engenheiro Civil', 'Profissional que projeta e constrói infraestrutura como pontes e edifícios', 1, 1),
(24, 'Cozinheiro', 'Profissional que prepara e cozinha alimentos em ambientes como restaurantes', 1, 1),
(25, 'Social Media', 'Profissional que gerencia e cria conteúdo para redes sociais', 1, 1),
(26, 'Auditor', 'Profissional que examina e avalia registros financeiros e operacionais', 1, 1),
(27, 'Técnico em Informática', 'Profissional que presta suporte técnico e manutenção de hardware e software', 1, 1),
(28, 'Líder de Projeto', 'Profissional que coordena e supervisiona projetos para garantir a conclusão bem-sucedida', 1, 1),
(29, 'Químico', 'Profissional que realiza pesquisas e experimentos químicos', 1, 1),
(30, 'Gerente de Recursos Humanos', 'Profissional responsável pela gestão de pessoal e políticas de recursos humanos', 1, 1),
(31, 'Engenheiro Eletricista', 'Profissional que projeta e implementa sistemas elétricos e eletrônicos', 1, 1),
(32, 'Designer de Moda', 'Profissional que cria e desenvolve roupas e acessórios', 1, 1),
(33, 'Engenheiro Mecânico', 'Profissional que projeta e desenvolve sistemas mecânicos e máquinas', 1, 1),
(34, 'Web Designer', 'Profissional que cria e mantém layouts e interfaces de sites', 1, 1),
(35, 'Geólogo', 'Profissional que estuda a composição e estrutura da Terra', 1, 1),
(36, 'Segurança da Informação', 'Profissional que protege sistemas e dados contra ameaças e ataques', 1, 1),
(37, 'Consultor Financeiro', 'Profissional que oferece orientação sobre gestão e planejamento financeiro', 1, 1),
(38, 'Artista Plástico', 'Profissional que cria obras de arte em diversos meios e materiais', 1, 1),
(39, 'Logístico', 'Profissional que coordena e gerencia operações de logística e cadeia de suprimentos', 1, 1),
(40, 'Fonoaudiólogo', 'Profissional que avalia e trata problemas de comunicação e linguagem', 1, 1),
(41, 'Corretor de Imóveis', 'Profissional que facilita a compra, venda e aluguel de propriedades', 1, 1);

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


CREATE TABLE documentos_tipos (
    documento_tipo_id varchar(36) NOT NULL DEFAULT (UUID()),
    documento_tipo_nome varchar(255) NOT NULL UNIQUE,
    documento_tipo_descricao text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    documento_tipo_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    documento_tipo_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    documento_tipo_criado_por varchar(36) NOT NULL,
    documento_tipo_cliente varchar(36) NOT NULL,
    PRIMARY KEY (documento_tipo_id),
    CONSTRAINT fk_documento_tipo_criado_por FOREIGN KEY (documento_tipo_criado_por) REFERENCES usuario (usuario_id),
    CONSTRAINT fk_documento_tipo_cliente FOREIGN KEY (documento_tipo_cliente) REFERENCES cliente (cliente_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;


INSERT INTO documentos_tipos (documento_tipo_id, documento_tipo_nome, documento_tipo_descricao, documento_tipo_criado_por, documento_tipo_cliente) VALUES (1, 'Sem tipo definido', 'Sem tipo definido', 1, 1);
INSERT INTO documentos_tipos (documento_tipo_id, documento_tipo_nome, documento_tipo_descricao, documento_tipo_criado_por, documento_tipo_cliente) 
VALUES (2, 'Ofício', 'Documento utilizado para comunicações formais entre órgãos ou instituições', 1, 1);

INSERT INTO documentos_tipos (documento_tipo_id, documento_tipo_nome, documento_tipo_descricao, documento_tipo_criado_por, documento_tipo_cliente) 
VALUES (3, 'Requerimento', 'Documento formal solicitando algo de uma instituição ou órgão', 1, 1);

INSERT INTO documentos_tipos (documento_tipo_id, documento_tipo_nome, documento_tipo_descricao, documento_tipo_criado_por, documento_tipo_cliente) 
VALUES (4, 'Carta', 'Documento informal ou formal que transmite informações ou solicitações', 1, 1);

INSERT INTO documentos_tipos (documento_tipo_id, documento_tipo_nome, documento_tipo_descricao, documento_tipo_criado_por, documento_tipo_cliente) 
VALUES (5, 'Memorando', 'Documento utilizado para comunicação interna entre setores de uma organização', 1, 1);

INSERT INTO documentos_tipos (documento_tipo_id, documento_tipo_nome, documento_tipo_descricao, documento_tipo_criado_por, documento_tipo_cliente) 
VALUES (6, 'Ata', 'Documento que registra os acontecimentos e decisões de uma reunião ou evento', 1, 1);

INSERT INTO documentos_tipos (documento_tipo_id, documento_tipo_nome, documento_tipo_descricao, documento_tipo_criado_por, documento_tipo_cliente) 
VALUES (7, 'Termo de Compromisso', 'Documento que formaliza um compromisso ou acordo entre as partes', 1, 1);


CREATE TABLE documentos(
    documento_id varchar(36) NOT NULL DEFAULT (UUID()),
    documento_titulo VARCHAR(255) NOT NULL UNIQUE,
    documento_resumo text,
    documento_arquivo text,
    documento_ano int,
    documento_tipo varchar(36) NOT NULL,
    documento_orgao varchar(36) NOT NULL,
    documento_criado_por varchar(36) NOT NULL,
    documento_cliente varchar(36) NOT NULL,
    documento_criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    documento_atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY(documento_id),
    CONSTRAINT fk_documento_criado_por FOREIGN KEY (documento_criado_por) REFERENCES usuario(usuario_id),
    CONSTRAINT fk_documento_orgao FOREIGN KEY (documento_orgao) REFERENCES orgaos(orgao_id),
    CONSTRAINT fk_documento_tipo FOREIGN KEY (documento_tipo) REFERENCES documentos_tipos(documento_tipo_id),
    CONSTRAINT fk_documento_cliente FOREIGN KEY (documento_cliente) REFERENCES cliente(cliente_id)
)ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;



CREATE TABLE postagem_status(
    postagem_status_id varchar(36) NOT NULL DEFAULT (UUID()),
    postagem_status_nome VARCHAR(255) NOT NULL UNIQUE,
    postagem_status_descricao TEXT NULL,
    postagem_status_criado_por varchar(36) NOT NULL,
    postagem_status_cliente varchar(36) NOT NULL,
    postagem_status_criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    postagem_status_atualizada_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY(postagem_status_id),
    CONSTRAINT fk_postagem_status_criado_por FOREIGN KEY (postagem_status_criado_por) REFERENCES usuario(usuario_id),
    CONSTRAINT fk_postagem_status_cliente FOREIGN KEY (postagem_status_cliente) REFERENCES cliente(cliente_id)
)ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO postagem_status (postagem_status_id, postagem_status_nome, postagem_status_descricao,postagem_status_criado_por, postagem_status_cliente) VALUES (1, 'Iniciada', 'Iniciada uma postagem', 1, 1);
INSERT INTO postagem_status (postagem_status_id, postagem_status_nome, postagem_status_descricao,postagem_status_criado_por, postagem_status_cliente) VALUES (2, 'Em produção', 'Postagem em fase de produção', 1,1);
INSERT INTO postagem_status (postagem_status_id, postagem_status_nome, postagem_status_descricao,postagem_status_criado_por, postagem_status_cliente) VALUES (3, 'Em aprovação', 'Postagem em fase de aprovação', 1,1);
INSERT INTO postagem_status (postagem_status_id, postagem_status_nome, postagem_status_descricao,postagem_status_criado_por, postagem_status_cliente) VALUES (4, 'Aprovada', 'Postagem aprovada', 1,1);
INSERT INTO postagem_status (postagem_status_id, postagem_status_nome, postagem_status_descricao,postagem_status_criado_por, postagem_status_cliente) VALUES (5, 'Postada', 'Postagem postada', 1,1);

CREATE TABLE postagens(
    postagem_id varchar(36) NOT NULL DEFAULT (UUID()),
    postagem_titulo VARCHAR(255) NOT NULL UNIQUE,
    postagem_data VARCHAR(255),
    postagem_pasta TEXT, 
    postagem_informacoes TEXT,
    postagem_midias TEXT,  
    postagem_status varchar(36) NOT NULL,
    postagem_criada_por varchar(36) NOT NULL,
    postagem_cliente varchar(36) NOT NULL,
    postagem_criada_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    postagem_atualizada_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY(postagem_id),
    CONSTRAINT fk_postagem_criada_por FOREIGN KEY (postagem_criada_por) REFERENCES usuario(usuario_id),
    CONSTRAINT fk_postagem_status FOREIGN KEY (postagem_status) REFERENCES postagem_status(postagem_status_id),
    CONSTRAINT fk_postagem_cliente FOREIGN KEY (postagem_cliente) REFERENCES cliente(cliente_id)

)ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE clipping_tipos (
    clipping_tipo_id varchar(36) NOT NULL DEFAULT (UUID()),
    clipping_tipo_nome varchar(255) NOT NULL UNIQUE,
    clipping_tipo_descricao text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    clipping_tipo_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    clipping_tipo_criado_por varchar(36) NOT NULL,
    clipping_tipo_cliente varchar(36) NOT NULL,
    PRIMARY KEY (clipping_tipo_id),
    CONSTRAINT fk_clipping_tipo_criado_por FOREIGN KEY (clipping_tipo_criado_por) REFERENCES usuario(usuario_id),
    CONSTRAINT fk_clipping_tipo_cliente FOREIGN KEY (clipping_tipo_cliente) REFERENCES cliente(cliente_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO clipping_tipos (clipping_tipo_id, clipping_tipo_nome, clipping_tipo_descricao, clipping_tipo_criado_por, clipping_tipo_cliente) VALUES (1, 'Sem tipo definido', 'Sem tipo definido', 1,1);
INSERT INTO clipping_tipos (clipping_tipo_id, clipping_tipo_nome, clipping_tipo_descricao, clipping_tipo_criado_por, clipping_tipo_cliente) VALUES (2, 'Notícia Jornalística', 'Matéria Jornalistica de site, revista, blog...', 1, 1);
INSERT INTO clipping_tipos (clipping_tipo_id, clipping_tipo_nome, clipping_tipo_descricao, clipping_tipo_criado_por, clipping_tipo_cliente) VALUES (3, 'Post de rede social', 'Post de instagram, facebook....', 1, 1);

CREATE TABLE clipping (
    clipping_id varchar(36) NOT NULL DEFAULT (UUID()),
    clipping_resumo TEXT NOT NULL,
    clipping_titulo TEXT NOT NULL,
    clipping_link VARCHAR(255) NOT NULL UNIQUE,
    clipping_orgao varchar(36) NOT NULL,
    clipping_arquivo VARCHAR(255),
    clipping_data date NOT NULL,
    clipping_tipo varchar(36) NOT NULL,
    clipping_criado_por varchar(36) NOT NULL,
    clipping_cliente varchar(36) NOT NULL,
    clipping_criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    clipping_atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (clipping_id),
    CONSTRAINT fk_clipping_criado_por FOREIGN KEY (clipping_criado_por) REFERENCES usuario(usuario_id),
    CONSTRAINT fk_clipping_orgao FOREIGN KEY (clipping_orgao) REFERENCES orgaos(orgao_id),
    CONSTRAINT fk_clipping_tipo FOREIGN KEY (clipping_tipo) REFERENCES clipping_tipos(clipping_tipo_id),
    CONSTRAINT fk_clipping_cliente FOREIGN KEY (clipping_cliente) REFERENCES cliente(cliente_id)

)ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;


CREATE TABLE emendas_status (
    emendas_status_id varchar(36) NOT NULL DEFAULT (UUID()),
    emendas_status_nome varchar(255) NOT NULL UNIQUE,
    emendas_status_descricao TEXT NOT NULL,
    emendas_status_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    emendas_status_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    emendas_status_criado_por varchar(36) NOT NULL,
    emendas_status_cliente varchar(36) NOT NULL,
    PRIMARY KEY (emendas_status_id),
    CONSTRAINT fk_emendas_status_criado_por FOREIGN KEY (emendas_status_criado_por) REFERENCES usuario (usuario_id),
    CONSTRAINT fk_emendas_status_cliente FOREIGN KEY (emendas_status_cliente) REFERENCES cliente (cliente_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO emendas_status (emendas_status_id, emendas_status_nome, emendas_status_descricao, emendas_status_criado_por, emendas_status_cliente)
VALUES
    (1, 'Criada', 'A emenda foi criada no sistema.', '1', '1'),
    (2, 'Em Análise', 'A emenda foi recebida e está sendo analisada pelos responsáveis.', '1', '1'),
    (3, 'Aprovada', 'A emenda foi aprovada e aguarda os próximos trâmites.', '1', '1'),
    (4, 'Rejeitada', 'A emenda foi rejeitada por não atender aos critérios estabelecidos.', '1', '1'),
    (5, 'Em Execução', 'A emenda foi aprovada e está em fase de execução.', '1', '1'),
    (6, 'Concluída', 'A emenda foi totalmente executada e finalizada.', '1', '1'),
    (7, 'Pendente de Documentação', 'A emenda aguarda a entrega de documentos para seguir para análise.', '1', '1'),
    (8, 'Cancelada', 'A emenda foi cancelada por solicitação do proponente.', '1', '1'),
    (9, 'Aguardando Liberação', 'A emenda foi aprovada e está aguardando a liberação de recursos.', '1', '1'),
    (10, 'Revisão Necessária', 'A emenda precisa de ajustes antes de seguir para aprovação.', '1', '1'),
    (11, 'Suspensa', 'A execução da emenda foi temporariamente suspensa.', '1', '1');


CREATE TABLE emendas_objetivos (
    emendas_objetivos_id varchar(36) NOT NULL DEFAULT (UUID()),
    emendas_objetivos_nome varchar(255) NOT NULL UNIQUE,
    emendas_objetivos_descricao TEXT NOT NULL,
    emendas_objetivos_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    emendas_objetivos_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    emendas_objetivos_criado_por varchar(36) NOT NULL,
    emendas_objetivos_cliente varchar(36) NOT NULL,
    PRIMARY KEY (emendas_objetivos_id),
    CONSTRAINT fk_emendas_objetivos_criado_por FOREIGN KEY (emendas_objetivos_criado_por) REFERENCES usuario (usuario_id),
    CONSTRAINT fk_emendas_objetivos_status_cliente FOREIGN KEY (emendas_objetivos_cliente) REFERENCES cliente (cliente_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO emendas_objetivos (emendas_objetivos_id, emendas_objetivos_nome, emendas_objetivos_descricao, emendas_objetivos_criado_por, emendas_objetivos_cliente)
VALUES
    (1, 'Sem objetivo definido', 'Sem objetivo definido.', '1', '1'),
    (2, 'Transferência especial', 'Emenda PIX.', '1', '1'),
    (3, 'Saúde', 'Destinação de recursos para hospitais, unidades de saúde e aquisição de equipamentos médicos.', '1', '1'),
    (4, 'Educação', 'Investimentos em escolas, creches, universidades e formação de professores.', '1', '1'),
    (5, 'Infraestrutura', 'Obras de pavimentação, saneamento básico e construção de equipamentos públicos.', '1', '1'),
    (6, 'Segurança Pública', 'Apoio a projetos para melhoria das forças de segurança, aquisição de viaturas e equipamentos.', '1', '1'),
    (7, 'Cultura', 'Fomento a atividades culturais, reforma de teatros, bibliotecas e museus.', '1', '1'),
    (8, 'Esporte', 'Incentivo ao esporte e lazer, construção de quadras e centros esportivos.', '1', '1'),
    (9, 'Assistência Social', 'Apoio a programas sociais voltados para populações vulneráveis.', '1', '1'),
    (10, 'Agricultura', 'Fomento à agricultura familiar, assistência técnica e compra de equipamentos.', '1', '1'),
    (11, 'Meio Ambiente', 'Projetos de sustentabilidade, preservação ambiental e energias renováveis.', '1', '1'),
    (12, 'Turismo', 'Apoio a iniciativas de turismo sustentável e infraestrutura turística.', '1', '1'),
    (13, 'Ciência e Tecnologia', 'Fomento à inovação, pesquisa e desenvolvimento tecnológico.', '1', '1'),
    (14, 'Transporte', 'Melhoria da mobilidade urbana e transporte público.', '1', '1'),
    (15, 'Habitação', 'Investimentos em programas habitacionais e urbanização de áreas carentes.', '1', '1');


CREATE TABLE emendas (
    emenda_id varchar(36) NOT NULL DEFAULT (UUID()),
    emenda_numero INT NOT NULL,
    emenda_ano INT NOT NULL,
    emenda_valor DECIMAL(12,2),
    emenda_descricao TEXT NOT NULL,
    emenda_status VARCHAR(36) NOT NULL,
    emenda_orgao VARCHAR(36) NOT NULL,
    emenda_municipio VARCHAR(50) NOT NULL,
    emenda_estado VARCHAR(3) NOT NULL,
    emenda_objetivo VARCHAR(36) NOT NULL,
    emenda_informacoes TEXT NULL,
    emenda_tipo VARCHAR(12) NOT NULL,
    emenda_cliente VARCHAR(36) NOT NULL,
    emenda_criado_por VARCHAR(36) NOT NULL,
    emenda_criada_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    emenda_atualizada_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (emenda_id),
    CONSTRAINT fk_emenda_status FOREIGN KEY (emenda_status) REFERENCES emendas_status (emendas_status_id),
    CONSTRAINT fk_emendas_objetivos FOREIGN KEY (emenda_objetivo) REFERENCES emendas_objetivos (emendas_objetivos_id),
    CONSTRAINT fk_emenda_criado_por FOREIGN KEY (emenda_criado_por) REFERENCES usuario (usuario_id),
    CONSTRAINT fk_emenda_cliente FOREIGN KEY (emenda_cliente) REFERENCES cliente (cliente_id),
    CONSTRAINT fk_emenda_orgao FOREIGN KEY (emenda_orgao) REFERENCES orgaos (orgao_id)
)ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;


CREATE TABLE proposicoes (
    proposicao_id INT NOT NULL,
    proposicao_numero INT NOT NULL,
    proposicao_titulo VARCHAR(255) NOT NULL,
    proposicao_ano INT NOT NULL,
    proposicao_tipo VARCHAR(10) NOT NULL,
    proposicao_ementa TEXT NOT NULL,
    proposicao_apresentacao DATETIME NULL DEFAULT NULL,
    proposicao_arquivada TINYINT(1) NOT NULL DEFAULT 0,
    proposicao_aprovada TINYINT(1) NOT NULL DEFAULT 0,
    proposicao_principal INT DEFAULT NULL,
    PRIMARY KEY (proposicao_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE proposicoes_autores (
    proposicao_id INT NOT NULL,
    proposicao_autor_id INT NOT NULL,
    proposicao_autor_nome TEXT NOT NULL,
    proposicao_autor_partido VARCHAR(255) DEFAULT NULL,
    proposicao_autor_estado VARCHAR(255) DEFAULT NULL,
    proposicao_autor_proponente INT NOT NULL,
    proposicao_autor_assinatura INT NOT NULL,
    proposicao_autor_ano INT NOT NULL,
    INDEX (proposicao_id, proposicao_autor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO pessoas_profissoes (pessoas_profissoes_id, pessoas_profissoes_nome, pessoas_profissoes_descricao,pessoas_profissoes_criado_por, pessoas_profissoes_cliente) VALUES (1, 'Profissão não informada', 'Profissão não informada', 1, 1);

CREATE VIEW view_usuarios AS SELECT * FROM usuario INNER JOIN cliente ON usuario.usuario_cliente = cliente.cliente_id;
CREATE VIEW view_orgaos_tipos AS SELECT orgaos_tipos.*, usuario.usuario_nome, cliente.cliente_nome FROM orgaos_tipos INNER JOIN usuario on orgaos_tipos.orgao_tipo_criado_por = usuario.usuario_id INNER JOIN cliente ON orgaos_tipos.orgao_tipo_cliente = cliente.cliente_id;
CREATE VIEW view_pessoas_tipos AS SELECT pessoas_tipos.*, usuario.usuario_nome, cliente.cliente_nome FROM pessoas_tipos INNER JOIN usuario ON pessoa_tipo_criado_por = usuario.usuario_id INNER JOIN cliente ON pessoas_tipos.pessoa_tipo_cliente = cliente.cliente_id;
CREATE VIEW view_pessoas_profissoes AS SELECT pessoas_profissoes.*, usuario.usuario_nome, cliente.cliente_nome FROM pessoas_profissoes INNER JOIN usuario ON pessoas_profissoes.pessoas_profissoes_criado_por = usuario.usuario_id INNER JOIN cliente ON pessoas_profissoes.pessoas_profissoes_cliente = cliente.cliente_id; 
CREATE VIEW view_orgaos AS SELECT orgaos.*, orgaos_tipos.orgao_tipo_nome, usuario.usuario_nome, cliente.cliente_nome FROM orgaos INNER JOIN orgaos_tipos ON orgaos.orgao_tipo = orgaos_tipos.orgao_tipo_id INNER JOIN usuario ON orgaos.orgao_criado_por = usuario.usuario_id INNER JOIN cliente ON orgaos.orgao_cliente = cliente_id;
CREATE VIEW view_pessoas AS SELECT pessoas.*, usuario.usuario_nome, cliente.cliente_nome, pessoas_tipos.pessoa_tipo_nome, pessoas_profissoes.pessoas_profissoes_nome, orgaos.orgao_nome FROM pessoas INNER JOIN usuario ON pessoas.pessoa_criada_por = usuario.usuario_id INNER JOIN cliente ON pessoas.pessoa_cliente = cliente.cliente_id INNER JOIN pessoas_tipos ON pessoas.pessoa_tipo = pessoas_tipos.pessoa_tipo_id INNER JOIN pessoas_profissoes ON pessoas.pessoa_profissao = pessoas_profissoes.pessoas_profissoes_id INNER JOIN orgaos ON pessoas.pessoa_orgao = orgaos.orgao_id;
CREATE VIEW view_documentos_tipos AS SELECT documentos_tipos.*, usuario.usuario_nome, cliente.cliente_nome FROM documentos_tipos INNER JOIN usuario ON documento_tipo_criado_por = usuario.usuario_id INNER JOIN cliente ON documentos_tipos.documento_tipo_cliente = cliente.cliente_id;
CREATE VIEW view_documentos AS SELECT documentos.*, documentos_tipos.*, orgaos.orgao_nome, orgaos.orgao_id, usuario.usuario_nome FROM documentos INNER JOIN documentos_tipos ON documentos.documento_tipo = documentos_tipos.documento_tipo_id INNER JOIN orgaos ON documentos.documento_orgao = orgaos.orgao_id INNER JOIN usuario ON documentos.documento_criado_por = usuario.usuario_id;
CREATE VIEW view_postagens_status AS SELECT postagem_status.*, usuario.usuario_nome, cliente.cliente_nome FROM postagem_status INNER JOIN usuario ON postagem_status.postagem_status_criado_por = usuario.usuario_id INNER JOIN cliente ON postagem_status.postagem_status_cliente = cliente.cliente_id ORDER BY postagem_status.postagem_status_nome ASC;
CREATE VIEW view_postagens AS SELECT postagens.*, usuario.usuario_nome, postagem_status.postagem_status_id, postagem_status.postagem_status_nome, postagem_status.postagem_status_descricao, cliente.cliente_nome FROM postagens INNER JOIN usuario ON postagens.postagem_criada_por = usuario.usuario_id INNER JOIN postagem_status ON postagens.postagem_status = postagem_status.postagem_status_id INNER JOIN cliente ON postagens.postagem_cliente = cliente.cliente_id; 
CREATE VIEW view_tipo_clipping AS SELECT clipping_tipos.*, usuario.usuario_nome FROM clipping_tipos INNER JOIN usuario ON clipping_tipos.clipping_tipo_criado_por = usuario.usuario_id INNER JOIN cliente ON clipping_tipos.clipping_tipo_cliente = cliente.cliente_id;
CREATE VIEW view_clipping AS SELECT clipping.*, usuario.usuario_nome, orgaos.orgao_nome, cliente.cliente_nome, clipping_tipos.clipping_tipo_nome FROM clipping INNER JOIN clipping_tipos ON clipping.clipping_tipo = clipping_tipos.clipping_tipo_id INNER JOIN orgaos ON clipping.clipping_orgao = orgaos.orgao_id INNER JOIN usuario ON clipping.clipping_criado_por = usuario.usuario_id INNER JOIN cliente ON clipping.clipping_cliente = cliente.cliente_id;
CREATE VIEW view_proposicoes AS SELECT proposicoes.*, proposicoes_autores.proposicao_autor_nome, proposicoes_autores.proposicao_autor_partido, proposicoes_autores.proposicao_autor_estado, proposicoes_autores.proposicao_autor_proponente, proposicoes_autores.proposicao_autor_assinatura FROM proposicoes INNER JOIN proposicoes_autores ON proposicoes.proposicao_id = proposicoes_autores.proposicao_id;
CREATE VIEW view_emendas_status AS SELECT emendas_status.*, usuario.usuario_nome, cliente.cliente_nome FROM emendas_status INNER JOIN usuario ON emendas_status.emendas_status_criado_por = usuario.usuario_id INNER JOIN cliente ON emendas_status.emendas_status_cliente = cliente.cliente_id ORDER BY emendas_status.emendas_status_nome ASC;
CREATE VIEW view_emendas_objetivos AS SELECT emendas_objetivos.*, usuario.usuario_nome, cliente.cliente_nome FROM emendas_objetivos INNER JOIN usuario ON emendas_objetivos.emendas_objetivos_criado_por = usuario.usuario_id INNER JOIN cliente ON emendas_objetivos.emendas_objetivos_cliente = cliente.cliente_id ORDER BY emendas_objetivos.emendas_objetivos_nome ASC;
CREATE VIEW view_emendas AS SELECT emendas.*, emendas_status.emendas_status_nome, emendas_objetivos.emendas_objetivos_nome, orgaos.orgao_nome, usuario.usuario_nome FROM emendas INNER JOIN emendas_status ON emendas.emenda_status = emendas_status.emendas_status_id INNER JOIN emendas_objetivos ON emendas.emenda_objetivo = emendas_objetivos.emendas_objetivos_id INNER JOIN orgaos ON emendas.emenda_orgao = orgaos.orgao_id INNER JOIN usuario ON emendas.emenda_criado_por = usuario.usuario_id;