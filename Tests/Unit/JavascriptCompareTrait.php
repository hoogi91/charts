<?php

namespace Hoogi91\Charts\Tests\Unit;

use Peast\Formatter\PrettyPrint;
use Peast\Peast;
use Peast\Renderer;
use PHPUnit\Framework\Constraint\IsEqual;

trait JavascriptCompareTrait
{
    private string $tempReplaceCharacter = '';

    private array $replacements = [
        '[' => "\n[\n",
        ']' => "\n]\n",
        '{' => "\n{\n",
        '}' => "\n}\n",
        ';' => "\n;\n",
        ',' => "\n,\n",
//        "\n;\n" => ";",
    ];

    public function assertStringEqualsJavascriptFile(
        string $expectedFile,
        string $actualString,
        string $message = ''
    ): void {
        static::assertFileExists($expectedFile, $message);

        $renderer = (new Renderer())->setFormatter(new PrettyPrint());
        $fileContent = $renderer->render(Peast::latest(file_get_contents($expectedFile))->parse());
        $actualString = $renderer->render(Peast::latest($actualString)->parse());
        static::assertNotEmpty($fileContent);
        static::assertNotEmpty($actualString);

        static::assertThat($actualString, new IsEqual($fileContent), $message);
    }
}
