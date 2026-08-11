<?php

namespace App\Tests\Rector\Fixtures;

function skippedHelpers()
{
    // Array form of cache()/session() is not a simple read, so it is left alone.
    cache(['key' => 'value'], 60);
    session(['key' => 'value']);

    // Helpers without a facade equivalent are untouched.
    $path = base_path('config');
    abort(404);
}
