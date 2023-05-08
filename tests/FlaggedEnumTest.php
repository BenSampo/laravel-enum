<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use PHPUnit\Framework\TestCase;
use BenSampo\Enum\Tests\Enums\SuperPowers;

final class FlaggedEnumTest extends TestCase
{
    public function test_can_construct_flagged_enum_using_static_properties(): void
    {
        new SuperPowers([SuperPowers::Strength, SuperPowers::Flight, SuperPowers::LaserVision]);
        SuperPowers::fromValue([SuperPowers::Strength, SuperPowers::Flight, SuperPowers::LaserVision]);
        SuperPowers::flags([SuperPowers::Strength, SuperPowers::Flight, SuperPowers::LaserVision]);

        $this->expectNotToPerformAssertions();
    }

    public function test_can_construct_flagged_enum_using_instances(): void
    {
        new SuperPowers([SuperPowers::Strength(), SuperPowers::Flight(), SuperPowers::LaserVision()]);
        SuperPowers::fromValue([SuperPowers::Strength(), SuperPowers::Flight(), SuperPowers::LaserVision()]);
        SuperPowers::flags([SuperPowers::Strength(), SuperPowers::Flight(), SuperPowers::LaserVision()]);

        $this->expectNotToPerformAssertions();
    }

    public function test_can_check_if_instance_has_flag(): void
    {
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight]);

        $this->assertTrue($powers->hasFlag(SuperPowers::Strength()));
        $this->assertTrue($powers->hasFlag(SuperPowers::Strength));
        $this->assertFalse($powers->hasFlag(SuperPowers::LaserVision()));
        $this->assertFalse($powers->hasFlag(SuperPowers::LaserVision));
    }

    public function test_can_check_if_instance_has_flags(): void
    {
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight]);

        $this->assertTrue($powers->hasFlags([SuperPowers::Strength, SuperPowers::Flight]));
        $this->assertFalse($powers->hasFlags([SuperPowers::Strength, SuperPowers::Invisibility]));
    }

    public function test_can_check_if_instance_does_not_have_flag(): void
    {
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight]);

        $this->assertTrue($powers->notHasFlag(SuperPowers::LaserVision()));
        $this->assertTrue($powers->notHasFlag(SuperPowers::LaserVision));
        $this->assertFalse($powers->notHasFlag(SuperPowers::Strength()));
        $this->assertFalse($powers->notHasFlag(SuperPowers::Strength));
    }

    public function test_can_check_if_instance_does_not_have_flags(): void
    {
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight]);

        $this->assertTrue($powers->notHasFlags([SuperPowers::Invisibility, SuperPowers::LaserVision]));
        $this->assertFalse($powers->notHasFlags([SuperPowers::Strength, SuperPowers::LaserVision]));
        $this->assertFalse($powers->notHasFlags([SuperPowers::Strength, SuperPowers::Flight]));
    }

    public function test_can_set_flags(): void
    {
        $powers = SuperPowers::None();
        $this->assertFalse($powers->hasFlag(SuperPowers::LaserVision));

        $powers->setFlags([SuperPowers::LaserVision, SuperPowers::Strength]);
        $this->assertTrue($powers->hasFlag(SuperPowers::LaserVision));
        $this->assertTrue($powers->hasFlag(SuperPowers::Strength));
    }

    public function test_can_add_flag(): void
    {
        $powers = SuperPowers::None();
        $this->assertFalse($powers->hasFlag(SuperPowers::LaserVision));

        $powers->addFlag(SuperPowers::LaserVision);
        $this->assertTrue($powers->hasFlag(SuperPowers::LaserVision));

        $powers->addFlag(SuperPowers::Strength);
        $this->assertTrue($powers->hasFlag(SuperPowers::Strength));
    }

    public function test_can_add_flags(): void
    {
        $powers = SuperPowers::None();
        $this->assertFalse($powers->hasFlag(SuperPowers::LaserVision));

        $powers->addFlags([SuperPowers::LaserVision, SuperPowers::Strength]);
        $this->assertTrue($powers->hasFlags([SuperPowers::LaserVision, SuperPowers::Strength]));
    }

    public function test_can_remove_flag(): void
    {
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight]);
        $this->assertTrue($powers->hasFlags([SuperPowers::Strength, SuperPowers::Flight]));

        $powers->removeFlag(SuperPowers::Strength);
        $this->assertFalse($powers->hasFlag(SuperPowers::Strength));

        $powers->removeFlag(SuperPowers::Flight);
        $this->assertFalse($powers->hasFlag(SuperPowers::Flight));

        $this->assertTrue($powers->is(SuperPowers::None));
    }

    public function test_can_remove_flags(): void
    {
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight]);
        $this->assertTrue($powers->hasFlags([SuperPowers::Strength, SuperPowers::Flight]));

        $powers->removeFlags([SuperPowers::Strength, SuperPowers::Flight]);
        $this->assertFalse($powers->hasFlags([SuperPowers::Strength, SuperPowers::Flight]));

        $this->assertTrue($powers->is(SuperPowers::None));
    }

    public function test_can_get_flags(): void
    {
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight, SuperPowers::Invisibility]);
        $flags = $powers->getFlags();

        $this->assertCount(3, $flags);
        $this->assertContainsOnlyInstancesOf(SuperPowers::class, $flags);
    }

    public function test_can_set_shortcut_values(): void
    {
        $powers = new SuperPowers(SuperPowers::Superman);

        $this->assertTrue($powers->hasFlag(SuperPowers::Strength));
        $this->assertTrue($powers->hasFlag(SuperPowers::LaserVision));
        $this->assertFalse($powers->hasFlag(SuperPowers::Invisibility));
    }

    public function test_shortcut_values_are_comparable_to_explicit_set(): void
    {
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::LaserVision, SuperPowers::Flight]);
        $this->assertTrue($powers->hasFlag(SuperPowers::Superman));

        $powers->removeFlag(SuperPowers::LaserVision);
        $this->assertFalse($powers->hasFlag(SuperPowers::Superman));
    }

    public function test_can_check_if_instance_has_multiple_flags_set(): void
    {
        $this->assertTrue(SuperPowers::Superman()->hasMultipleFlags());
        $this->assertFalse(SuperPowers::Strength()->hasMultipleFlags());
        $this->assertFalse(SuperPowers::None()->hasMultipleFlags());
    }

    public function test_can_get_bitmask_for_an_instance(): void
    {
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight]);
        $this->assertSame(1001, $powers->getBitmask());

        $this->assertSame(1101, SuperPowers::Superman()->getBitmask());
    }

    public function test_can_instantiate_a_flagged_enum_from_a_value_which_has_multiple_flags_set(): void
    {
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight]);

        $this->assertEquals($powers, SuperPowers::fromValue($powers->value));
    }

    public function test_can_add_all_flags_to_an_enum(): void
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

        $this->assertEquals($powers, (new SuperPowers)->addAllFlags());
    }

    public function test_can_remove_all_flags_from_an_enum(): void
    {
        $powers = new SuperPowers([
            SuperPowers::Flight,
            SuperPowers::Invisibility,
        ]);

        $this->assertEquals(SuperPowers::None(), $powers->removeAllFlags());
    }
}
