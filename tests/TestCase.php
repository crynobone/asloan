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
    public function expectValidationException(callable $given, array $expectedErrors): void
    {
        try {
            $given();
        } catch (ValidationException $exception) {
            (new JsonInspector($exception->errors()))->assertFragment($expectedErrors);

            return;
        }

        $this->fail('The test does not throw ValidationException.');
    }
}
