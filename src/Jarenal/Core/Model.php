<?php

namespace Jarenal\Core;

use DI\Annotation\Inject;
use Jarenal\Core\Database;

abstract class Model
{
    /**
     * @Inject("Jarenal\Core\Database")
     * @var Database $database
     */
    protected $database;

    abstract public function save();

}
