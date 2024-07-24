<?php

declare(strict_types=1);

namespace Maximaster\BitrixLoader;

use InvalidArgumentException;

/**
 * Загрузчик ядра Битрикс.
 *
 * @immutable
 */
final class BitrixLoader
{
    private const DEFAULT_DOCUMENT_ROOT_PARAMETER_NAME = 'BITRIX_DOCUMENT_ROOT';

    /** @psalm-var non-empty-string */
    public string $documentRoot;

    /**
     * @throws InvalidArgumentException
     *
     * @psalm-param non-empty-string $configPath
     * @psalm-param non-empty-string $extraName
     */
    public static function fromComposerConfigExtra(
        string $configPath,
        string $extraName = self::DEFAULT_DOCUMENT_ROOT_PARAMETER_NAME
    ): self {
        if (is_file($configPath) === false) {
            throw new InvalidArgumentException(sprintf('Файл %s недоступен.', $configPath));
        }

        $config = json_decode(file_get_contents($configPath), true);
        if (is_array($config) === false) {
            throw new InvalidArgumentException(
                sprintf('Указанный конфиг (%s) не содержит валидный JSON.', $configPath)
            );
        }

        $documentRoot = $config['extra'][$extraName] ?? '';
        if (is_string($documentRoot) === false || $documentRoot === '') {
            throw new InvalidArgumentException(
                sprintf('Ожидалось наличие не пустого параметра extra.%s в файле %s.', $extraName, $configPath)
            );
        }

        return new self($documentRoot);
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function fromEnvironment(string $variableName = self::DEFAULT_DOCUMENT_ROOT_PARAMETER_NAME): self
    {
        foreach (array_filter([getenv($variableName), $_ENV[$variableName] ?? '']) as $documentRoot) {
            if (is_string($documentRoot) && $documentRoot !== '') {
                return new self($documentRoot);
            }
        }

        throw new InvalidArgumentException(sprintf('В переменных окружения не найдена заполненная %s.', $variableName));
    }

    /**
     * @throws InvalidArgumentException
     *
     * @psalm-param non-empty-string $documentRoot
     */
    public function __construct(string $documentRoot)
    {
        /** @psalm-var string $documentRoot */
        if ($documentRoot === '') {
            throw new InvalidArgumentException('Параметр documentRoot не должен быть пуст.');
        }

        $this->documentRoot = $documentRoot;
    }

    /**
     * Подключить файл bitrix/modules/main/include/prolog_before.php.
     */
    public function prologBefore(?callable $beforeRequire = null): void
    {
        // Уже загружены.
        if (defined('START_EXEC_PROLOG_BEFORE_1')) {
            return;
        }

        $_SERVER['DOCUMENT_ROOT'] = $this->documentRoot;

        if (is_callable($beforeRequire)) {
            $beforeRequire();
        }

        /** @psalm-suppress UnresolvableInclude */
        require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
    }
}
