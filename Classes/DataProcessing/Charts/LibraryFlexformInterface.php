<?php

declare(strict_types=1);

namespace Hoogi91\Charts\DataProcessing\Charts;

interface LibraryFlexformInterface extends LibraryInterface
{
    /**
     * @return array<mixed>
     */
    public function getDataStructures(): array;
}
