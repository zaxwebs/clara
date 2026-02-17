<?php

declare(strict_types=1);

namespace DI\Invoker;

use Invoker\ParameterResolver\ParameterResolver;
use Psr\Container\ContainerInterface;
use ReflectionFunctionAbstract;

/**
 * Inject the container, the definition or any other service using type-hints.
 *
 * {@internal This class is similar to TypeHintingResolver and TypeHintingContainerResolver,
 *            we use this instead for performance reasons}
 *
 * @author Quim Calpe <quim@kalpe.com>
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class FactoryParameterResolver implements ParameterResolver
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getParameters(
        ReflectionFunctionAbstract $reflection,
        array $providedParameters,
        array $resolvedParameters
    ) : array {
        $parameters = $reflection->getParameters();

        // Skip parameters already resolved
        if (! empty($resolvedParameters)) {
            $parameters = array_diff_key($parameters, $resolvedParameters);
        }

        foreach ($parameters as $index => $parameter) {
            $parameterClassName = $this->getParameterClassName($parameter);

            if ($parameterClassName === null) {
                continue;
            }

            if ($parameterClassName === 'Psr\Container\ContainerInterface') {
                $resolvedParameters[$index] = $this->container;
            } elseif ($parameterClassName === 'DI\Factory\RequestedEntry') {
                // By convention the second parameter is the definition
                $resolvedParameters[$index] = $providedParameters[1];
            } elseif ($this->container->has($parameterClassName)) {
                $resolvedParameters[$index] = $this->container->get($parameterClassName);
            }
        }

        return $resolvedParameters;
    }

    private function getParameterClassName(\ReflectionParameter $parameter) : ?string
    {
        $parameterType = $parameter->getType();

        if (! $parameterType instanceof \ReflectionNamedType || $parameterType->isBuiltin()) {
            return null;
        }

        return $parameterType->getName();
    }
}
