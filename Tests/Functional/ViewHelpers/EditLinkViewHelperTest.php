<?php

namespace Hoogi91\Charts\Tests\Functional\ViewHelpers;

class EditLinkViewHelperTest extends AbstractViewHelperTestCase
{
    private const MOCKED_RETURN_URL = '/typo3/functional/testing/';

    private array $returnUrl = [
        1 => '/typo3/module/web/layout?token=dummyToken&id=1',
        456 => '/typo3/module/web/layout?token=dummyToken&id=456',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $_SERVER['REQUEST_URI'] = self::MOCKED_RETURN_URL;
    }

    /**
     * @testWith [1, "tx_charts_domain_model_chartdata"]
     *           [123, "tx_charts_domain_model_chartdata", 456]
     *           [456, "tt_content"]
     *           [456, "tt_content", 1]
     */
    public function testRender(
        int $recordId,
        string $table,
        ?int $returnPid = null
    ): void {
        $expectedQueryData = [
            'token' => 'dummyToken',
            sprintf('edit[%s][%d]', $table, $recordId) => 'edit'
        ];
        if (isset($this->returnUrl[$returnPid])) {
            $expectedQueryData['returnUrl'] = $this->returnUrl[$returnPid];
        } else {
            $expectedQueryData['returnUrl'] = self::MOCKED_RETURN_URL;
        }

        $expectedHref = str_replace('&', '&amp;', '/typo3/record/edit?' . http_build_query($expectedQueryData));
        $expectedReturnPid = $returnPid !== null ? ' returnPid="' . $returnPid . '"' : '';

        self::assertEquals(
            vsprintf(
                '<a class="link" recordId="%d" recordTable="%s"%s href="%s">Link</a>',
                [$recordId, $table, $expectedReturnPid, $expectedHref]
            ),
            $this->getView(
                '<test:backend.editLink class="link" recordId="{recordId}" recordTable="{table}" returnPid="{pid}">Link</test:backend.editLink>',
                ['recordId' => $recordId, 'table' => $table, 'pid' => $returnPid]
            )->render()
        );
    }
}
