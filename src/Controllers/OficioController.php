<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\FileUploader;
use GabineteDigital\Middleware\Logger;
use GabineteDigital\Models\OficioModel;
use PDOException;

/**
 * Classe OficioController
 *
 * Controla as operações relacionadas a ofícios, incluindo criação, atualização, listagem,
 * busca e exclusão de registros.
 */
class OficioController
{
    /**
     * @var OficioModel Instância do modelo OficioModel para interagir com os dados.
     */
    private $oficioModel;

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
     * Construtor do OficioController.
     *
     * Inicializa as instâncias necessárias para manipulação de ofícios.
     */
    public function __construct()
    {
        $this->oficioModel = new OficioModel();
        $this->logger = new Logger();
        $this->fileUploader = new FileUploader();
        $this->pasta_foto = 'public/arquivos/oficios';
    }

    /**
     * Criar um novo ofício.
     *
     * @param array $dados Dados do ofício a serem inseridos.
     * @return array Resultado da operação.
     */
    public function criarOficio($dados)
    {
        $camposObrigatorios = ['oficio_titulo', 'oficio_resumo', 'arquivo', 'oficio_ano', 'oficio_orgao', 'oficio_criado_por', 'oficio_cliente'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        if (!empty($dados['arquivo']['tmp_name'])) {
            $uploadResult = $this->fileUploader->uploadFile($this->pasta_foto, $dados['arquivo'], ['pdf'], 5);

            if ($uploadResult['status'] !== 'success') {
                return $uploadResult;
            }

            $dados['oficio_arquivo'] = $uploadResult['file_path'];
        }

        try {
            $this->oficioModel->criar($dados);
            return ['status' => 'success', 'message' => 'Ofício criado com sucesso.'];
        } catch (PDOException $e) {

            if (!empty($dados['arquivo']['tmp_name'])) {
                $this->fileUploader->deleteFile($dados['oficio_arquivo'] ?? null);
            }

            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'Esse ofício já está cadastrado.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('oficio_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Atualizar os dados de um ofício existente.
     *
     * @param string $oficio_id ID do ofício a ser atualizado.
     * @param array $dados Dados atualizados do ofício.
     * @return array Resultado da operação.
     */
    public function atualizarOficio($oficio_id, $dados)
    {
        $camposObrigatorios = ['oficio_titulo', 'oficio_resumo', 'arquivo', 'oficio_ano', 'oficio_orgao'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        $oficio = $this->buscarOficio('oficio_id', $oficio_id);

        if ($oficio['status'] == 'not_found') {
            return $oficio;
        }

        if (!empty($dados['arquivo']['tmp_name'])) {
            $uploadResult = $this->fileUploader->uploadFile($this->pasta_foto, $dados['arquivo'], ['pdf'], 5);

            if ($uploadResult['status'] !== 'success') {
                return $uploadResult;
            }

            if (!empty($oficio['dados'][0]['oficio_arquivo'])) {
                $this->fileUploader->deleteFile($oficio['dados'][0]['oficio_arquivo']);
            }

            $dados['oficio_arquivo'] = $uploadResult['file_path'];
        } else {
            $dados['oficio_arquivo'] = $oficio['dados'][0]['oficio_arquivo'] ?? null;
        }

        try {
            $this->oficioModel->atualizar($oficio_id, $dados);
            return ['status' => 'success', 'message' => 'Ofício atualizado com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('oficio_log', $e->getMessage() . ' | ' . $erro_id);
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
    public function listarOficios($ano, $busca, $cliente)
    {
        try {
            $result = $this->oficioModel->listar($ano, $busca, $cliente);

            if (empty($result)) {
                return ['status' => 'empty', 'message' => 'Nenhum ofício encontrado.'];
            }

            return ['status' => 'success', 'dados' => $result];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('oficio_error', 'ID do erro: ' . $erro_id . ' | ' . $e->getMessage());
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
    public function buscarOficio($coluna, $valor)
    {
        try {
            $oficio = $this->oficioModel->buscar($coluna, $valor);
            if ($oficio) {
                return ['status' => 'success', 'dados' => $oficio];
            } else {
                return ['status' => 'not_found', 'message' => 'Ofício não encontrado.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('oficio_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Apagar um ofício existente.
     *
     * @param string $oficio_id ID do ofício a ser apagado.
     * @return array Resultado da operação.
     */
    public function apagarOficio($oficio_id)
    {
        try {
            $oficio = $this->buscarOficio('oficio_id', $oficio_id);

            if ($oficio['status'] == 'not_found') {
                return $oficio;
            }

            if (isset($oficio['dados'][0]['oficio_arquivo'])) {
                $this->fileUploader->deleteFile($oficio['dados'][0]['oficio_arquivo']);
            }

            $this->oficioModel->apagar($oficio_id);
            return ['status' => 'success', 'message' => 'Ofício apagado com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('oficio_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
