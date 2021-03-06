<?php

namespace Hoogi91\Charts\ViewHelpers;

use Closure;
use Hoogi91\Charts\Domain\Repository\ChartDataRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Class GetChartDataViewHelper
 * @package Hoogi91\Charts\ViewHelpers
 */
class GetChartDataViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @return void
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('list', 'string', 'single or list of uid\'s pointing to chart datasets', true);
    }

    /**
     * Reverses the string
     *
     * @param array $arguments
     * @param Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     *
     * @return array
     * @throws InvalidQueryException
     */
    public static function renderStatic(
        array $arguments,
        Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): array {
        $uidList = GeneralUtility::intExplode(',', $arguments['list'], true);
        if (empty($uidList)) {
            return [];
        }

        $query = static::getChartRepository()->createQuery();
        $query->matching($query->in('uid', $uidList));
        return $query->execute()->toArray();
    }

    /**
     * @return ChartDataRepository
     */
    protected static function getChartRepository(): ChartDataRepository
    {
        return GeneralUtility::makeInstance(ObjectManager::class)->get(ChartDataRepository::class);
    }
}
