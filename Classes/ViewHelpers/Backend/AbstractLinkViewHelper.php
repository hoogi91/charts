<?php

declare(strict_types=1);

namespace Hoogi91\Charts\ViewHelpers\Backend;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

abstract class AbstractLinkViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var string
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $tagName = 'a';

    /**
     * @var array<string, string>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $arguments = [];

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerTagAttribute('recordId', 'int', 'uid of the record to edit', true);
        $this->registerTagAttribute('recordTable', 'string', 'string of the table record', true);
    }

    public function render(): string
    {
        /** @var string $renderedContent */
        $renderedContent = $this->renderChildren();
        $classes = trim($this->arguments['class']);
        if (!empty($classes)) {
            $this->tag->addAttribute('class', $classes);
        }
        $this->tag->addAttribute('href', $this->renderModuleUrl($this->arguments));
        $this->tag->setContent($renderedContent);

        return $this->tag->render();
    }

    /**
     * @param array<mixed> $arguments
     */
    abstract protected function renderModuleUrl(array $arguments = []): string;
}
