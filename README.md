# maximaster/bitrix-loader

Загружает Битрикс, получая информацию о DOCUMENT_ROOT из разных источников.

```bash
composer require maximaster/bitrix-loader
```

```php
use Maximaster\BitrixLoader\BitrixLoader;

// Через данные в composer.json:
$bitrixLoader = BitrixLoader::fromComposerConfigExtra(__DIR__ . '/composer.json', 'documentRoot');
// или из переменной окружения:
$bitrixLoader = BitrixLoader::fromEnvironment('BITRIX_DOCUMENT_ROOT');
// или попытаться догадатсья:
$bitrixLoader = BitrixLoader::fromGuess();
// Потом подключаем, чтобы перед этим были объявлены консольные константы:
$bitrixLoader->prologBefore(static fn () => $bitrixLoader->defineConsoleScriptConstants());
```

## Зачем?

* минимизируется дублирования кода определения DOCUMENT_ROOT;
* можно внедрить `BitrixLoader` как зависимость;
* очевидным образом видно, где подключается Битрикс.
