<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Dengpju\PhpGen;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
            ],
            'commands' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'gen',
                    'description' => 'php gen config',
                    'source' => __DIR__ . '/../publish/gen.php',
                    'destination' => BASE_PATH . '/config/autoload/gen.php',
                ],
                [
                    'id' => 'watcher_config',
                    'description' => 'php gen config',
                    'source' => __DIR__ . '/../publish/watcher_config.php',
                    'destination' => BASE_PATH . '/config/autoload/watcher.php',
                ],
                [
                    'id' => 'watch',
                    'description' => 'watch',
                    'source' => __DIR__ . '/../publish/watch',
                    'destination' => BASE_PATH . '/watch',
                ],
                [
                    'id' => 'watcher',
                    'description' => 'watcher',
                    'source' => __DIR__ . '/../publish/watcher.php',
                    'destination' => BASE_PATH . '/watcher.php',
                ],
            ],
        ];
    }
}
