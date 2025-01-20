<?php

namespace GabineteDigital\Middleware;

class FileUploader
{

    /**
     * Método responsável por fazer o upload de arquivos.
     *
     * @param string $directory Diretório onde o arquivo será armazenado.
     * @param array $file Dados do arquivo ($_FILES array).
     * @param array $allowedTypes Tipos de arquivos permitidos.
     * @param int $maxSize Tamanho máximo permitido para o arquivo em megabytes.
     * @return array Retorna um array associativo com o status e mensagem do upload.
     */
    public function uploadFile($directory, $file, $allowedTypes, $maxSize, $uniqueFlag = true)
    {

        // Verifica se houve algum erro no upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['status' => 'upload_error', 'message' => 'Erro ao fazer upload'];
        }

        // Verifica se o tipo do arquivo é permitido
        if (!in_array($file['type'], $allowedTypes) && !in_array(pathinfo($file['name'], PATHINFO_EXTENSION), $allowedTypes)) {
            return ['status' => 'file_not_permited', 'message' => 'Tipo de arquivo não permitido'];
        }

        // Verifica se o tamanho do arquivo excede o limite permitido
        if ($file['size'] > $maxSize * 1024 * 1024) {
            return ['status' => 'too_big', 'message' => 'Tamanho do arquivo excede o limite permitido de ' . $maxSize . ' MB'];
        }

        // Verifica se o diretório existe, caso contrário, cria
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        // Gera um nome único para o arquivo
        if ($uniqueFlag) {
            $uniqueName = uniqid() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
            $destination = $directory . DIRECTORY_SEPARATOR . $uniqueName;
        }else{
            $fileName = $file['name'];
            $destination = $directory . DIRECTORY_SEPARATOR . $fileName;
        }


        // Verifica se o arquivo já existe
        if (file_exists($destination)) {
            return ['status' => 'file_exists', 'message' => 'Arquivo já existe no diretório'];
        }

        // Move o arquivo para o destino especificado
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return [
                'status' => 'success',
                'message' => 'Upload feito com sucesso.',
                'file_path' => str_replace('\\', '/', $destination)
            ];
        } else {
            return ['status' => 'folder_error', 'message' => 'Erro ao mover o arquivo para o diretório especificado.'];
        }
    }

    /**
     * Método responsável por deletar um arquivo.
     *
     * @param string $filePath Caminho completo do arquivo a ser deletado.
     * @return array Retorna um array associativo com o status e mensagem da operação.
     */
    public function deleteFile($filePath)
    {
        $filePath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $filePath);  // Normalizando caminho

        // Verifica se o arquivo existe
        if (file_exists($filePath)) {
            // Tentativa de exclusão do arquivo
            if (unlink($filePath)) {
                return ['status' => 'success', 'message' => 'Arquivo excluído com sucesso.'];
            } else {
                return ['status' => 'delete_error', 'message' => 'Erro ao excluir o arquivo.'];
            }
        } else {
            return ['status' => 'file_not_found', 'message' => 'Arquivo não encontrado.'];
        }
    }
}
