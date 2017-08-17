<?php

namespace Ph3\DockerArch\Application;

use Twig_Loader_Filesystem;
use Twig_Environment;
use Twig_Extension_Debug;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class TemplatedFileGenerator implements TemplatedFileGeneratorInterface
{
    /**
     * @var Twig_Environment
     */
    private $templateEngine;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $loader = new Twig_Loader_Filesystem(__DIR__ . '/Resources/views');
        $this->templateEngine = new Twig_Environment($loader);
    }

    /**
     * @return Twig_Environment
     */
    public function getTemplateEngine(): Twig_Environment
    {
        return $this->templateEngine;
    }

    /**
     * @param string $viewPath
     * @param array  $parameters
     *
     * @return string
     */
    public function render(string $viewPath, array $parameters = []): string
    {
        return $this->getTemplateEngine()->render($viewPath, $parameters);
    }
}
