<?php

declare(strict_types=1);

namespace Hoogi91\Charts\DataProcessing\Charts;

use Closure;
use Composer\Autoload\ClassLoader;
use Hoogi91\Charts\DataProcessing\Charts\Library\ChartJs;
use Psr\Container\ContainerExceptionInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LibraryRegistry
{
    private readonly ServiceLocator $libraries;
    private readonly LibraryInterface $defaultLibrary;

    public function __construct(ServiceLocator $libraries = null, ChartJs $defaultLibrary = null)
    {
        // @codeCoverageIgnoreStart
        if ($libraries === null || $defaultLibrary === null) {
            // TODO: this is just a bad hack for install tool requests!
            //  Install tool is loaded with FailsafeContainer see install.php => "Bootstrap::init($classLoader, true)"
            //  Currently there is no official way to define DI based classes (like the usage of service locator here)
            //  inside of these requests. The only way is to bootstrap without failsafe container, getting this
            //  service again and retrieving its libraries property by closure.
            $autoloader = GeneralUtility::getContainer()->get(ClassLoader::class);
            $instance = Bootstrap::init($autoloader)->get(self::class);
            $libraries ??= Closure::fromCallable(fn () => $this->libraries)->call($instance);
            $defaultLibrary ??= Closure::fromCallable(fn () => $this->defaultLibrary)->call($instance);
        }
        // @codeCoverageIgnoreEnd
        $this->libraries = $libraries;
        $this->defaultLibrary = $defaultLibrary;
    }

    public function getDefaultLibrary(): LibraryInterface
    {
        return $this->defaultLibrary;
    }

    public function getLibrary(string $name): ?LibraryInterface
    {
        try {
            $library = $this->libraries->get($name);

            return $library instanceof LibraryInterface ? $library : null;
        } catch (ContainerExceptionInterface) {
            return null;
        }
    }

    /**
     * @param array<string> $data
     */
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
