<?php

namespace App\Services;

abstract class BaseService
{
    use Traits\ManagesData;

    abstract protected function getModelClass(): string;
}
