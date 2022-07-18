<?php

namespace Tests\Traits;

use Database\Seeders\MandatoryDataSeeder;
use Illuminate\Support\Facades\Schema;

trait FillDatabaseWithMandatoryData
{
    /**
     * Setup up the database with mandatory data.
     *
     * @return void
     *
     * @throws IncompleteTestError if the database is not ready.
     */
    public function setUpFillDatabaseWithMandatoryData()
    {
        if (! Schema::hasTable('roles')) {
            static::markTestIncomplete('It was not possible to fill the database with mandatory data. Check the database-related traits of the current test suite.');
        }

        $this->seed(MandatoryDataSeeder::class);
    }
}
