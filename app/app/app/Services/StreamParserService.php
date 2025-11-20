<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class StreamParserService
{
    /**
     * Парсит строку конфигурации, извлекая данные из всех найденных блоков 'stream'.
     *
     * @param string $configString Строка конфигурации, содержащая один или несколько блоков stream.
     * @return array Массив, содержащий ассоциативные массивы с данными потоков.
     */
    public function parse(string $configString): array
    {
        // Паттерн остается прежним, но теперь он будет искать все совпадения.
        $pattern = '/stream\s+(?P<id>[a-zA-Z0-9_]+)\s*\{.*?input\s+(?P<link>[^;]+);.*?title\s+(?P<title>[^;]+);.*?\}/six';

        $streams = [];

        // Используем preg_match_all для поиска всех совпадений в строке.
        // PREG_SET_ORDER гарантирует, что каждое полное совпадение будет отдельным элементом массива.
        if (preg_match_all($pattern, $configString, $matches, PREG_SET_ORDER)) {
            
            // Перебираем каждое отдельное совпадение
            foreach ($matches as $match) {
                // Добавляем новый поток в массив $streams, используя двойные кавычки для ключей.
                $streams[] = array(
                    "id"    => trim($match["id"]),
                    "title" => trim($match["title"]),
                  //  "link"  => trim($match["link"]),
                );
            }
        }

        return $streams;
    }

     public function parseFile(string $relativePath): array
    {
        // Используем public_path() для корректного формирования абсолютного пути
        // к файлу внутри директории public.
        //$absolutePath = public_path($relativePath);
        $absolutePath = $relativePath;
        if (!File::exists($absolutePath)) {
            // В логе теперь отображаем полный путь, который искали.
            \Log::error("Configuration file not found.", ['path' => $absolutePath]);
            return []; // Возвращаем пустой массив при ошибке или отсутствии файла
        }

        $configString = File::get($absolutePath);

        return $this->parse($configString);
    }
}