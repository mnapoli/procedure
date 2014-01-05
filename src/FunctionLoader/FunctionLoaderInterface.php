<?php

namespace Procedure\FunctionLoader;

interface FunctionLoaderInterface
{
    /**
     * @param string $functionName
     *
     * @return callable
     */
    public function load($functionName);
}
