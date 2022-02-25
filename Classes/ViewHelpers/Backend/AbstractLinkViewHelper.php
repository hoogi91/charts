<?php

namespace Hoogi91\Charts\ViewHelpers\Backend;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

abstract class AbstractLinkViewHelper extends AbstractTagBasedViewHelper
{

    protected $tagName = 'a';

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerTagAttribute('recordId', 'int', 'uid of the record to edit', true);
        $this->registerTagAttribute('recordTable', 'string', 'string of the table record', true);
    }

    public function render(): string
    {
        $classes = trim($this->arguments['class']);
        if (!empty($classes)) {
            $this->tag->addAttribute('class', $classes);
        }
        $this->tag->addAttribute('href', $this->renderModuleUrl($this->arguments));
        $this->tag->setContent($this->renderChildren());
        return $this->tag->render();
    }

    abstract protected function renderModuleUrl(array $arguments = []): string;
}
