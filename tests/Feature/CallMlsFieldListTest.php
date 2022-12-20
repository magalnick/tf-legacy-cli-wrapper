<?php
/**
 * DMagalnick
 * 2022-10-11
 * I've spent *many* hours trying to get various types of tests to work.
 *
 * Apparently since this is testing an artisan command, snagging the output doesn't work
 * quite the same way as running standard web/route based calls.
 *
 * All attempts to grab the CLI STDOUT output has failed. I've tried various built-in methods
 * that artisan provides. I've tried calling artisan in a few different ways.
 * I even tried buffering the output. No matter what, the result of the CLI output that's
 * available to the test is an empty string, which makes it hard to do any unit tests on output,
 * mocked or otherwise.
 *
 * While the output isn't available, the exit code is. So I'm writing a few basic tests to
 * compare exit code on custom functions in the main class that exist to verify testing.
 *
 * While this is not ideal, at least it helps verify that the main class is being hit as intended.
 */

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Utilities\FunWithText;

class CallMlsFieldListTest extends TestCase
{
    use WithFaker;

    /**
     * Test that the calling class is allowed to make this call
     *
     * @test
     * @dataProvider specificCommandProvider
     * @param $specific_command
     * @return void
     */
    public function it_checks_the_calling_class_is_allowed($specific_command): void
    {
        $mls                    = $this->faker->uuid;
        $state                  = 'CA';
        $property_type          = 'SFR';
        $special_field_modifier = 2;
        $north_star_new         = false;
        $test                   = 'test-calling-class-is-allowed';
        $command                = $this->getCommand($specific_command, $mls, $state, $property_type, $special_field_modifier, $north_star_new, $test);
        $this
            ->artisan($command)
            ->assertExitCode(0);
    }

    /**
     * Test that a fake calling class is not allowed to make this call
     *
     * @test
     * @dataProvider specificCommandProvider
     * @param $specific_command
     * @return void
     */
    public function it_checks_a_fake_calling_class_is_not_allowed($specific_command): void
    {
        $mls                    = $this->faker->uuid;
        $state                  = 'CA';
        $property_type          = 'SFR';
        $special_field_modifier = 2;
        $north_star_new         = false;
        $test                   = 'test-fake-calling-class-is-not-allowed';
        $command                = $this->getCommand($specific_command, $mls, $state, $property_type, $special_field_modifier, $north_star_new, $test);
        $this
            ->artisan($command)
            ->assertExitCode(403);
    }

    /**
     * Test the MLS value is received properly by the CLI function
     *
     * @test
     * @dataProvider specificCommandProvider
     * @param $specific_command
     * @return void
     */
    public function it_checks_the_mls_value($specific_command): void
    {
        $mls                    = $this->faker->uuid;
        $state                  = 'CA';
        $property_type          = 'SFR';
        $special_field_modifier = 2;
        $north_star_new         = false;
        $test                   = 'test-mls';
        $command                = $this->getCommand($specific_command, $mls, $state, $property_type, $special_field_modifier, $north_star_new, $test);
        $this
            ->artisan($command)
            ->assertExitCode(FunWithText::integersInStringAsInt(
                substr($mls, 0, 20)
            ));
    }

    /**
     * Test the MLS value is in the list of valid MLSes
     *
     * @test
     * @dataProvider specificCommandProvider
     * @param $specific_command
     * @return void
     */
    public function it_checks_the_mls_value_is_valid($specific_command): void
    {
        $mls                    = 'SanDiegoMLS_Paragon';
        $state                  = 'CA';
        $property_type          = 'SFR';
        $special_field_modifier = 2;
        $north_star_new         = false;
        $test                   = 'test-mls-is-valid';
        $command                = $this->getCommand($specific_command, $mls, $state, $property_type, $special_field_modifier, $north_star_new, $test);
        $this
            ->artisan($command)
            ->assertExitCode(FunWithText::integersInStringAsInt(
                substr(md5($mls), 0, 20)
            ));
    }

    /**
     * Test the MLS value is not in the list of valid MLSes
     *
     * @test
     * @dataProvider specificCommandProvider
     * @param $specific_command
     * @return void
     */
    public function it_checks_the_mls_value_is_invalid($specific_command): void
    {
        $mls                    = $this->faker->uuid;
        $state                  = 'CA';
        $property_type          = 'SFR';
        $special_field_modifier = 2;
        $north_star_new         = false;
        $test                   = 'test-mls-is-valid';
        $command                = $this->getCommand($specific_command, $mls, $state, $property_type, $special_field_modifier, $north_star_new, $test);
        $this
            ->artisan($command)
            ->assertExitCode(400);
    }

    /**
     * Test the state value is received properly by the CLI function
     *
     * @test
     * @dataProvider specificCommandProvider
     * @param $specific_command
     * @return void
     */
    public function it_checks_the_state_value($specific_command): void
    {
        $mls                    = $this->faker->uuid;
        $state                  = 'CA';
        $property_type          = 'SFR';
        $special_field_modifier = 2;
        $north_star_new         = false;
        $test                   = 'test-state';
        $command                = $this->getCommand($specific_command, $mls, $state, $property_type, $special_field_modifier, $north_star_new, $test);
        $this
            ->artisan($command)
            ->assertExitCode(FunWithText::integersInStringAsInt(md5($state)));
    }

    /**
     * Test the state value is in the list of valid states
     *
     * @test
     * @dataProvider specificCommandProvider
     * @param $specific_command
     * @return void
     */
    public function it_checks_the_state_value_is_valid($specific_command): void
    {
        $mls                    = $this->faker->uuid;
        $state                  = 'CA';
        $property_type          = 'SFR';
        $special_field_modifier = 2;
        $north_star_new         = false;
        $test                   = 'test-state-is-valid';
        $command                = $this->getCommand($specific_command, $mls, $state, $property_type, $special_field_modifier, $north_star_new, $test);
        $this
            ->artisan($command)
            ->assertExitCode(FunWithText::integersInStringAsInt(md5($state)));
    }

    /**
     * Test the state value is not in the list of valid states
     *
     * @test
     * @dataProvider specificCommandProvider
     * @param $specific_command
     * @return void
     */
    public function it_checks_the_state_value_is_invalid($specific_command): void
    {
        $mls                    = $this->faker->uuid;
        $state                  = $this->faker->md5;
        $property_type          = 'SFR';
        $special_field_modifier = 2;
        $north_star_new         = false;
        $test                   = 'test-state-is-valid';
        $command                = $this->getCommand($specific_command, $mls, $state, $property_type, $special_field_modifier, $north_star_new, $test);
        $this
            ->artisan($command)
            ->assertExitCode(400);
    }

    /**
     * Test the MLS is available for the specified state
     *
     * @test
     * @dataProvider specificCommandProvider
     * @param $specific_command
     * @return void
     */
    public function it_checks_the_mls_is_available_in_that_state($specific_command): void
    {
        $mls                    = 'SanDiegoMLS_Paragon';
        $state                  = 'CA';
        $property_type          = 'SFR';
        $special_field_modifier = 2;
        $north_star_new         = false;
        $test                   = 'test-mls-state-combination';
        $command                = $this->getCommand($specific_command, $mls, $state, $property_type, $special_field_modifier, $north_star_new, $test);
        $this
            ->artisan($command)
            ->assertExitCode(FunWithText::integersInStringAsInt(md5($mls . $state)));
    }

    /**
     * Test the MLS is not available for the specified state
     *
     * @test
     * @dataProvider specificCommandProvider
     * @param $specific_command
     * @return void
     */
    public function it_checks_the_mls_is_not_available_in_that_state($specific_command): void
    {
        $mls                    = 'SanDiegoMLS_Paragon';
        $state                  = 'CO';
        $property_type          = 'SFR';
        $special_field_modifier = 2;
        $north_star_new         = false;
        $test                   = 'test-mls-state-combination';
        $command                = $this->getCommand($specific_command, $mls, $state, $property_type, $special_field_modifier, $north_star_new, $test);
        $this
            ->artisan($command)
            ->assertExitCode(400);
    }

    /**
     * Test the property type value is received properly by the CLI function
     *
     * @test
     * @dataProvider specificCommandProvider
     * @param $specific_command
     * @return void
     */
    public function it_checks_the_property_type_value($specific_command): void
    {
        $mls                    = $this->faker->uuid;
        $state                  = 'CA';
        $property_type          = 'SFR';
        $special_field_modifier = 2;
        $north_star_new         = false;
        $test                   = 'test-property-type';
        $command                = $this->getCommand($specific_command, $mls, $state, $property_type, $special_field_modifier, $north_star_new, $test);
        $this
            ->artisan($command)
            ->assertExitCode(FunWithText::integersInStringAsInt(md5($property_type)));
    }

    /**
     * Test the property type value is in the list of valid property types
     *
     * @test
     * @dataProvider specificCommandProvider
     * @param $specific_command
     * @return void
     */
    public function it_checks_the_property_type_value_is_valid($specific_command): void
    {
        $mls                    = $this->faker->uuid;
        $state                  = 'CA';
        $property_type          = 'SFR';
        $special_field_modifier = 2;
        $north_star_new         = false;
        $test                   = 'test-property-type-is-valid';
        $command                = $this->getCommand($specific_command, $mls, $state, $property_type, $special_field_modifier, $north_star_new, $test);
        $this
            ->artisan($command)
            ->assertExitCode(FunWithText::integersInStringAsInt(md5($property_type)));
    }

    /**
     * Test the property type value is not in the list of valid property types
     *
     * @test
     * @dataProvider specificCommandProvider
     * @param $specific_command
     * @return void
     */
    public function it_checks_the_property_type_value_is_invalid($specific_command): void
    {
        $mls                    = $this->faker->uuid;
        $state                  = 'CA';
        $property_type          = $this->faker->md5;
        $special_field_modifier = 2;
        $north_star_new         = false;
        $test                   = 'test-property-type-is-valid';
        $command                = $this->getCommand($specific_command, $mls, $state, $property_type, $special_field_modifier, $north_star_new, $test);
        $this
            ->artisan($command)
            ->assertExitCode(400);
    }

    /**
     * Test the special-field-modifier flag is not set, so it will be null
     *
     * @test
     * @dataProvider specificCommandProvider
     * @param $specific_command
     * @return void
     */
    public function it_checks_the_special_field_modifier_is_null($specific_command): void
    {
        $mls                    = $this->faker->uuid;
        $state                  = 'CA';
        $property_type          = 'SFR';
        $special_field_modifier = null;
        $north_star_new         = true;
        $test                   = 'test-special-field-modifier-is-valid';
        $command                = $this->getCommand($specific_command, $mls, $state, $property_type, $special_field_modifier, $north_star_new, $test);
        $this
            ->artisan($command)
            ->assertExitCode(-1);
    }

    /**
     * Test the special-field-modifier flag is set and valid
     *
     * @test
     * @dataProvider specificCommandProvider
     * @param $specific_command
     * @return void
     */
    public function it_checks_the_special_field_modifier_is_a_valid_integer($specific_command): void
    {
        $mls                    = $this->faker->uuid;
        $state                  = 'CA';
        $property_type          = 'SFR';
        $special_field_modifier = 1;
        $north_star_new         = true;
        $test                   = 'test-special-field-modifier-is-valid';
        $command                = $this->getCommand($specific_command, $mls, $state, $property_type, $special_field_modifier, $north_star_new, $test);
        $this
            ->artisan($command)
            ->assertExitCode($special_field_modifier);
    }

    /**
     * Test the special-field-modifier flag is set and invalid
     *
     * @test
     * @dataProvider specificCommandProvider
     * @param $specific_command
     * @return void
     */
    public function it_checks_the_special_field_modifier_is_an_invalid_integer($specific_command): void
    {
        $mls                    = $this->faker->uuid;
        $state                  = 'CA';
        $property_type          = 'SFR';
        $special_field_modifier = 3;
        $north_star_new         = true;
        $test                   = 'test-special-field-modifier-is-valid';
        $command                = $this->getCommand($specific_command, $mls, $state, $property_type, $special_field_modifier, $north_star_new, $test);
        $this
            ->artisan($command)
            ->assertExitCode(400);
    }

    /**
     * Test the north-star-new flag is false
     *
     * @test
     * @dataProvider specificCommandProvider
     * @param $specific_command
     * @return void
     */
    public function it_checks_that_north_star_new_is_false($specific_command): void
    {
        $mls                    = $this->faker->uuid;
        $state                  = 'CA';
        $property_type          = 'SFR';
        $special_field_modifier = 2;
        $north_star_new         = false;
        $test                   = 'test-north-star-new-is-false';
        $command                = $this->getCommand($specific_command, $mls, $state, $property_type, $special_field_modifier, $north_star_new, $test);
        $this
            ->artisan($command)
            ->assertExitCode(
                FunWithText::integersInStringAsInt(md5($north_star_new))
            );
    }

    /**
     * Test the north-star-new flag is set with the right MLS
     *
     * @test
     * @dataProvider specificCommandProvider
     * @param $specific_command
     * @return void
     */
    public function it_checks_that_north_star_new_is_set_with_the_right_mls($specific_command): void
    {
        $mls                    = 'Northstar';
        $state                  = 'CA';
        $property_type          = 'SFR';
        $special_field_modifier = 2;
        $north_star_new         = true;
        $test                   = 'test-north-star-new-is-valid';
        $command                = $this->getCommand($specific_command, $mls, $state, $property_type, $special_field_modifier, $north_star_new, $test);
        $this
            ->artisan($command)
            ->assertExitCode(
                FunWithText::integersInStringAsInt(
                    substr(
                        md5(
                            md5($mls) . md5($north_star_new)
                        ), 0, 16
                    )
                )
            );
    }

    /**
     * Test the north-star-new flag is set, but with the wrong MLS
     *
     * @test
     * @dataProvider specificCommandProvider
     * @param $specific_command
     * @return void
     */
    public function it_checks_that_north_star_new_is_set_with_the_wrong_mls($specific_command): void
    {
        $mls                    = $this->faker->uuid;
        $state                  = 'CA';
        $property_type          = 'SFR';
        $special_field_modifier = 2;
        $north_star_new         = true;
        $test                   = 'test-north-star-new-is-valid';
        $command                = $this->getCommand($specific_command, $mls, $state, $property_type, $special_field_modifier, $north_star_new, $test);
        $this
            ->artisan($command)
            ->assertExitCode(400);
    }

    /**
     * Test that the legacy array file exists on the server as a real file
     *
     * @test
     * @dataProvider specificCommandProvider
     * @param $specific_command
     * @return void
     */
    public function it_checks_that_the_legacy_array_file_exists_on_the_server($specific_command): void
    {
        $mls                    = $this->faker->uuid;
        $state                  = 'CA';
        $property_type          = 'SFR';
        $special_field_modifier = 2;
        $north_star_new         = true;
        $test                   = 'test-legacy-array-file-exists-on-server';
        $command                = $this->getCommand($specific_command, $mls, $state, $property_type, $special_field_modifier, $north_star_new, $test);
        $this
            ->artisan($command)
            ->assertExitCode(0);
    }

    /**
     * Test that the legacy array file function exists for SFR
     *
     * @test
     * @dataProvider specificCommandProvider
     * @param $specific_command
     * @return void
     */
    public function it_checks_that_the_legacy_array_function_exists_for_sfr($specific_command): void
    {
        $mls                    = 'IRES';
        $state                  = 'CO';
        $property_type          = 'SFR';
        $special_field_modifier = null;
        $north_star_new         = false;
        $test                   = 'test-legacy-array-function-exists';
        $command                = $this->getCommand($specific_command, $mls, $state, $property_type, $special_field_modifier, $north_star_new, $test);
        $this
            ->artisan($command)
            ->assertExitCode(0);
    }

    /**
     * Test that the legacy array file function exists for Multi
     *
     * @test
     * @dataProvider specificCommandProvider
     * @param $specific_command
     * @return void
     */
    public function it_checks_that_the_legacy_array_function_exists_for_multi($specific_command): void
    {
        $mls                    = 'IRES';
        $state                  = 'CO';
        $property_type          = 'Multi';
        $special_field_modifier = null;
        $north_star_new         = false;
        $test                   = 'test-legacy-array-function-exists';
        $command                = $this->getCommand($specific_command, $mls, $state, $property_type, $special_field_modifier, $north_star_new, $test);
        $this
            ->artisan($command)
            ->assertExitCode(0);
    }

    /**
     * Test all legacy array file functions for errors
     *
     * @test
     * @dataProvider specificCommandProvider
     * @param $specific_command
     * @return void
     */
    public function it_checks_all_legacy_array_functions_for_errors($specific_command): void
    {
        $mls                    = $this->faker->uuid;
        $state                  = 'CA';
        $property_type          = $this->faker->md5;
        $special_field_modifier = null;
        $north_star_new         = false;
        $test                   = 'test-all-legacy-array-functions';
        $command                = $this->getCommand($specific_command, $mls, $state, $property_type, $special_field_modifier, $north_star_new, $test);
        $this
            ->artisan($command)
            ->assertExitCode(1);
    }

    /**
     * @return array
     */
    public function specificCommandProvider(): array
    {
        return [
            ['required'],
            ['reordered'],
        ];
    }

    /**
     * @param string $specific_command
     * @param string $mls
     * @param string $state
     * @param string $property_type
     * @param mixed $special_field_modifier
     * @param bool $north_star_new
     * @param string $test
     * @return string
     */
    private function getCommand(string $specific_command, string $mls, string $state, string $property_type, mixed $special_field_modifier, bool $north_star_new, string $test = ''): string
    {
        $mls                    = FunWithText::safeString($mls);
        $state                  = FunWithText::safeString($state);
        $property_type          = FunWithText::safeString($property_type);
        $special_field_modifier = is_numeric($special_field_modifier) ? (int) $special_field_modifier : null;
        $test                   = FunWithText::safeString($test);
        $command                = "cli:call-mls-$specific_command-field-list $mls $state $property_type";

        if (!is_null($special_field_modifier)) {
            $command .= " --special-field-modifier=$special_field_modifier";
        }
        if ($north_star_new) {
            $command .= ' --north-star-new';
        }
        if ($test != '') {
            $command .= " -t $test";
        }

        return $command;
    }
}
