<?php

declare(strict_types=1);

use Maximaster\BitrixLoader\BitrixLoader;

describe(BitrixLoader::class, function () {
    it('should instaniate from documentRoot-rich composer.json', function () {
        $loader = BitrixLoader::fromComposerConfigExtra(__DIR__ . '/fixtures/composer-with-document-root.json', 'documentRoot');
        expect($loader->documentRoot)->toBe('www/from-composer');
    });

    it('should fail instaniate from documentRoot-less composer.json', function () {
        expect(static fn () => BitrixLoader::fromComposerConfigExtra(__DIR__ . '/fixtures/empty-composer.json'))
            ->toThrow();
    });

    it('should instaniate from $_ENV', function () {
        $documentRoot = 'www/from-global-env';
        $variableName = '__UNIT_TEST_TMP';

        $_ENV[$variableName] = $documentRoot;

        $loader = BitrixLoader::fromEnvironment($variableName);
        expect($loader->documentRoot)->toBe($documentRoot);
    });

    it('should instaniate from getenv', function () {
        $documentRoot = 'www/from-getenv';
        $variableName = '__UNIT_TEST_TMP';

        $putted = putenv(sprintf('%s=%s', $variableName, $documentRoot));
        if ($putted === false) {
            // Не удастся проверить в этом окружении, пропускаем тест.
            return;
        }

        $loader = BitrixLoader::fromEnvironment($variableName);
        expect($loader->documentRoot)->toBe($documentRoot);
    });

    it('should execute callback when prologBefore called with it', function () {
        $loader = new BitrixLoader(__DIR__);
        $throwMessage = 'I_WAS_EXPECTED';
        expect(static fn () => $loader->prologBefore(static function () use ($throwMessage): void {
            /** @noinspection PhpUnhandledExceptionInspection */
            throw new Exception($throwMessage);
        }))
            ->toThrow($throwMessage);
    });
});
