<?php

namespace Hoogi91\Charts\ViewHelpers\Backend;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Class AbstractLinkViewHelper
 * @package Hoogi91\Charts\ViewHelpers\Backend
 */
abstract class AbstractLinkViewHelper extends AbstractTagBasedViewHelper
{

    /**
     * @var string
     */
    protected $tagName = 'a';

    /**
     * Initializes the arguments
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerTagAttribute('recordId', 'int', 'uid of the record to edit', true);
        $this->registerTagAttribute('recordTable', 'string', 'string of the table record', true);
    }

    /**
     * @return string
     */
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

    /**
     * @param array $arguments
     *
     * @return string
     */
    abstract protected function renderModuleUrl(array $arguments = []): string;
}
