<?php

namespace App\Tests\Rector\Fixtures;

function shorthandHelpers()
{
    $name = config('app.name');
    $timezone = config('app.timezone', 'UTC');
    config(['app.locale' => 'en']);
    $value = cache('key');
    $token = session('token');
    $service = app(\App\Rector\HelperFunctionToFacadeRector::class);
    $view = view('welcome', ['name' => 'Taylor']);
    $input = request('email');
}

?>
-----
<?php

namespace App\Tests\Rector\Fixtures;

function shorthandHelpers()
{
    $name = \Illuminate\Support\Facades\Config::get('app.name');
    $timezone = \Illuminate\Support\Facades\Config::get('app.timezone', 'UTC');
    \Illuminate\Support\Facades\Config::set(['app.locale' => 'en']);
    $value = \Illuminate\Support\Facades\Cache::get('key');
    $token = \Illuminate\Support\Facades\Session::get('token');
    $service = \Illuminate\Support\Facades\App::make(\App\Rector\HelperFunctionToFacadeRector::class);
    $view = \Illuminate\Support\Facades\View::make('welcome', ['name' => 'Taylor']);
    $input = \Illuminate\Support\Facades\Request::input('email');
}

?>
