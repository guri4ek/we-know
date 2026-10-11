<?php

declare(strict_types=1);

namespace App\Tests\Fixtures\Jev;

final class ResponseFixture
{
    public static function data(): array
    {
        return json_decode(file_get_contents(__DIR__.'/success.json'), true, 32, JSON_THROW_ON_ERROR);
    }

    public static function json(): string
    {
        return file_get_contents(__DIR__.'/success.json');
    }

    public static function malformedBodies(): iterable
    {
        yield 'broken JSON' => ['{bad'];
        yield 'empty' => [''];
        yield 'null' => ['null'];
        yield 'list root' => ['[]'];
        yield 'string root' => ['"not a classification"'];
        yield 'oversized' => [str_repeat(' ', 65537)];
        foreach ([['model'], ['answers'], ['answers', 'intent'], ['answers', 'topic'], ['usage'],
            ['usage', 'input_tokens'], ['usage', 'output_tokens'],
            ['answers', 'intent', 'choice'], ['answers', 'intent', 'type'], ['answers', 'intent', 'confidence'],
            ['answers', 'intent', 'probabilities'], ['answers', 'topic', 'choice'], ['answers', 'topic', 'confidence']] as $path) {
            $data = self::data();
            $cursor = &$data;
            foreach (array_slice($path, 0, -1) as $part) {
                $cursor = &$cursor[$part];
            }
            unset($cursor[$path[array_key_last($path)]]);
            unset($cursor);
            yield 'missing '.implode('.', $path) => [json_encode($data, JSON_THROW_ON_ERROR)];
        }
        foreach ([
            [['model'], ''], [['model'], 5], [['answers'], []], [['answers', 'intent'], 'WHEN'],
            [['answers', 'intent', 'type'], 'score'], [['answers', 'topic', 'type'], 'noul'],
            [['answers', 'intent', 'choice'], 'ANY'], [['answers', 'intent', 'choice'], 'NEW_INTENT'],
            [['answers', 'topic', 'choice'], 'ANY'], [['answers', 'topic', 'choice'], 'NEW_TOPIC'],
            [['answers', 'intent', 'choice'], 1], [['answers', 'topic', 'choice'], null],
            [['answers', 'intent', 'confidence'], -0.1], [['answers', 'intent', 'confidence'], 1.1],
            [['answers', 'intent', 'confidence'], '0.8'], [['answers', 'intent', 'confidence'], true],
            [['answers', 'topic', 'confidence'], null], [['answers', 'topic', 'confidence'], 1.1],
            [['answers', 'intent', 'probabilities'], []], [['answers', 'intent', 'probabilities', 'WHEN'], '1'],
            [['answers', 'intent', 'probabilities', 'WHEN'], false], [['answers', 'intent', 'probabilities', 'WHEN'], -1],
            [['answers', 'intent', 'probabilities', 'WHEN'], 1.1], [['answers', 'topic', 'probabilities', 'TECH'], null],
            [['answers', 'topic', 'probabilities', 'TECH'], 0.5], [['answers', 'intent', 'probabilities', 'ALIEN'], 0],
            [['answers', 'intent', 'choice'], 'WHY'],
            [['usage', 'input_tokens'], -1], [['usage', 'input_tokens'], '100'],
            [['usage', 'input_tokens'], 1.5], [['usage', 'output_tokens'], true], [['usage', 'output_tokens'], -2],
        ] as $index => [$path, $value]) {
            $data = self::data();
            $cursor = &$data;
            foreach ($path as $part) {
                $cursor = &$cursor[$part];
            }
            $cursor = $value;
            unset($cursor);
            yield 'invalid '.$index.' '.implode('.', $path) => [json_encode($data, JSON_THROW_ON_ERROR)];
        }
        $data = self::data();
        unset($data['answers']['intent']['probabilities']['WHY']);
        yield 'incomplete distribution' => [json_encode($data, JSON_THROW_ON_ERROR)];
        yield 'infinite number' => [str_replace('"confidence": 1', '"confidence": 1e999', self::json())];
    }
}
