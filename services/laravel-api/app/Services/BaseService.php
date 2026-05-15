<?php

namespace App\Services;

use App\CQRS\Bus\BusInterface;

abstract class BaseService
{
    public function __construct(protected readonly BusInterface $bus) {}
}
