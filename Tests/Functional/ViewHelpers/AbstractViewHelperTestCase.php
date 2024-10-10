<?php

declare(strict_types=1);

namespace Hoogi91\Charts\Tests\Functional\ViewHelpers;

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use TYPO3Fluid\Fluid\View\TemplateView;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperResolver;

abstract class AbstractViewHelperTestCase extends FunctionalTestCase
{
    /**
     * @var array<non-empty-string>
     */
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/charts',
        'typo3conf/ext/spreadsheets',
    ];

    /**
     * @param array<mixed> $arguments
     */
    protected function getView(string $template, array $arguments = []): TemplateView
    {
        // Get the Fluid namespaces from the global configuration
        $namespaces = $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces'] ?? [];

        // Initialize the Fluid TemplateView
        $view = new TemplateView();

        // Set the template source
        $view->getRenderingContext()->getTemplatePaths()->setTemplateSource($template);

        // Set the ViewHelperResolver from TYPO3Fluid
        $resolver = new ViewHelperResolver();
        $resolver->setNamespaces($namespaces);

        // Assign the ViewHelperResolver to the RenderingContext
        $view->getRenderingContext()->setViewHelperResolver($resolver);

        // Add custom namespace for the ViewHelpers
        $view->getRenderingContext()->getViewHelperResolver()->addNamespace(
            'test',
            'Hoogi91\\Charts\\ViewHelpers'
        );

        // Assign variables to the view
        $view->assignMultiple($arguments);

        return $view;
    }
}
