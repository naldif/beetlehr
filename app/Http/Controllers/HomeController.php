<?php

namespace App\Http\Controllers;

class HomeController extends AdminBaseController
{
    function __construct()
    {
        $this->title = "BattleHR | Dashboard";
        $this->path = "dashboard/index";
    }
}
