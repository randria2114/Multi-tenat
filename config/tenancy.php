<?php

declare(strict_types=1);

use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Database\Models\Tenant;

return [
    'tenant_model' => \App\Domains\Tenant\Models\Tenant::class,
    'id_generator' => Stancl\Tenancy\UUIDGenerator::class,

    'domain_model' => Domain::class,

    /**
     * Liste des domaines hébergeant votre application centrale.
     *
     * Utilisé uniquement si vous utilisez l'identification par domaine ou sous-domaine.
     */
    'central_domains' => [
        '127.0.0.1',
        'localhost',
    ],

    /**
     * Les bootstrappers de Tenancy sont exécutés lors de l'initialisation de la session client.
     * Leur responsabilité est de rendre les fonctionnalités de Laravel compatibles avec le multi-tenant.
     *
     * Pour configurer leur comportement, voir les clés de configuration ci-dessous.
     */
    'bootstrappers' => [
        Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\CacheTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\QueueTenancyBootstrapper::class,
        // Stancl\Tenancy\Bootstrappers\RedisTenancyBootstrapper::class, // Note: phpredis is needed
    ],

    /**
     * Configuration de la base de données client. Utilisée par DatabaseTenancyBootstrapper.
     */
    'database' => [
        'central_connection' => env('DB_CONNECTION', 'central'),

        /**
         * Connexion utilisée comme "modèle" pour la création dynamique de la connexion DB client.
         * Note : ne nommez pas votre connexion modèle 'tenant', ce nom est réservé par le package.
         */
        'template_tenant_connection' => null,

        /**
         * Les noms des bases de données clients sont créés ainsi :
         * préfixe + id_client + suffixe.
         */
        'prefix' => 'tenant',
        'suffix' => '',

        /**
         * Les TenantDatabaseManagers sont des classes gérant la création et suppression des bases de données clients.
         */
        'managers' => [
            'sqlite' => Stancl\Tenancy\TenantDatabaseManagers\SQLiteDatabaseManager::class,
            'mysql' => Stancl\Tenancy\TenantDatabaseManagers\MySQLDatabaseManager::class,
            'pgsql' => Stancl\Tenancy\TenantDatabaseManagers\PostgreSQLDatabaseManager::class,

            /**
             * Utilisez ce manager pour MySQL afin de créer un utilisateur DB spécifique pour chaque base client.
             * Vous pouvez personnaliser les droits via la propriété $grants.
             */
            // 'mysql' => Stancl\Tenancy\TenantDatabaseManagers\PermissionControlledMySQLDatabaseManager::class,

            /**
             * Désactivez le manager pgsql ci-dessus et activez celui-ci si vous
             * souhaitez séparer les clients par schémas plutôt que par bases de données.
             */
            // 'pgsql' => Stancl\Tenancy\TenantDatabaseManagers\PostgreSQLSchemaManager::class, // Separate by schema instead of database
        ],
    ],

    /**
     * Cache tenancy config. Used by CacheTenancyBootstrapper.
     *
     * This works for all Cache facade calls, cache() helper
     * calls and direct calls to injected cache stores.
     *
     * Each key in cache will have a tag applied on it. This tag is used to
     * scope the cache both when writing to it and when reading from it.
     *
     * You can clear cache selectively by specifying the tag.
     */
    'cache' => [
        'tag_base' => 'tenant', // Ce tag_base, suivi de l'id_client, formera un tag appliqué à chaque appel de cache.
    ],

    /**
     * Filesystem tenancy config. Used by FilesystemTenancyBootstrapper.
     * https://tenancyforlaravel.com/docs/v3/tenancy-bootstrappers/#filesystem-tenancy-boostrapper.
     */
    'filesystem' => [
        /**
         * Chaque disque listé aura le suffixe_base, suivi de l'id_client.
         */
        'suffix_base' => 'tenant',
        'disks' => [
            'local',
            'public',
            // 's3',
        ],

        /**
         * Use this for local disks.
         *
         * See https://tenancyforlaravel.com/docs/v3/tenancy-bootstrappers/#filesystem-tenancy-boostrapper
         */
        'root_override' => [
            // Les racines des disques qui seront remplacées après l'ajout du suffixe de storage_path().
            'local' => '%storage_path%/app/',
            'public' => '%storage_path%/app/public/',
        ],

        /**
         * Should storage_path() be suffixed.
         *
         * Note: Disabling this will likely break local disk tenancy. Only disable this if you're using an external file storage service like S3.
         *
         * For the vast majority of applications, this feature should be enabled. But in some
         * edge cases, it can cause issues (like using Passport with Vapor - see #196), so
         * you may want to disable this if you are experiencing these edge case issues.
         */
        'suffix_storage_path' => true,

        /**
         * By default, asset() calls are made multi-tenant too. You can use global_asset() and mix()
         * for global, non-tenant-specific assets. However, you might have some issues when using
         * packages that use asset() calls inside the tenant app. To avoid such issues, you can
         * disable asset() helper tenancy and explicitly use tenant_asset() calls in places
         * where you want to use tenant-specific assets (product images, avatars, etc).
         */
        'asset_helper_tenancy' => true,
    ],

    /**
     * Configuration Redis.
     *
     * Note : phpredis est nécessaire.
     * Note : Ce n'est utile que si vous faites des appels directs à la façade Redis.
     */
    'redis' => [
        'prefix_base' => 'tenant', // Chaque clé Redis sera préfixée par ce prefix_base, suivi de l'id_client.
        'prefixed_connections' => [ // Connexions Redis dont les clés sont préfixées.
            // 'default',
        ],
    ],

    /**
     * Les Features sont des classes fournissant des fonctionnalités supplémentaires.
     * Elles sont exécutées que la session client soit initialisée ou non.
     */
    'features' => [
        // Stancl\Tenancy\Features\UserImpersonation::class,
        // Stancl\Tenancy\Features\TelescopeTags::class,
        // Stancl\Tenancy\Features\UniversalRoutes::class,
        // Stancl\Tenancy\Features\TenantConfig::class,
        // Stancl\Tenancy\Features\CrossDomainRedirect::class,
        // Stancl\Tenancy\Features\ViteBundler::class,
    ],

    /**
     * Should tenancy routes be registered.
     *
     * Tenancy routes include tenant asset routes. By default, this route is
     * enabled. But it may be useful to disable them if you use external
     * storage (e.g. S3 / Dropbox) or have a custom asset controller.
     */
    'routes' => true,

    /**
     * Parameters used by the tenants:migrate command.
     */
    'migration_parameters' => [
        '--force' => true, // This needs to be true to run migrations in production.
        '--path' => [database_path('migrations/tenant')],
        '--realpath' => true,
    ],

    /**
     * Paramètres utilisés par la commande tenants:seed.
     */
    'seeder_parameters' => [
        '--class' => 'DatabaseSeeder', // Classe seeder racine
        // '--force' => true, // Nécessaire en production
    ],
];
