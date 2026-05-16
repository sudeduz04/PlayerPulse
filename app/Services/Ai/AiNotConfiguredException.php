<?php

namespace App\Services\Ai;

use RuntimeException;

class AiNotConfiguredException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('AI sağlayıcısı yapılandırılmamış. .env dosyasında OPENAI_API_KEY veya GEMINI_API_KEY ayarlayın.');
    }
}
