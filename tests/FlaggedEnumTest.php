<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Tests\Enums\SuperPowers;
use PHPUnit\Framework\TestCase;

final class FlaggedEnumTest extends TestCase
{
    public function testCanConstructFlaggedEnumUsingStaticProperties(): void
    {
        new SuperPowers([SuperPowers::Strength, SuperPowers::Flight, SuperPowers::LaserVision]);
        SuperPowers::fromValue([SuperPowers::Strength, SuperPowers::Flight, SuperPowers::LaserVision]);
        SuperPowers::flags([SuperPowers::Strength, SuperPowers::Flight, SuperPowers::LaserVision]);

        $this->expectNotToPerformAssertions();
    }

    public function testCanConstructFlaggedEnumUsingInstances(): void
    {
        new SuperPowers([SuperPowers::Strength(), SuperPowers::Flight(), SuperPowers::LaserVision()]);
        SuperPowers::fromValue([SuperPowers::Strength(), SuperPowers::Flight(), SuperPowers::LaserVision()]);
        SuperPowers::flags([SuperPowers::Strength(), SuperPowers::Flight(), SuperPowers::LaserVision()]);

        $this->expectNotToPerformAssertions();
    }

    public function testCanCheckIfInstanceHasFlag(): void
    {
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight]);

        $this->assertTrue($powers->hasFlag(SuperPowers::Strength()));
        $this->assertTrue($powers->hasFlag(SuperPowers::Strength));
        $this->assertFalse($powers->hasFlag(SuperPowers::LaserVision()));
        $this->assertFalse($powers->hasFlag(SuperPowers::LaserVision));
    }

    public function testCanCheckIfInstanceHasFlags(): void
    {
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight]);

        $this->assertTrue($powers->hasFlags([SuperPowers::Strength, SuperPowers::Flight]));
        $this->assertFalse($powers->hasFlags([SuperPowers::Strength, SuperPowers::Invisibility]));
    }

    public function testCanCheckIfInstanceDoesNotHaveFlag(): void
    {
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight]);

        $this->assertTrue($powers->notHasFlag(SuperPowers::LaserVision()));
        $this->assertTrue($powers->notHasFlag(SuperPowers::LaserVision));
        $this->assertFalse($powers->notHasFlag(SuperPowers::Strength()));
        $this->assertFalse($powers->notHasFlag(SuperPowers::Strength));
    }

    public function testCanCheckIfInstanceDoesNotHaveFlags(): void
    {
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight]);

        $this->assertTrue($powers->notHasFlags([SuperPowers::Invisibility, SuperPowers::LaserVision]));
        $this->assertFalse($powers->notHasFlags([SuperPowers::Strength, SuperPowers::LaserVision]));
        $this->assertFalse($powers->notHasFlags([SuperPowers::Strength, SuperPowers::Flight]));
    }

    public function testCanSetFlags(): void
    {
        $powers = SuperPowers::None();
        $this->assertFalse($powers->hasFlag(SuperPowers::LaserVision));

        $powers->setFlags([SuperPowers::LaserVision, SuperPowers::Strength]);
        $this->assertTrue($powers->hasFlag(SuperPowers::LaserVision));
        $this->assertTrue($powers->hasFlag(SuperPowers::Strength));
    }

    public function testCanAddFlag(): void
    {
        $powers = SuperPowers::None();
        $this->assertFalse($powers->hasFlag(SuperPowers::LaserVision));

        $powers->addFlag(SuperPowers::LaserVision);
        $this->assertTrue($powers->hasFlag(SuperPowers::LaserVision));

        $powers->addFlag(SuperPowers::Strength);
        $this->assertTrue($powers->hasFlag(SuperPowers::Strength));
    }

    public function testCanAddFlags(): void
    {
        $powers = SuperPowers::None();
        $this->assertFalse($powers->hasFlag(SuperPowers::LaserVision));

        $powers->addFlags([SuperPowers::LaserVision, SuperPowers::Strength]);
        $this->assertTrue($powers->hasFlags([SuperPowers::LaserVision, SuperPowers::Strength]));
    }

    public function testCanRemoveFlag(): void
    {
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight]);
        $this->assertTrue($powers->hasFlags([SuperPowers::Strength, SuperPowers::Flight]));

        $powers->removeFlag(SuperPowers::Strength);
        $this->assertFalse($powers->hasFlag(SuperPowers::Strength));

        $powers->removeFlag(SuperPowers::Flight);
        $this->assertFalse($powers->hasFlag(SuperPowers::Flight));

        $this->assertTrue($powers->is(SuperPowers::None));
    }

    public function testCanRemoveFlags(): void
    {
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight]);
        $this->assertTrue($powers->hasFlags([SuperPowers::Strength, SuperPowers::Flight]));

        $powers->removeFlags([SuperPowers::Strength, SuperPowers::Flight]);
        $this->assertFalse($powers->hasFlags([SuperPowers::Strength, SuperPowers::Flight]));

        $this->assertTrue($powers->is(SuperPowers::None));
    }

    public function testCanGetFlags(): void
    {
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight, SuperPowers::Invisibility]);
        $flags = $powers->getFlags();

        $this->assertCount(3, $flags);
        $this->assertContainsOnlyInstancesOf(SuperPowers::class, $flags);
    }

    public function testCanSetShortcutValues(): void
    {
        $powers = new SuperPowers(SuperPowers::Superman);

        $this->assertTrue($powers->hasFlag(SuperPowers::Strength));
        $this->assertTrue($powers->hasFlag(SuperPowers::LaserVision));
        $this->assertFalse($powers->hasFlag(SuperPowers::Invisibility));
    }

    public function testShortcutValuesAreComparableToExplicitSet(): void
    {
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::LaserVision, SuperPowers::Flight]);
        $this->assertTrue($powers->hasFlag(SuperPowers::Superman));

        $powers->removeFlag(SuperPowers::LaserVision);
        $this->assertFalse($powers->hasFlag(SuperPowers::Superman));
    }

    public function testCanCheckIfInstanceHasMultipleFlagsSet(): void
    {
        $this->assertTrue(SuperPowers::Superman()->hasMultipleFlags());
        $this->assertFalse(SuperPowers::Strength()->hasMultipleFlags());
        $this->assertFalse(SuperPowers::None()->hasMultipleFlags());
    }

    public function testCanGetBitmaskForAnInstance(): void
    {
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight]);
        $this->assertSame(1001, $powers->getBitmask());

        $this->assertSame(1101, SuperPowers::Superman()->getBitmask());
    }

    public function testCanInstantiateAFlaggedEnumFromAValueWhichHasMultipleFlagsSet(): void
    {
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight]);

        $this->assertEquals($powers, SuperPowers::fromValue($powers->value));
    }

    public function testCanAddAllFlagsToAnEnum(): void
    {
        $powers = new SuperPowers([
            SuperPowers::Flight,
            SuperPowers::Invisibility,
            SuperPowers::LaserVision,
            SuperPowers::Strength,
            SuperPowers::Teleportation,
            SuperPowers::Immortality,
            SuperPowers::TimeTravel,
        ]);

        $this->assertEquals($powers, (new SuperPowers())->addAllFlags());
    }

    public function testCanRemoveAllFlagsFromAnEnum(): void
    {
        $powers = new SuperPowers([
            SuperPowers::Flight,
            SuperPowers::Invisibility,
        ]);

        $this->assertEquals(SuperPowers::None(), $powers->removeAllFlags());
    }
}
