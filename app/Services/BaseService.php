<?php

namespace App\Services;

abstract class BaseService
{
    use Traits\ManagesData;

    /**
     * Method abstrak ini MEWAJIBKAN setiap service anak (seperti CategoryService)
     * untuk mendefinisikan model Eloquent mana yang mereka kelola.
     * * @return string
     */
    abstract protected function getModelClass(): string;
}
