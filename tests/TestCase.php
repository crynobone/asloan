<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Validation\ValidationException;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Expect validation exception assert.
     */
    public function expectValidationException(callable $given, callable $handleException): void
    {
        try {
            $given();
        } catch (ValidationException $exception) {
            $handleException($exception);

            return;
        }

        $this->fail('The test does not throw ValidationException.');
    }
}
