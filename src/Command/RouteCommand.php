<?php

declare(strict_types=1);

namespace Dengpju\PhpGen\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\HttpServer\Router\DispatcherFactory;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * php bin/hyperf.php dengpju:route server=http
 * Class RouteCommand
 * @package App\Command
 */
#[Command]
class RouteCommand extends BaseCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('dengpju:route');
    }

    public function configure()
    {
        parent::configure();
        $description = str_pad("Look Route List.", 20, " ", STR_PAD_RIGHT);
        $this->setDescription($description . 'php bin/hyperf.php dengpju:route server=http');
    }

    /**
     * @return array[]
     */
    protected function getArguments(): array
    {
        return [
            ['server', InputArgument::REQUIRED, 'Server Name,eg:http'],
        ];
    }

    public function handle()
    {
        $this->autoPublish();

        $server = $this->input->getArgument('server');
        $server = str_replace("server=", "", $server);
        $routers = make(DispatcherFactory::class)->getRouter($server);

        $routerCollector = [];
        foreach ($routers->getData() as $collector) {
            foreach ($collector as $method => $handlers) {
                /**
                 * @var \Hyperf\HttpServer\Router\Handler $handler
                 */
                foreach ($handlers as $handler) {
                    $options = $handler->options;
                    $middlewares = isset($options["middleware"]) ? $options["middleware"] : [];
                    if (is_array($handler->callback)) {
                        $handle = "{$handler->callback[0]}@{$handler->callback[1]}";
                        $key = "{$handle}.{$method}.{$handler->route}";
                        $routerCollector[$key] = [$method, $handler->route, $handle, sprintf("[%s]", implode(",", $middlewares))];
                    } elseif (is_callable($handler->callback)) {
                        $key = "Closure().{$method}.{$handler->route}";
                        $routerCollector[$key] = [$method, $handler->route, "Closure()", sprintf("[%s]", implode(",", $middlewares))];
                    } elseif (is_string($handler->callback)) {
                        $key = "{$handler->callback}.{$method}.{$handler->route}";
                        $routerCollector[$key] = [$method, $handler->route, $handler->callback, sprintf("[%s]", implode(",", $middlewares))];
                    }
                }
            }
        }

        ksort($routerCollector);
        foreach ($routerCollector as $item) {
            $method = str_pad($item[0], 7, " ", STR_PAD_RIGHT)." ";
            $route = str_pad($item[1], 32, " ", STR_PAD_RIGHT)." ";
            $handle = str_pad($item[2], 64, " ", STR_PAD_RIGHT);
            $middleware = $item[3];
            $this->line("{$method}{$route}{$handle}{$middleware}", 'info');
        }
    }
}
