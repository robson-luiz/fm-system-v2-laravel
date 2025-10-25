<?php

namespace App\Services\SmsProviders;

use App\Contracts\SmsProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class CustomSmsProvider implements SmsProviderInterface
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function send(string $to, string $message): bool
    {
        try {
            $url = $this->config['api_url'] ?? '';
            $method = strtoupper($this->config['http_method'] ?? 'POST');
            $headers = $this->parseHeaders($this->config['headers'] ?? '');
            
            // Preparar dados da requisição
            $data = $this->prepareRequestData($to, $message);
            
            // Fazer requisição HTTP
            $response = $this->makeHttpRequest($method, $url, $data, $headers);
            
            // Verificar se foi bem-sucedido
            return $this->isSuccessResponse($response);
            
        } catch (Exception $e) {
            Log::error('Erro no provedor SMS customizado', [
                'provider' => $this->getName(),
                'error' => $e->getMessage(),
                'to' => $to
            ]);
            return false;
        }
    }

    public function testConnection(): array
    {
        try {
            $testNumber = $this->config['test_number'] ?? '+5511999999999';
            $testMessage = 'Teste de conexão - FM System';
            
            $success = $this->send($testNumber, $testMessage);
            
            return [
                'success' => $success,
                'message' => $success 
                    ? 'Conexão testada com sucesso!' 
                    : 'Falha na conexão. Verifique as configurações.'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao testar conexão: ' . $e->getMessage()
            ];
        }
    }

    public function validateConfig(array $config): array
    {
        $errors = [];
        
        if (empty($config['api_url'])) {
            $errors[] = 'URL da API é obrigatória';
        } elseif (!filter_var($config['api_url'], FILTER_VALIDATE_URL)) {
            $errors[] = 'URL da API deve ser válida';
        }
        
        if (empty($config['http_method'])) {
            $errors[] = 'Método HTTP é obrigatório';
        } elseif (!in_array(strtoupper($config['http_method']), ['GET', 'POST', 'PUT', 'PATCH'])) {
            $errors[] = 'Método HTTP deve ser GET, POST, PUT ou PATCH';
        }
        
        if (empty($config['phone_field'])) {
            $errors[] = 'Campo do telefone é obrigatório';
        }
        
        if (empty($config['message_field'])) {
            $errors[] = 'Campo da mensagem é obrigatório';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    public function getConfigFields(): array
    {
        return [
            'provider_name' => [
                'label' => 'Nome do Provedor',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'Ex: Iagente, ZenviaNow, etc.'
            ],
            'api_url' => [
                'label' => 'URL da API',
                'type' => 'url',
                'required' => true,
                'placeholder' => 'https://api.provedor.com/sms/send'
            ],
            'http_method' => [
                'label' => 'Método HTTP',
                'type' => 'select',
                'required' => true,
                'options' => [
                    'POST' => 'POST',
                    'GET' => 'GET',
                    'PUT' => 'PUT',
                    'PATCH' => 'PATCH'
                ]
            ],
            'phone_field' => [
                'label' => 'Campo do Telefone',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'to, phone, number, etc.'
            ],
            'message_field' => [
                'label' => 'Campo da Mensagem',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'message, text, msg, etc.'
            ],
            'headers' => [
                'label' => 'Headers HTTP',
                'type' => 'textarea',
                'required' => false,
                'placeholder' => "Authorization: Bearer TOKEN\nContent-Type: application/json"
            ],
            'additional_fields' => [
                'label' => 'Campos Adicionais',
                'type' => 'textarea',
                'required' => false,
                'placeholder' => "sender: FM System\napi_key: sua_chave_aqui"
            ],
            'success_indicators' => [
                'label' => 'Indicadores de Sucesso',
                'type' => 'textarea',
                'required' => false,
                'placeholder' => "status_code: 200\nresponse_contains: success"
            ],
            'test_number' => [
                'label' => 'Número para Teste',
                'type' => 'text',
                'required' => false,
                'placeholder' => '+5511999999999'
            ]
        ];
    }

    public function getName(): string
    {
        return $this->config['provider_name'] ?? 'Provedor Customizado';
    }

    public function getDescription(): string
    {
        return 'Provedor SMS configurável para integração com qualquer API REST';
    }

    /**
     * Preparar dados da requisição
     */
    protected function prepareRequestData(string $to, string $message): array
    {
        $data = [
            $this->config['phone_field'] => $to,
            $this->config['message_field'] => $message
        ];
        
        // Adicionar campos extras
        if (!empty($this->config['additional_fields'])) {
            $additionalFields = $this->parseKeyValuePairs($this->config['additional_fields']);
            $data = array_merge($data, $additionalFields);
        }
        
        return $data;
    }

    /**
     * Fazer requisição HTTP
     */
    protected function makeHttpRequest(string $method, string $url, array $data, array $headers): \Illuminate\Http\Client\Response
    {
        $http = Http::withHeaders($headers);
        
        switch ($method) {
            case 'GET':
                return $http->get($url, $data);
            case 'POST':
                return $http->post($url, $data);
            case 'PUT':
                return $http->put($url, $data);
            case 'PATCH':
                return $http->patch($url, $data);
            default:
                return $http->post($url, $data);
        }
    }

    /**
     * Verificar se a resposta indica sucesso
     */
    protected function isSuccessResponse(\Illuminate\Http\Client\Response $response): bool
    {
        // Verificar código de status HTTP
        if (!$response->successful()) {
            return false;
        }
        
        // Verificar indicadores customizados de sucesso
        if (!empty($this->config['success_indicators'])) {
            $indicators = $this->parseKeyValuePairs($this->config['success_indicators']);
            
            foreach ($indicators as $key => $expectedValue) {
                if ($key === 'status_code') {
                    if ($response->status() != $expectedValue) {
                        return false;
                    }
                } elseif ($key === 'response_contains') {
                    if (strpos($response->body(), $expectedValue) === false) {
                        return false;
                    }
                } else {
                    // Verificar campo na resposta JSON
                    $responseData = $response->json();
                    if (!isset($responseData[$key]) || $responseData[$key] != $expectedValue) {
                        return false;
                    }
                }
            }
        }
        
        return true;
    }

    /**
     * Converter headers em array
     */
    protected function parseHeaders(string $headersString): array
    {
        if (empty($headersString)) {
            return [];
        }
        
        $headers = [];
        $lines = explode("\n", $headersString);
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            $parts = explode(':', $line, 2);
            if (count($parts) === 2) {
                $headers[trim($parts[0])] = trim($parts[1]);
            }
        }
        
        return $headers;
    }

    /**
     * Converter string chave:valor em array
     */
    protected function parseKeyValuePairs(string $string): array
    {
        if (empty($string)) {
            return [];
        }
        
        $pairs = [];
        $lines = explode("\n", $string);
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            $parts = explode(':', $line, 2);
            if (count($parts) === 2) {
                $pairs[trim($parts[0])] = trim($parts[1]);
            }
        }
        
        return $pairs;
    }
}
