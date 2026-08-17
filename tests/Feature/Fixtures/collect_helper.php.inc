<?php

namespace App\Tests\Rector\Fixtures;

function collectHelper()
{
    $empty = collect();
    $items = collect([1, 2, 3]);
    $names = collect(['taylor', 'larry'])->map(fn (string $name) => strtoupper($name));
}

?>
-----
<?php

namespace App\Tests\Rector\Fixtures;

function collectHelper()
{
    $empty = \Illuminate\Support\Collection::make();
    $items = \Illuminate\Support\Collection::make([1, 2, 3]);
    $names = \Illuminate\Support\Collection::make(['taylor', 'larry'])->map(fn (string $name) => strtoupper($name));
}

?>
