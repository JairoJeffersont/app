<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\FileUploader;
use GabineteDigital\Middleware\Logger;
use GabineteDigital\Models\ClippingModel;
use PDOException;

/**
 * Classe ClippingController
 *
 * Controla as operações relacionadas a clippings, incluindo criação, atualização, listagem,
 * busca e exclusão de registros.
 */
class ClippingController
{
    /**
     * @var ClippingModel Instância do modelo ClippingModel para interagir com os dados.
     */
    private $clippingModel;

    /**
     * @var Logger Instância do Logger para registrar erros.
     */
    private $logger;

    /**
     * @var FileUploader Instância do FileUploader para manipulação de arquivos.
     */
    private $fileUploader;

    /**
     * @var string Caminho da pasta onde os arquivos de clippings serão armazenados.
     */
    private $pasta_arquivo;

    /**
     * Construtor do ClippingController.
     *
     * Inicializa as instâncias necessárias para manipulação de clippings.
     */
    public function __construct()
    {
        $this->clippingModel = new ClippingModel();
        $this->logger = new Logger();
        $this->fileUploader = new FileUploader();
        $this->pasta_arquivo = 'public/arquivos/clippings';
    }

    /**
     * Criar um novo clipping.
     *
     * @param array $dados Dados do clipping a serem inseridos.
     * @return array Resultado da operação.
     */
    public function criarClipping($dados)
    {
        $camposObrigatorios = ['clipping_titulo', 'clipping_resumo', 'clipping_link', 'clipping_orgao', 'clipping_tipo', 'clipping_criado_por', 'clipping_cliente'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        if (!empty($dados['arquivo']['tmp_name'])) {
            $uploadResult = $this->fileUploader->uploadFile($this->pasta_arquivo . '/' . $dados['clipping_cliente'], $dados['arquivo'], ['pdf', 'png', 'jpg', 'jpeg'], 5);

            if ($uploadResult['status'] !== 'success') {
                return $uploadResult;
            }

            $dados['clipping_arquivo'] = $uploadResult['file_path'];
        }

        try {
            $this->clippingModel->criar($dados);
            return ['status' => 'success', 'message' => 'Clipping criado com sucesso.'];
        } catch (PDOException $e) {
            if (!empty($dados['arquivo']['tmp_name'])) {
                $this->fileUploader->deleteFile($dados['clipping_arquivo'] ?? null);
            }

            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'Esse clipping já está cadastrado.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('clipping_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Atualizar os dados de um clipping existente.
     *
     * @param string $clipping_id ID do clipping a ser atualizado.
     * @param array $dados Dados atualizados do clipping.
     * @return array Resultado da operação.
     */
    public function atualizarClipping($clipping_id, $dados)
    {
        $camposObrigatorios = ['clipping_titulo', 'clipping_resumo', 'clipping_link', 'clipping_orgao', 'clipping_tipo'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        $clipping = $this->buscarClipping('clipping_id', $clipping_id);

        if ($clipping['status'] == 'not_found') {
            return $clipping;
        }

        if (!empty($dados['arquivo']['tmp_name'])) {
            $uploadResult = $this->fileUploader->uploadFile($this->pasta_arquivo . '/' . $dados['clipping_cliente'], $dados['arquivo'], ['pdf', 'png', 'jpg', 'jpeg'], 5);

            if ($uploadResult['status'] !== 'success') {
                return $uploadResult;
            }

            if (!empty($clipping['dados'][0]['clipping_arquivo'])) {
                $this->fileUploader->deleteFile($clipping['dados'][0]['clipping_arquivo']);
            }

            $dados['clipping_arquivo'] = $uploadResult['file_path'];
        } else {
            $dados['clipping_arquivo'] = $clipping['dados'][0]['clipping_arquivo'] ?? null;
        }

        try {
            $this->clippingModel->atualizar($clipping_id, $dados);
            return ['status' => 'success', 'message' => 'Clipping atualizado com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('clipping_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Listar clippings com base no tipo, termo de busca e cliente.
     *
     * @param int $tipo Tipo para filtrar os clippings.
     * @param string|null $busca Termo de busca opcional.
     * @param string $cliente Cliente relacionado aos clippings.
     * @return array Resultado da operação.
     */
    public function listarClippings($busca, $ano, $cliente)
    {
        try {
            $result = $this->clippingModel->listar($busca, $ano, $cliente);

            if (empty($result)) {
                return ['status' => 'empty', 'message' => 'Nenhum clipping encontrado.'];
            }

            return ['status' => 'success', 'dados' => $result];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('clipping_error', 'ID do erro: ' . $erro_id . ' | ' . $e->getMessage());
            return ['status' => 'error', 'status_code' => 500, 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Buscar um clipping específico.
     *
     * @param string $coluna Nome da coluna para busca.
     * @param mixed $valor Valor a ser buscado.
     * @return array Resultado da busca.
     */
    public function buscarClipping($coluna, $valor)
    {
        try {
            $clipping = $this->clippingModel->buscar($coluna, $valor);
            if ($clipping) {
                return ['status' => 'success', 'dados' => $clipping];
            } else {
                return ['status' => 'not_found', 'message' => 'Clipping não encontrado.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('clipping_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Apagar um clipping existente.
     *
     * @param string $clipping_id ID do clipping a ser apagado.
     * @return array Resultado da operação.
     */
    public function apagarClipping($clipping_id)
    {
        try {
            $clipping = $this->buscarClipping('clipping_id', $clipping_id);

            if ($clipping['status'] == 'not_found') {
                return $clipping;
            }

            if (isset($clipping['dados'][0]['clipping_arquivo'])) {
                $this->fileUploader->deleteFile($clipping['dados'][0]['clipping_arquivo']);
            }

            $this->clippingModel->apagar($clipping_id);
            return ['status' => 'success', 'message' => 'Clipping apagado com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('clipping_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Buscar clippings por ano.
     *
     * Retorna os clippings agrupados por ano, com o número de ocorrências de cada ano.
     * Caso não haja resultados, retorna mensagem informando que o ano não foi encontrado.
     *
     * @param string $cliente ID do cliente para filtrar os clippings.
     * @return array Resultado da operação, incluindo status e dados ou mensagem.
     */
    public function buscarAno($cliente)
    {
        try {
            $anos = $this->clippingModel->buscarAno($cliente);
            if ($anos) {
                return ['status' => 'success', 'dados' => $anos];
            } else {
                return ['status' => 'not_found', 'message' => 'Ano não encontrado.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('clipping_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Buscar clippings por tipo.
     *
     * Retorna os clippings agrupados por tipo, com a contagem de ocorrências de cada tipo.
     * Caso não haja resultados, retorna mensagem informando que o tipo não foi encontrado.
     *
     * @param string $ano Ano para filtrar os clippings.
     * @param string $cliente ID do cliente para filtrar os clippings.
     * @return array Resultado da operação, incluindo status e dados ou mensagem.
     */
    public function buscarTipo($ano, $cliente)
    {
        try {
            $tipos = $this->clippingModel->buscarTipo($ano, $cliente);
            if ($tipos) {
                return ['status' => 'success', 'dados' => $tipos];
            } else {
                return ['status' => 'not_found', 'message' => 'Tipo não encontrado.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('clipping_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Buscar clippings por órgão.
     *
     * Retorna os clippings agrupados por órgão, com a contagem de ocorrências de cada órgão.
     * Caso não haja resultados, retorna mensagem informando que o órgão não foi encontrado.
     *
     * @param string $ano Ano para filtrar os clippings.
     * @param string $cliente ID do cliente para filtrar os clippings.
     * @return array Resultado da operação, incluindo status e dados ou mensagem.
     */
    public function buscarOrgao($ano, $cliente)
    {
        try {
            $orgaos = $this->clippingModel->buscarOrgao($ano, $cliente);
            if ($orgaos) {
                return ['status' => 'success', 'dados' => $orgaos];
            } else {
                return ['status' => 'not_found', 'message' => 'Órgão não encontrado.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('clipping_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
