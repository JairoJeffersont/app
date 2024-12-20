<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\FileUploader;
use GabineteDigital\Models\UsuarioModel;
use GabineteDigital\Middleware\Logger;
use GabineteDigital\Models\ClienteModel;
use PDOException;

/**
 * Classe UsuarioController
 *
 * Controla as operações relacionadas a usuários, incluindo criação, atualização, listagem,
 * busca e exclusão de usuários.
 */
class UsuarioController {

    /**
     * @var UsuarioModel Instância do modelo UsuarioModel para interagir com os dados.
     */
    private $usuarioModel;

    /**
     * @var ClienteModel Instância do modelo UsuarioModel para interagir com os dados.
     */
    private $clienteModel;

    /**
     * @var Logger Instância do Logger para registrar erros.
     */
    private $logger;

    /**
     * @var FileUploader Instância do FileUploader para upload de arquivos.
     */
    private $fileUploader;

    /**
     * @var PastaFotos Pasta padrão de fotos dos usuários.
     */
    private $pasta_foto;


    /**
     * Construtor do UsuarioController.
     *
     * Inicializa as instâncias do modelo UsuarioModel e do Logger para gerenciamento de logs.
     */
    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
        $this->clienteModel = new ClienteModel();
        $this->logger = new Logger();
        $this->fileUploader = new FileUploader();
        $this->pasta_foto = 'public/arquivos/fotos_usuarios';
    }

    /**
     * Método para criar um novo usuário.
     *
     * @param array $dados Associativo com os dados do usuário a serem inseridos. Campos obrigatórios: 
     *                     usuario_nome, usuario_email, usuario_telefone, usuario_senha, usuario_nivel, 
     *                     usuario_ativo, usuario_aniversario, usuario_cliente, $_FILES['foto'].
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function criarUsuario($dados) {
        $camposObrigatorios = ['usuario_nome', 'usuario_email', 'usuario_telefone', 'usuario_senha', 'usuario_nivel', 'usuario_ativo', 'usuario_aniversario', 'usuario_cliente'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        if (!filter_var($dados['usuario_email'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email', 'message' => 'Email inválido.'];
        }


        $usuariosCount = count($this->usuarioModel->buscar('usuario_cliente', $dados['usuario_cliente']));
        $clienteBusca = $this->clienteModel->buscar('cliente_id', $dados['usuario_cliente']);
        $clienteAssinatura = $clienteBusca[0]['cliente_assinaturas'] ?? 1;

        if ($usuariosCount >= $clienteAssinatura) {
            return ['status' => 'forbidden', 'message' => "Não existem mais assinaturas disponíveis. Contate o gestor do sistema"];
        }

        if (!empty($dados['foto']['tmp_name'])) {
            $uploadResult = $this->fileUploader->uploadFile($this->pasta_foto, $dados['foto'], ['jpg', 'jpeg', 'png'], 2);

            if ($uploadResult['status'] !== 'success') {
                return $uploadResult;
            }

            $dados['usuario_foto'] = $uploadResult['file_path'];
        }

        try {
            $this->usuarioModel->criar($dados);
            return ['status' => 'success', 'message' => 'Usuário inserido com sucesso.'];
        } catch (PDOException $e) {
            if (!empty($dados['foto']['tmp_name'])) {
                $this->fileUploader->deleteFile($dados['usuario_foto'] ?? null);
            }

            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O e-mail já está cadastrado.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para atualizar os dados de um usuário existente.
     *
     * @param string $usuario_id ID do usuário a ser atualizado.
     * @param array $dados Associativo com os dados atualizados do usuário. $_FILES['foto']
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function atualizarUsuario($usuario_id, $dados) {

        $camposObrigatorios = ['usuario_nome', 'usuario_email', 'usuario_telefone', 'usuario_senha', 'usuario_nivel', 'usuario_ativo', 'usuario_aniversario', 'usuario_cliente'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        if (!filter_var($dados['usuario_email'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email', 'message' => 'Email inválido.'];
        }

        $usuario = $this->buscarUsuario('usuario_id', $usuario_id);

        if ($usuario['status'] == 'not_found') {
            return $usuario;
        }

        if (!empty($dados['foto']['tmp_name'])) {
            $uploadResult = $this->fileUploader->uploadFile($this->pasta_foto, $dados['foto'], ['jpg', 'jpeg', 'png'], 2);

            if ($uploadResult['status'] !== 'success') {
                return $uploadResult;
            }

            if (!empty($usuario['dados'][0]['usuario_foto'])) {
                $this->fileUploader->deleteFile($usuario['dados'][0]['usuario_foto']);
            }

            $dados['usuario_foto'] = $uploadResult['file_path'];
        } else {
            $dados['usuario_foto'] = $usuario['dados'][0]['usuario_foto'] ?? null;
        }

        try {
            $this->usuarioModel->atualizar($usuario_id, $dados);
            return ['status' => 'success', 'message' => 'Usuário atualizado com sucesso.'];
        } catch (PDOException $e) {

            if (!empty($dados['foto']['tmp_name'])) {
                $this->fileUploader->deleteFile($dados['usuario_foto'] ?? null);
            }

            $erro_id = uniqid();
            $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para listar todos os usuários registrados.
     *
     * @return array Retorna um array com o status da operação, mensagem e lista de usuários.
     */
    public function listarUsuarios($cliente) {
        try {
            $usuarios = $this->usuarioModel->listar($cliente);

            if (empty($usuarios)) {
                return ['status' => 'empty', 'message' => 'Nenhum usuário registrado'];
            }

            return ['status' => 'success', 'message' => count($usuarios) . ' usuário(s) encontrado(s)', 'dados' => $usuarios];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para buscar um usuário específico baseado em uma coluna e valor fornecido.
     *
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna para busca.
     * @return array Retorna um array com o status da operação, mensagem e dados do usuário ou mensagem de usuário não encontrado.
     */
    public function buscarUsuario($coluna, $valor) {
        $colunasPermitidas = ['usuario_id', 'usuario_email'];

        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request', 'message' => 'Coluna inválida. Apenas usuario_id e usuario_email são permitidos.'];
        }

        try {
            $usuario = $this->usuarioModel->buscar($coluna, $valor);
            if ($usuario) {
                return ['status' => 'success', 'dados' => $usuario];
            } else {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para apagar um usuário.
     *
     * @param string $usuario_id ID do usuário a ser apagado.
     * @return array Retorna um array com o status da operação e mensagem de sucesso ou erro.
     */
    public function apagarUsuario($usuario_id) {
        try {
            $usuario = $this->buscarUsuario('usuario_id', $usuario_id);

            if ($usuario['status'] == 'not_found') {
                return $usuario;
            }

            if (isset($usuario['dados'][0]['usuario_foto'])) {
                $this->fileUploader->deleteFile($usuario['dados'][0]['usuario_foto']);
            }

            $this->usuarioModel->apagar($usuario_id);
            return ['status' => 'success', 'message' => 'Usuário apagado com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
