<?php

namespace Tests;

use Illuminate\Contracts\Support\Htmlable;

/**
 * Trait to fake Vite in tests so views that use @vite() render without requiring built assets.
 */
trait FakeViteForTests
{
    protected function setUpFakeVite(): void
    {
        $fake = new class implements Htmlable {
            public function __invoke(array $entryPoints = []): self
            {
                return $this;
            }

            public function toHtml(): string
            {
                return '';
            }
        };

        $this->instance(\Illuminate\Foundation\Vite::class, $fake);
    }
}
