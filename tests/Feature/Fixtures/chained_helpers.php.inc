<?php

namespace App\Tests\Rector\Fixtures;

function chainedHelpers()
{
    $user = auth()->user();
    $all = config()->all();
    $json = response()->json(['ok' => true]);
    $target = redirect()->route('home');
    $agent = request()->userAgent();
}

?>
-----
<?php

namespace App\Tests\Rector\Fixtures;

function chainedHelpers()
{
    $user = \Illuminate\Support\Facades\Auth::user();
    $all = \Illuminate\Support\Facades\Config::all();
    $json = \Illuminate\Support\Facades\Response::json(['ok' => true]);
    $target = \Illuminate\Support\Facades\Redirect::route('home');
    $agent = \Illuminate\Support\Facades\Request::userAgent();
}

?>
