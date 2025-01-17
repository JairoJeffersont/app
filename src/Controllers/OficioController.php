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

    private $fileUploader;
    private $pasta_foto;



    /**
     * Construtor do OficioController.
     *
     * Inicializa as instâncias do modelo OficioModel e do Logger.
     */
    public function __construct()
    {
        $this->oficioModel = new OficioModel();
        $this->logger = new Logger();
        $this->fileUploader = new FileUploader();
        $this->pasta_foto = 'public/arquivos/oficios';
    }

    /**
     * Método para criar um novo ofício.
     *
     * @param array $dados Associativo com os dados do ofício a serem inseridos.
     * @return array Retorna um array com o status da operação e mensagem.
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
            $uploadResult = $this->fileUploader->uploadFile($this->pasta_foto, $dados['arquivo'], ['doc', 'docx', 'pdf', 'png'], 5);

            if ($uploadResult['status'] !== 'success') {
                return $uploadResult;
            }

            $dados['oficio_arquivo'] = $uploadResult['file_path'];
        }



        try {
            $this->oficioModel->criar($dados);
            return ['status' => 'success', 'message' => 'Ofício criado com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('oficio_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    /**
     * Método para atualizar os dados de um ofício.
     *
     * @param string $oficio_id ID do ofício a ser atualizado.
     * @param array $dados Associativo com os dados atualizados do ofício.
     * @return array Retorna um array com o status da operação e mensagem.
     */
    public function atualizarOficio($oficio_id, $dados)
    {
        $camposObrigatorios = ['oficio_titulo', 'oficio_resumo', 'oficio_arquivo', 'oficio_ano', 'oficio_orgao'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        $oficio = $this->buscarOficio('oficio_id', $oficio_id);

        if ($oficio['status'] == 'not_found') {
            return $oficio;
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
     * Método para listar ofícios com base no ano e um termo de busca.
     *
     * @param int $ano Ano dos ofícios a serem filtrados.
     * @param string|null $busca Termo de busca opcional.
     * @return array Retorna um array com o status da operação, mensagem e lista de ofícios.
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
     * Método para buscar um ofício específico baseado em uma coluna e valor fornecido.
     *
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor correspondente à coluna para busca.
     * @return array Retorna um array com o status da operação e dados encontrados ou mensagem de registro não encontrado.
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
     * Método para apagar um ofício.
     *
     * @param string $oficio_id ID do ofício a ser apagado.
     * @return array Retorna um array com o status da operação e mensagem de sucesso ou erro.
     */
    public function apagarOficio($oficio_id)
    {
        try {
            $oficio = $this->buscarOficio('oficio_id', $oficio_id);

            if ($oficio['status'] == 'not_found') {
                return $oficio;
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
