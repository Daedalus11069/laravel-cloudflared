<?php

namespace PixoVoid\Cloudflared\Concerns;

use Illuminate\Support\Str;

trait AssemblesPath
{
    protected function assemble(string ...$parts): string
    {
        return Str::of(implode('/', $parts))
            ->replace('\\', '/')
            ->replace('//', '/')
            ->toString();
    }
}
