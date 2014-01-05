<?php

namespace Procedure\FunctionLoader;

/**
 * Function loader following the PSR-4 standard (applicable to classes) by applying it to functions.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class PSR4FunctionLoader
{
    /**
     * Array mapping namespaces to directories. Indexed by the namespaces.
     *
     * @var string[]
     */
    private $namespaces = [];

    /**
     * {@inheritdoc}
     */
    public function load($functionName)
    {
        $file = $this->findFile($functionName);

        if (! $file) {
            throw new \RuntimeException("Unable to load function $functionName");
        }

        require $file;

        return $functionName;
    }

    public function registerNamespaces($namespace, $directory)
    {
        $this->namespaces[$namespace] = $directory;
    }

    private function findFile($fullFunctionName)
    {
        $pos = strrpos($fullFunctionName, '\\');
        $namespace = substr($fullFunctionName, 0, $pos);
        $functionName = substr($fullFunctionName, $pos + 1);

        foreach ($this->namespaces as $currentNamespace => $directory) {
            // Check if the namespace matches
            if (strpos($namespace, $currentNamespace) !== 0) {
                continue;
            }

            // Remove registered namespace part from the namespace (PSR-4)
            $subNamespace = ltrim($namespace, $currentNamespace);

            $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $subNamespace) . DIRECTORY_SEPARATOR . $functionName . '.php';

            $file = $directory . DIRECTORY_SEPARATOR . $fileName;
            if (is_file($file)) {
                return $file;
            }
        }

        return null;
    }
}
