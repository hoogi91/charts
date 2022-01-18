<?php

namespace Hoogi91\Charts\DataProcessing\Charts;

use Psr\Container\ContainerExceptionInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Core\Environment;

class LibraryRegistry
{

    private ServiceLocator $libraries;

    public function __construct(ServiceLocator $libraries = null)
    {
        if ($libraries === null) {
            // TODO: this is just a bad hack for install tool requests!
            //      Install tool is loaded with FailsafeContainer see install.php => "Bootstrap::init($classLoader, true)"
            //      Currently there is no official way to define DI based classes (like the usage of service locator here)
            //      inside of these requests. The only way is to bootstrap without failsafe container, getting this
            //      service again and retrieving its libraries property by closure.
            $autoloader = require dirname(realpath(Environment::getBackendPath())) . '/vendor/autoload.php';
            $libraries = \Closure::fromCallable(fn() => $this->libraries)
                ->call(Bootstrap::init($autoloader)->get(self::class));
        }
        $this->libraries = $libraries;
    }

    public function getLibrary(string $name): ?LibraryInterface
    {
        try {
            return $this->libraries->get($name);
        } catch (ContainerExceptionInterface $exception) {
            return null;
        }
    }

    public function getLibrarySelect(array $data): string
    {
        $html = '<div class="form-inline">';
        $html .= sprintf('<input type="hidden" name="%s" value="%s"/>', $data['fieldName'], $data['fieldValue']);
        $html .= sprintf('<select class="form-control" name="%s">', $data['fieldName']);
        foreach ($this->libraries->getProvidedServices() as $name => $class) {
            if ($name === $data['fieldValue']) {
                $html .= sprintf('<option value="%1$s" selected="selected">%1$s (%2$s)</option>', $name, $class);
            } else {
                $html .= sprintf('<option value="%1$s">%1$s (%2$s)</option>', $name, $class);
            }
        }
        $html .= '</select>';
        $html .= '</div>';
        return $html;
    }
}
