<?php

namespace Hoogi91\Charts\DataProcessing\Charts;

/**
 * Interface LibraryFlexformInterface
 * @package Hoogi91\Charts\DataProcessing\Charts
 */
interface LibraryFlexformInterface extends LibraryInterface
{

    /**
     * please note that this is related to pointerField value:
     * https://docs.typo3.org/typo3cms/TCAReference/6.2/Reference/Columns/Flex/Index.html#ds
     *
     * this array should always contain the key 'default' which points to default data structure
     *
     * @return array
     */
    public function getDataStructures();
}
