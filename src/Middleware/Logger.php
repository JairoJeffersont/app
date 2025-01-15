<?php

namespace GabineteDigital\Middleware;

class Logger
{

    function novoLog($title, $message)
    {
        $logFile = dirname(__DIR__, 2) . '/logs/' .  $title . '.log';
        $formattedMessage = date('Y-m-d H:i:s') . ' ' . $message . PHP_EOL;
        file_put_contents($logFile, $formattedMessage, FILE_APPEND | LOCK_EX);
    }

    // Função para pegar o IP do usuário
    function get_client_ip()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        return 'Desconhecido';
    }

    // Função para pegar o User-Agent do navegador
    function get_user_agent()
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? 'Desconhecido';
    }

    // Função para pegar o Sistema Operacional
    function get_os()
    {
        $userAgent = $this->get_user_agent();
        $os = 'Desconhecido';

        // Verifica os sistemas operacionais mais comuns
        $os_platforms = [
            'Windows' => 'Windows',
            'Linux' => 'Linux',
            'Macintosh|Mac OS X' => 'Mac OS',
            'Android' => 'Android',
            'iPhone' => 'iPhone',
            'iPad' => 'iPad'
        ];

        foreach ($os_platforms as $key => $value) {
            if (preg_match("/$key/i", $userAgent)) {
                $os = $value;
                break;
            }
        }

        return $os;
    }

    // Função para pegar o navegador
    function get_browser_info()
    {
        $userAgent = $this->get_user_agent();
        $browser = 'Desconhecido';

        // Verifica os navegadores mais comuns
        $browsers = [
            'Firefox' => 'Firefox',
            'Chrome' => 'Chrome',
            'Safari' => 'Safari',
            'Opera' => 'Opera',
            'Edge' => 'Edge',
            'MSIE' => 'Internet Explorer',
            'Trident' => 'Internet Explorer'
        ];

        foreach ($browsers as $key => $value) {
            if (preg_match("/$key/i", $userAgent)) {
                $browser = $value;
                break;
            }
        }

        return $browser;
    }

    // Função para coletar todas as informações do usuário
    function get_user_info()
    {
        return [
            'ip' => $this->get_client_ip(),
            'os' => $this->get_os(),
            'browser' => $this->get_browser_info(),
            'user_agent' => $this->get_user_agent()
        ];
    }
}
