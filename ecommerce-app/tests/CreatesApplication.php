<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // Override Vite in tests so views using @vite() render without built assets
        $app->instance(\Illuminate\Foundation\Vite::class, new class implements \Illuminate\Contracts\Support\Htmlable {
            public function __invoke(array $entryPoints = []): self { return $this; }
            public function hotFile(): string { return public_path('/hot'); }
            public function reactRefresh(): string { return ''; }
            public function toHtml(): string { return ''; }
            public function __toString(): string { return ''; }
        });

        return $app;
    }
}
