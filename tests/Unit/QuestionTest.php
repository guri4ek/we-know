<?php

declare(strict_types=1);
namespace App\Tests\Unit;

use App\Domain\Answer\Question;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class QuestionTest extends TestCase
{
    public function testTrimsUnicodeWhitespaceAndCountsCodePoints(): void
    {
        self::assertSame('🦉', Question::from("\u{00A0} 🦉 \n")->text);
        self::assertSame(str_repeat('🦉', 500), Question::from(str_repeat('🦉', 500))->text);
    }

    #[DataProvider('invalid')]
    public function testRejectsInvalidQuestion(string $text): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Question::from($text);
    }

    public static function invalid(): iterable
    {
        yield ['']; yield ["\u{00A0} \n\t"]; yield ["a\0b"]; yield ["\xff"]; yield [str_repeat('🦉', 501)];
    }
}
