<?php

namespace Hoogi91\Charts\Tests\Functional\ViewHelpers;

use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\Core\ViewHelper\ViewHelperResolver;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use TYPO3Fluid\Fluid\View\TemplateView;

abstract class AbstractViewHelperTestCase extends FunctionalTestCase
{

    protected $pathsToLinkInTestInstance = [
        'typo3conf/ext/charts/Tests/Fixtures/' => 'fileadmin/user_upload',
    ];

    protected $testExtensionsToLoad = [
        'typo3conf/ext/charts',
        'typo3conf/ext/spreadsheets',
    ];

    protected function getView(string $template, array $arguments = []): TemplateView
    {
        $view = new TemplateView();
        $view->getRenderingContext()->getTemplatePaths()->setTemplateSource($template);
        $view->getRenderingContext()->setViewHelperResolver(
            new ViewHelperResolver(
                $this->getContainer(),
                $this->getContainer()->get(ObjectManager::class),
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces'] ?? []
            )
        );
        $view->getRenderingContext()->getViewHelperResolver()->addNamespace(
            'test',
            'Hoogi91\\Charts\\ViewHelpers'
        );
        $view->assignMultiple($arguments);

        return $view;
    }
}
