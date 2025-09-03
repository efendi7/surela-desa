<?php

namespace App\Services\Interfaces;

interface TemplateServiceInterface
{
    /**
     * Mengambil daftar template .docx yang tersedia
     *
     * @return array
     */
    public function getAvailableTemplates(): array;

    /**
     * Memvalidasi apakah template ada
     *
     * @param string $templateName
     * @return bool
     */
    public function templateExists(string $templateName): bool;

    /**
     * Mendapatkan path lengkap template
     *
     * @param string $templateName
     * @return string|null
     */
    public function getTemplatePath(string $templateName): ?string;
}