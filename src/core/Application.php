<?php

declare(strict_types=1);

namespace Clara\core;

use DI\Container;
use DI\ContainerBuilder;
use function DI\create;

final class Application
{
    private readonly Container $container;
    private readonly Router $router;

    private function __construct(
        private readonly string $basePath,
        private readonly array $config,
    ) {
        $dbConfig = $this->config['database'];

        if (str_starts_with($dbConfig['dsn'], 'sqlite:')) {
            $sqlitePath = substr($dbConfig['dsn'], strlen('sqlite:'));
            $dir = dirname($sqlitePath);

            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        $builder = new ContainerBuilder();
        $builder->addDefinitions([
            DB::class => create(DB::class)->constructor(
                $dbConfig['dsn'],
                $dbConfig['username'],
                $dbConfig['password'],
                $dbConfig['options'],
            ),
        ]);

        $this->container = $builder->build();
        $this->router = $this->container->get(Router::class);
        Route::setRouter($this->router);
    }

    public static function boot(string $basePath): self
    {
        return new self($basePath, require $basePath . '/config/app.php');
    }

    public function withRoutes(string $routesPath): self
    {
        require $routesPath;

        return $this;
    }

    public function run(): void
    {
        $this->router->dispatch();
    }
}
