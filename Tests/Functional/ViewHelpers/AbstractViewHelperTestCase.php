<?php

declare(strict_types=1);

namespace Hoogi91\Charts\Tests\Functional\ViewHelpers;

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\Core\ViewHelper\ViewHelperResolver;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use TYPO3Fluid\Fluid\View\TemplateView;

abstract class AbstractViewHelperTestCase extends FunctionalTestCase
{
    /**
     * @var array<string, non-empty-string>
     */
    protected array $pathsToLinkInTestInstance = [
        'typo3conf/ext/charts/Tests/Fixtures/' => 'fileadmin/user_upload',
    ];

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
        $namespaces = $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces'] ?? [];
        $resolver = (new Typo3Version())->getMajorVersion() > 11
            ? new ViewHelperResolver($this->getContainer(), $namespaces)
            : new ViewHelperResolver(
                $this->getContainer(),
                $this->getContainer()->get(ObjectManager::class),
                $namespaces
            );

        $view = new TemplateView();
        $view->getRenderingContext()->getTemplatePaths()->setTemplateSource($template);
        $view->getRenderingContext()->setViewHelperResolver($resolver);
        $view->getRenderingContext()->getViewHelperResolver()->addNamespace(
            'test',
            'Hoogi91\\Charts\\ViewHelpers'
        );
        $view->assignMultiple($arguments);

        return $view;
    }
}
