<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\FileUploader;
use GabineteDigital\Middleware\Logger;
use GabineteDigital\Models\DocumentoModel;
use PDOException;

/**
 * Classe documentoController
 *
 * Controla as operações relacionadas a ofícios, incluindo criação, atualização, listagem,
 * busca e exclusão de registros.
 */
class DocumentoController
{
    /**
     * @var documentoModel Instância do modelo documentoModel para interagir com os dados.
     */
    private $documentoModel;

    /**
     * @var Logger Instância do Logger para registrar erros.
     */
    private $logger;

    /**
     * @var FileUploader Instância do FileUploader para manipulação de arquivos.
     */
    private $fileUploader;

    /**
     * @var string Caminho da pasta onde os arquivos de ofícios serão armazenados.
     */
    private $pasta_foto;

    /**
     * Construtor do documentoController.
     *
     * Inicializa as instâncias necessárias para manipulação de ofícios.
     */
    public function __construct()
    {
        $this->documentoModel = new DocumentoModel();
        $this->logger = new Logger();
        $this->fileUploader = new FileUploader();
        $this->pasta_foto = 'public/arquivos/documentos';
    }

    /**
     * Criar um novo ofício.
     *
     * @param array $dados Dados do ofício a serem inseridos.
     * @return array Resultado da operação.
     */
    public function criarDocumento($dados)
    {
        $camposObrigatorios = ['documento_titulo', 'documento_resumo', 'arquivo', 'documento_ano', 'documento_orgao', 'documento_criado_por', 'documento_cliente'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        if (!empty($dados['arquivo']['tmp_name'])) {
            $uploadResult = $this->fileUploader->uploadFile($this->pasta_foto.'/'.$dados['documento_cliente'], $dados['arquivo'], ['pdf'], 5);

            if ($uploadResult['status'] !== 'success') {
                return $uploadResult;
            }

            $dados['documento_arquivo'] = $uploadResult['file_path'];
        }

        try {
            $this->documentoModel->criar($dados);
            return ['status' => 'success', 'message' => 'Ofício criado com sucesso.'];
        } catch (PDOException $e) {

            if (!empty($dados['arquivo']['tmp_name'])) {
                $this->fileUploader->deleteFile($dados['documento_arquivo'] ?? null);
            }

            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'Esse ofício já está cadastrado.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('documento_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Atualizar os dados de um ofício existente.
     *
     * @param string $documento_id ID do ofício a ser atualizado.
     * @param array $dados Dados atualizados do ofício.
     * @return array Resultado da operação.
     */
    public function atualizarDocumento($documento_id, $dados)
    {
        $camposObrigatorios = ['documento_titulo', 'documento_resumo', 'arquivo', 'documento_ano', 'documento_orgao'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        $documento = $this->buscardocumento('documento_id', $documento_id);

        if ($documento['status'] == 'not_found') {
            return $documento;
        }

        if (!empty($dados['arquivo']['tmp_name'])) {
            $uploadResult = $this->fileUploader->uploadFile($this->pasta_foto.'/'.$dados['documento_cliente'], $dados['arquivo'], ['pdf'], 5);

            if ($uploadResult['status'] !== 'success') {
                return $uploadResult;
            }

            if (!empty($documento['dados'][0]['documento_arquivo'])) {
                $this->fileUploader->deleteFile($documento['dados'][0]['documento_arquivo']);
            }

            $dados['documento_arquivo'] = $uploadResult['file_path'];
        } else {
            $dados['documento_arquivo'] = $documento['dados'][0]['documento_arquivo'] ?? null;
        }

        try {
            $this->documentoModel->atualizar($documento_id, $dados);
            return ['status' => 'success', 'message' => 'Ofício atualizado com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('documento_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Listar ofícios com base no ano, termo de busca e cliente.
     *
     * @param int $ano Ano para filtrar os ofícios.
     * @param string|null $busca Termo de busca opcional.
     * @param string $cliente Cliente relacionado aos ofícios.
     * @return array Resultado da operação.
     */
    public function listarDocumentos($ano, $busca, $cliente)
    {
        try {
            $result = $this->documentoModel->listar($ano, $busca, $cliente);

            if (empty($result)) {
                return ['status' => 'empty', 'message' => 'Nenhum documento encontrado.'];
            }

            return ['status' => 'success', 'dados' => $result];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('documento_error', 'ID do erro: ' . $erro_id . ' | ' . $e->getMessage());
            return ['status' => 'error', 'status_code' => 500, 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Buscar um ofício específico.
     *
     * @param string $coluna Nome da coluna para busca.
     * @param mixed $valor Valor a ser buscado.
     * @return array Resultado da busca.
     */
    public function buscarDocumento($coluna, $valor)
    {
        try {
            $documento = $this->documentoModel->buscar($coluna, $valor);
            if ($documento) {
                return ['status' => 'success', 'dados' => $documento];
            } else {
                return ['status' => 'not_found', 'message' => 'Documento não encontrado.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('documento_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Apagar um ofício existente.
     *
     * @param string $documento_id ID do ofício a ser apagado.
     * @return array Resultado da operação.
     */
    public function apagarDocumento($documento_id)
    {
        try {
            $documento = $this->buscarDocumento('documento_id', $documento_id);

            if ($documento['status'] == 'not_found') {
                return $documento;
            }

            if (isset($documento['dados'][0]['documento_arquivo'])) {
                $this->fileUploader->deleteFile($documento['dados'][0]['documento_arquivo']);
            }

            $this->documentoModel->apagar($documento_id);
            return ['status' => 'success', 'message' => 'Documento apagado com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('documento_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
