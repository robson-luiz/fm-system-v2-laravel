<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\ImageManager;

class ImageUploadService
{
    /**
     * Upload de imagem do perfil do usuário logado
     */
    public function uploadProfileImage(UploadedFile $file, int $userId): string
    {
        return $this->processImageUpload($file, $userId, 'profile');
    }

    /**
     * Upload de imagem do usuário (admin editando)
     */
    public function uploadUserImage(UploadedFile $file, int $userId): string
    {
        return $this->processImageUpload($file, $userId, 'users');
    }

    /**
     * Processar upload e processamento da imagem
     */
    private function processImageUpload(UploadedFile $file, int $userId, string $type): string
    {
        // Nome do arquivo sem extensão
        $nameWithoutExt = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        // Slug do nome (ex: "Foto de Perfil" => "foto-de-perfil")
        $slug = Str::slug($nameWithoutExt);

        // Extensão (ex: jpg)
        $extension = $file->getClientOriginalExtension();

        // Nome final do arquivo
        $filename = "{$slug}.{$extension}";

        // === PROCESSAMENTO DA IMAGEM ===
        // Cria manager com GD
        $manager = new ImageManager(new Driver());

        // Carrega imagem
        $image = $manager->read($file->getRealPath());

        // Dimensões
        $width  = $image->width();
        $height = $image->height();

        // Fazer crop quadrado se necessário
        if ($width !== $height) {
            $side = min($width, $height);
            $x = intval(($width  - $side) / 2);
            $y = intval(($height - $side) / 2);

            // Recorta quadrado
            $image->crop($side, $side, $x, $y);
        }

        // Redimensiona 150x150
        $image->resize(150, 150);

        // Converte para binário no formato original (qualidade 100%)
        // Detecta a extensão enviada
        $extension = strtolower($file->getClientOriginalExtension());

        switch ($extension) {
            case 'png':
                // PNG → nível de compressão (0 sem compressão, 9 máxima)
                $encoded = $image->encode(new PngEncoder(0));
                break;

            case 'jpg':
            case 'jpeg':
                // JPG → qualidade 0 a 100
                $encoded = $image->encode(new JpegEncoder(100));
                break;

            default:
                // fallback: força para jpg
                $encoded = $image->encode(new JpegEncoder(100));
                $extension = 'jpg';
                $filename = "{$slug}.jpg";
                break;
        }

        // Caminho no storage
        $storagePath = "{$type}/{$userId}/{$filename}";

        // Upload para o storage público
        Storage::disk('public')->put($storagePath, (string) $encoded);

        return $filename;
    }

    /**
     * Remover imagem anterior
     */
    public function deleteImage(string $filename, int $userId, string $type): void
    {
        if (empty($filename)) {
            return;
        }

        $storagePath = "{$type}/{$userId}/{$filename}";

        if (Storage::disk('public')->exists($storagePath)) {
            Storage::disk('public')->delete($storagePath);
        }
    }

    /**
     * Verificar se a imagem existe no storage
     */
    public function imageExists(string $filename, int $userId, string $type): bool
    {
        if (empty($filename)) {
            return false;
        }

        $storagePath = "{$type}/{$userId}/{$filename}";
        return Storage::disk('public')->exists($storagePath);
    }

    /**
     * Obter URL da imagem
     */
    public function getImageUrl(string $filename, int $userId, string $type): string
    {
        if (empty($filename)) {
            return asset('/images/users/user.png');
        }

        $storagePath = "{$type}/{$userId}/{$filename}";
        
        if (Storage::disk('public')->exists($storagePath)) {
            return asset("storage/{$storagePath}");
        }

        return asset('/images/users/user.png');
    }
}