<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

final class EnvVarHelper
{
    public const SECRET_ENV = 'app.secret_env';
    public const MAILER_FROM = 'app.mailer_from';
    public const ROOT_PATH = 'app.root_path';
    public const HOST_PROTOCOL= 'app.host_protocol';
    public const YOUSIGN_APP = 'app.yousign_app';
    public const HOST_URL = 'app.host_url';
    public const WEBHOOK_YOUSIGN_URL = 'app.webhook_yousign_url';
    public const API_SUPER_ADMIN_FIRST_NAME = 'app.api_super_admin_first_name';
    public const API_SUPER_ADMIN_LAST_NAME = 'app.api_super_admin_last_name';
    public const API_SUPER_ADMIN_PHONE = 'app.api_super_admin_phone';
    public const API_SUPER_ADMIN_EMAIL = 'app.api_super_admin_email';
    public const API_SUPER_ADMIN_PASSWORD = 'app.api_super_admin_password';
    public const MODEL_IMPORT_NAME = 'app.model_import_name';
    public const INFO_PDF_NAME = 'app.info_pdf_name';
    public const MONOLOG_LOGGING_PATH = 'app.monolog_logging_path';
    public const SUPPORT_EMAIL = 'app.support_email';

    private ContainerBagInterface $parameters;

    public function __construct(ContainerBagInterface $parameters)
    {
        $this->parameters = $parameters;
    }

    public function fetch(string $key): string
    {
        return $this->parameters->get($key);
    }
}
