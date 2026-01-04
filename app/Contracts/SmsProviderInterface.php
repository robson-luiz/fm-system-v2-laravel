<?php

namespace App\Contracts;

interface SmsProviderInterface
{
    /**
     * Enviar SMS
     *
     * @param string $to Número de telefone de destino
     * @param string $message Mensagem a ser enviada
     * @return bool Sucesso do envio
     */
    public function send(string $to, string $message): bool;

    /**
     * Testar conexão com o provedor
     *
     * @return array ['success' => bool, 'message' => string]
     */
    public function testConnection(): array;

    /**
     * Validar configurações do provedor
     *
     * @param array $config Configurações do provedor
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateConfig(array $config): array;

    /**
     * Obter campos de configuração necessários
     *
     * @return array ['field_name' => ['label' => string, 'type' => string, 'required' => bool]]
     */
    public function getConfigFields(): array;

    /**
     * Obter nome do provedor
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Obter descrição do provedor
     *
     * @return string
     */
    public function getDescription(): string;
}
