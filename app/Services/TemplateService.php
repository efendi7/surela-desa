<?php

namespace App\Services;

use App\Services\Interfaces\TemplateServiceInterface;
use Illuminate\Support\Facades\File;

class TemplateService implements TemplateServiceInterface
{
    private string $templatePath;

    public function __construct()
    {
        $this->templatePath = resource_path('templates/docx');
    }

    /**
     * Mengambil daftar template .docx yang tersedia
     *
     * @return array
     */
    public function getAvailableTemplates(): array
    {
        if (!File::isDirectory($this->templatePath)) {
            return [];
        }

        $files = File::files($this->templatePath);
        
        return collect($files)
            ->filter(function ($file) {
                return strtolower($file->getExtension()) === 'docx';
            })
            ->map(function ($file) {
                return $file->getFilename();
            })
            ->values()
            ->all();
    }

    /**
     * Memvalidasi apakah template ada
     *
     * @param string $templateName
     * @return bool
     */
    public function templateExists(string $templateName): bool
    {
        $fullPath = $this->getTemplatePath($templateName);
        return $fullPath && File::exists($fullPath);
    }

    /**
     * Mendapatkan path lengkap template
     *
     * @param string $templateName
     * @return string|null
     */
    public function getTemplatePath(string $templateName): ?string
    {
        if (empty($templateName)) {
            return null;
        }

        return $this->templatePath . DIRECTORY_SEPARATOR . $templateName;
    }
}