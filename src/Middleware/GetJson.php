<?php

namespace GabineteDigital\Middleware;

class GetJson
{
    public function getJson($url, $headers = [])
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, true); // Captura os cabeçalhos da resposta
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE); // Obtém o tamanho do cabeçalho
        $header = substr($response, 0, $header_size); // Separa o cabeçalho do corpo da resposta
        $body = substr($response, $header_size); // Separa o corpo da resposta

        if (curl_errno($ch)) {
            curl_close($ch);
            return ['error' => curl_error($ch)];
        }

        curl_close($ch);

        // Processa os cabeçalhos para um array associativo
        $header_lines = explode("\r\n", trim($header));
        $header_assoc = [];
        foreach ($header_lines as $line) {
            if (strpos($line, ':') !== false) {
                list($key, $value) = explode(': ', $line, 2);
                $header_assoc[$key] = $value;
            }
        }

        return [
            'headers' => $header_assoc,
            'dados' => json_decode($body, true)
        ];
    }
}
