<?php

namespace App\CQRS\Bus;

interface BusInterface
{
    public function dispatch(object $message): mixed;
}
