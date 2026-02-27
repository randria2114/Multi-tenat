<?php

namespace App\Application\Constants;

class ErrorCodes
{
    // Tenant related
    public const TENANT_CREATION_FAILED = 'T001';
    public const TENANT_NOT_FOUND = 'T002';
    public const DOMAIN_CREATION_FAILED = 'T003';

    // Module related
    public const MODULE_NOT_ACTIVE = 'M001';
    public const MODULE_ACCESS_DENIED = 'M002';

    // Subscription related
    public const SUBSCRIPTION_EXPIRED = 'S001';
    public const MAX_USERS_REACHED = 'S002';

    public static function getMessage(string $code): string
    {
        return match ($code) {
            self::TENANT_CREATION_FAILED => 'Échec de la création du client.',
            self::TENANT_NOT_FOUND => 'Client introuvable.',
            self::MODULE_NOT_ACTIVE => 'Le module demandé n\'est pas activé pour ce compte.',
            self::SUBSCRIPTION_EXPIRED => 'L\'abonnement du compte a expiré.',
            default => 'Une erreur inattendue est survenue.',
        };
    }
}
