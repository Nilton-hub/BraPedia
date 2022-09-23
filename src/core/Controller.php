<?php

namespace src\core;

use src\support\Auth;

abstract class Controller
{
    public function __construct()
    {
        Auth::user();
    }
}
