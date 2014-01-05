<?php

namespace Procedure\Controller;

use DI\ContainerInterface;
use Procedure\FunctionLoader;
use Procedure\FunctionLoader\FunctionLoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class FunctionControllerResolver implements ControllerResolverInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var FunctionLoaderInterface
     */
    private $functionLoader;

    public function __construct(ContainerInterface $container, FunctionLoaderInterface $functionLoader)
    {
        $this->container = $container;
        $this->functionLoader = $functionLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function getController(Request $request)
    {
        $functionName = $request->attributes->get('_controller');

        if (! $functionName) {
            return false;
        }

        return $this->functionLoader->load($functionName);
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments(Request $request, $controller)
    {
        $function = new \ReflectionFunction($controller);

        $arguments = array();

        foreach ($function->getParameters() as $reflectionParameter) {
            $parameterType = $reflectionParameter->getClass();

            // Inject request
            if ($parameterType && $parameterType->isInstance($request)) {
                $arguments[] = $request;
                continue;
            }

            // Inject a request parameter
            $value = $request->get($reflectionParameter->getName());
            if ($value !== null) {
                $arguments[] = $value;
                continue;
            }

            // Try to get the entry from the container
            if ($parameterType && $this->container->has($parameterType->getName())) {
                $arguments[] = $this->container->get($parameterType->getName());
                continue;
            }

            // Fallback to the default value if it exists
            if ($reflectionParameter->isDefaultValueAvailable()) {
                $arguments[] = $reflectionParameter->getDefaultValue();
                continue;
            }

            throw new \RuntimeException(sprintf(
                'Unguessable parameter "%s" for function %s()',
                $reflectionParameter->getName(),
                $function->getName()
            ));
        }

        return $arguments;
    }
}
