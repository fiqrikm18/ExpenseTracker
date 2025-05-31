<?php

namespace App\Http\Controllers;

use App\Traits\Formatter;
use App\Traits\HttpResponser;

abstract class Controller
{
    use HttpResponser, Formatter;
}
