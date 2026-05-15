<?php

namespace App\CQRS\Bus;

class Bus implements BusInterface
{
    public function dispatch(object $message): mixed
    {
        $handlerClass = $this->resolveHandler($message);

        return app($handlerClass)->handle($message);
    }

    private function resolveHandler(object $message): string
    {
        $class = get_class($message);

        // App\CQRS\Category\Commands\CreateCategoryCommand
        //   → App\CQRS\Category\Handlers\CreateCategoryHandler
        // App\CQRS\Category\Queries\ListCategoriesQuery
        //   → App\CQRS\Category\Handlers\ListCategoriesHandler
        $handlerClass = str_replace(
            ['\\Commands\\', '\\Queries\\'],
            ['\\Handlers\\', '\\Handlers\\'],
            $class
        );

        $handlerClass = preg_replace('/(Command|Query)$/', 'Handler', $handlerClass);

        if (!class_exists($handlerClass)) {
            throw new \RuntimeException("No handler found for [{$class}].");
        }

        return $handlerClass;
    }
}
