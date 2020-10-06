<?php

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Tests\Enums\SuperPowers;
use BenSampo\Enum\Tests\Models\ModelWithFlaggedQueries;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase;

class FlaggedEnumQueriesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
    }

    protected function setUpDatabase(Application $app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('superpowers');
            $table->timestamps();
        });
    }

    /** @test */
    public function it_can_constrain_query_by_flagged_enum()
    {
        ModelWithFlaggedQueries::create([
            'superpowers' =>  SuperPowers::flags([SuperPowers::Flight, SuperPowers::Immortality])
        ]);

        ModelWithFlaggedQueries::create([
            'superpowers' =>  SuperPowers::flags([SuperPowers::Strength, SuperPowers::Immortality])
        ]);

        $this->assertEquals(2, ModelWithFlaggedQueries::query()->hasFlag('superpowers', SuperPowers::Immortality)->count());
        $this->assertEquals(1, ModelWithFlaggedQueries::query()->hasFlag('superpowers', SuperPowers::Flight)->count());
        $this->assertEquals(0, ModelWithFlaggedQueries::query()->hasFlag('superpowers', SuperPowers::Invisibility)->count());

        $this->assertEquals(0, ModelWithFlaggedQueries::query()->notHasFlag('superpowers', SuperPowers::Immortality)->count());
        $this->assertEquals(1, ModelWithFlaggedQueries::query()->notHasFlag('superpowers', SuperPowers::Flight)->count());
        $this->assertEquals(2, ModelWithFlaggedQueries::query()->notHasFlag('superpowers', SuperPowers::Invisibility)->count());

        $this->assertEquals(0, ModelWithFlaggedQueries::query()->hasAllFlags('superpowers', [SuperPowers::Strength, SuperPowers::Flight])->count());
        $this->assertEquals(1, ModelWithFlaggedQueries::query()->hasAllFlags('superpowers', [SuperPowers::Immortality, SuperPowers::Flight])->count());
        $this->assertEquals(2, ModelWithFlaggedQueries::query()->hasAllFlags('superpowers', [SuperPowers::Immortality])->count());

        $this->assertEquals(2, ModelWithFlaggedQueries::query()->hasAnyFlags('superpowers', [SuperPowers::Strength, SuperPowers::Flight])->count());
        $this->assertEquals(1, ModelWithFlaggedQueries::query()->hasAnyFlags('superpowers', [SuperPowers::Invisibility, SuperPowers::Flight])->count());
        $this->assertEquals(2, ModelWithFlaggedQueries::query()->hasAnyFlags('superpowers', [SuperPowers::Immortality])->count());
    }
}
