<?php

namespace Tests;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // SiteSetting memoises its singleton for the life of the process.
        // RefreshDatabase drops the row between tests, so the memo has to go
        // with it or the second test in a class holds a deleted record.
        SiteSetting::forgetInstance();
    }
}
