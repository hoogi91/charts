<?php

declare(strict_types=1);

namespace Hoogi91\Charts\ViewHelpers;

use Closure;
use Hoogi91\Charts\Domain\Repository\ChartDataRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class GetChartDataViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    public function initializeArguments(): void
    {
        $this->registerArgument('list', 'string', 'single or list of uid\'s pointing to chart datasets', true);
    }

    /**
     * @param array<string> $arguments
     *
     * @return array<mixed>
     */
    public static function renderStatic(
        array $arguments,
        Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): array {
        $uidList = GeneralUtility::intExplode(',', (string) $arguments['list'], true);
        if (empty($uidList)) {
            return [];
        }

        $query = GeneralUtility::makeInstance(ChartDataRepository::class)->createQuery();
        $query->matching($query->in('uid', $uidList));

        return $query->execute()->toArray();
    }
}
