<?php
/**
 * DMagalnick
 * 2022-08-11
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

class CallMlsEngineTest extends TestCase
{
    use WithFaker;

    /**
     * Test the MLS value is received properly by the CLI function
     *
     * @test
     * @return void
     */
    public function it_checks_the_mls_value(): void
    {
        $mls            = $this->faker->uuid;
        $state          = 'CA';
        $property_type  = 'SFR';
        $effective_date = date('Y-m-d');
        $mls_file       = "{$this->faker->md5}.json";
        $command        = $this->getCommand($mls, $state, $property_type, $effective_date, $mls_file, false, '', 'test-mls');
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
     * @return void
     */
    public function it_checks_the_mls_value_is_valid(): void
    {
        $mls            = 'SanDiegoMLS_Paragon';
        $state          = 'CA';
        $property_type  = 'SFR';
        $effective_date = date('Y-m-d');
        $mls_file       = "{$this->faker->md5}.json";
        $command        = $this->getCommand($mls, $state, $property_type, $effective_date, $mls_file, false, '', 'test-mls-is-valid');
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
     * @return void
     */
    public function it_checks_the_mls_value_is_invalid(): void
    {
        $mls            = $this->faker->uuid;
        $state          = 'CA';
        $property_type  = 'SFR';
        $effective_date = date('Y-m-d');
        $mls_file       = "{$this->faker->md5}.json";
        $command        = $this->getCommand($mls, $state, $property_type, $effective_date, $mls_file, false, '', 'test-mls-is-valid');
        $this
            ->artisan($command)
            ->assertExitCode(400);
    }

    /**
     * Test the state value is received properly by the CLI function
     *
     * @test
     * @return void
     */
    public function it_checks_the_state_value(): void
    {
        $mls            = $this->faker->uuid;
        $state          = 'CA';
        $property_type  = 'SFR';
        $effective_date = date('Y-m-d');
        $mls_file       = "{$this->faker->md5}.json";
        $command        = $this->getCommand($mls, $state, $property_type, $effective_date, $mls_file, false, '', 'test-state');
        $this
            ->artisan($command)
            ->assertExitCode(FunWithText::integersInStringAsInt(md5($state)));
    }

    /**
     * Test the state value is in the list of valid states
     *
     * @test
     * @return void
     */
    public function it_checks_the_state_value_is_valid(): void
    {
        $mls            = $this->faker->uuid;
        $state          = 'CA';
        $property_type  = 'SFR';
        $effective_date = date('Y-m-d');
        $mls_file       = "{$this->faker->md5}.json";
        $command        = $this->getCommand($mls, $state, $property_type, $effective_date, $mls_file, false, '', 'test-state-is-valid');
        $this
            ->artisan($command)
            ->assertExitCode(FunWithText::integersInStringAsInt(md5($state)));
    }

    /**
     * Test the state value is not in the list of valid states
     *
     * @test
     * @return void
     */
    public function it_checks_the_state_value_is_invalid(): void
    {
        $mls            = $this->faker->uuid;
        $state          = $this->faker->md5;
        $property_type  = 'SFR';
        $effective_date = date('Y-m-d');
        $mls_file       = "{$this->faker->md5}.json";
        $command        = $this->getCommand($mls, $state, $property_type, $effective_date, $mls_file, false, '', 'test-state-is-valid');
        $this
            ->artisan($command)
            ->assertExitCode(400);
    }

    /**
     * Test the MLS is available for the specified state
     *
     * @test
     * @return void
     */
    public function it_checks_the_mls_is_available_in_that_state(): void
    {
        $mls            = 'SanDiegoMLS_Paragon';
        $state          = 'CA';
        $property_type  = 'SFR';
        $effective_date = date('Y-m-d');
        $mls_file       = "{$this->faker->md5}.json";
        $command        = $this->getCommand($mls, $state, $property_type, $effective_date, $mls_file, false, '', 'test-mls-state-combination');
        $this
            ->artisan($command)
            ->assertExitCode(FunWithText::integersInStringAsInt(md5($mls . $state)));
    }

    /**
     * Test the MLS is not available for the specified state
     *
     * @test
     * @return void
     */
    public function it_checks_the_mls_is_not_available_in_that_state(): void
    {
        $mls            = 'SanDiegoMLS_Paragon';
        $state          = 'CO';
        $property_type  = 'SFR';
        $effective_date = date('Y-m-d');
        $mls_file       = "{$this->faker->md5}.json";
        $command        = $this->getCommand($mls, $state, $property_type, $effective_date, $mls_file, false, '', 'test-mls-state-combination');
        $this
            ->artisan($command)
            ->assertExitCode(400);
    }

    /**
     * Test the property type value is received properly by the CLI function
     *
     * @test
     * @return void
     */
    public function it_checks_the_property_type_value(): void
    {
        $mls            = $this->faker->uuid;
        $state          = 'CA';
        $property_type  = 'SFR';
        $effective_date = date('Y-m-d');
        $mls_file       = "{$this->faker->md5}.json";
        $command        = $this->getCommand($mls, $state, $property_type, $effective_date, $mls_file, false, '', 'test-property-type');
        $this
            ->artisan($command)
            ->assertExitCode(FunWithText::integersInStringAsInt(md5($property_type)));
    }

    /**
     * Test the property type value is in the list of valid property types
     *
     * @test
     * @return void
     */
    public function it_checks_the_property_type_value_is_valid(): void
    {
        $mls            = $this->faker->uuid;
        $state          = 'CA';
        $property_type  = 'SFR';
        $effective_date = date('Y-m-d');
        $mls_file       = "{$this->faker->md5}.json";
        $command        = $this->getCommand($mls, $state, $property_type, $effective_date, $mls_file, false, '', 'test-property-type-is-valid');
        $this
            ->artisan($command)
            ->assertExitCode(FunWithText::integersInStringAsInt(md5($property_type)));
    }

    /**
     * Test the property type value is not in the list of valid property types
     *
     * @test
     * @return void
     */
    public function it_checks_the_property_type_value_is_invalid(): void
    {
        $mls            = $this->faker->uuid;
        $state          = 'CA';
        $property_type  = $this->faker->md5;
        $effective_date = date('Y-m-d');
        $mls_file       = "{$this->faker->md5}.json";
        $command        = $this->getCommand($mls, $state, $property_type, $effective_date, $mls_file, false, '', 'test-property-type-is-valid');
        $this
            ->artisan($command)
            ->assertExitCode(400);
    }

    /**
     * Test the effective date value is received properly by the CLI function
     *
     * @test
     * @return void
     */
    public function it_checks_the_effective_date_value(): void
    {
        $mls            = $this->faker->uuid;
        $state          = 'CA';
        $property_type  = 'SFR';
        $effective_date = date('Y-m-d');
        $mls_file       = "{$this->faker->md5}.json";
        $command        = $this->getCommand($mls, $state, $property_type, $effective_date, $mls_file, false, '', 'test-effective-date');
        $this
            ->artisan($command)
            ->assertExitCode(FunWithText::integersInStringAsInt(md5($effective_date)));
    }

    /**
     * Test the effective date value is a valid date string
     *
     * @test
     * @return void
     */
    public function it_checks_the_effective_date_value_is_valid(): void
    {
        $mls            = $this->faker->uuid;
        $state          = 'CA';
        $property_type  = 'SFR';
        $effective_date = date('Y-m-d');
        $mls_file       = "{$this->faker->md5}.json";
        $command        = $this->getCommand($mls, $state, $property_type, $effective_date, $mls_file, false, '', 'test-effective-date-is-valid');
        $this
            ->artisan($command)
            ->assertExitCode(strtotime($effective_date));
    }

    /**
     * Test the effective date value is not a valid date string
     *
     * @test
     * @return void
     */
    public function it_checks_the_effective_date_value_is_invalid(): void
    {
        $mls            = $this->faker->uuid;
        $state          = 'CA';
        $property_type  = 'SFR';
        $effective_date = '2022-14-03';
        $mls_file       = "{$this->faker->md5}.json";
        $command        = $this->getCommand($mls, $state, $property_type, $effective_date, $mls_file, false, '', 'test-effective-date-is-valid');
        $this
            ->artisan($command)
            ->assertExitCode(400);
    }

    /**
     * Test the mls_file value is received properly by the CLI function
     *
     * @test
     * @return void
     */
    public function it_checks_the_mls_file_name_value(): void
    {
        $mls            = $this->faker->uuid;
        $state          = 'CA';
        $property_type  = 'SFR';
        $effective_date = date('Y-m-d');
        $mls_file       = "{$this->faker->md5}.json";
        $command        = $this->getCommand($mls, $state, $property_type, $effective_date, $mls_file, false, '', 'test-mls-file');
        $this
            ->artisan($command)
            ->assertExitCode(FunWithText::integersInStringAsInt($mls_file));
    }

    /**
     * Test the path value is empty when calling the CLI function
     *
     * @test
     * @return void
     */
    public function it_checks_that_the_path_option_is_empty_or_default(): void
    {
        $mls            = $this->faker->uuid;
        $state          = 'CA';
        $property_type  = 'SFR';
        $effective_date = date('Y-m-d');
        $mls_file       = "{$this->faker->md5}.json";
        $command        = $this->getCommand($mls, $state, $property_type, $effective_date, $mls_file, false, '', 'test-path-is-empty-or-default');
        $this
            ->artisan($command)
            ->assertExitCode(
                FunWithText::integersInStringAsInt(
                    md5(
                        config('truefootage.default.cli.mls.incoming_file_base_path')
                    )
                )
            );
    }

    /**
     * Test that the default path exists on the server as a real directory
     *
     * @test
     * @return void
     */
    public function it_checks_that_the_default_path_exists_on_the_server(): void
    {
        $mls            = $this->faker->uuid;
        $state          = 'CA';
        $property_type  = 'SFR';
        $effective_date = date('Y-m-d');
        $mls_file       = "{$this->faker->md5}.json";
        $command        = $this->getCommand($mls, $state, $property_type, $effective_date, $mls_file, false, '', 'test-path-exists-on-server');
        $this
            ->artisan($command)
            ->assertExitCode(
                FunWithText::integersInStringAsInt(
                    md5(
                        config('truefootage.default.cli.mls.incoming_file_base_path')
                    )
                )
            );
    }

    /**
     * Test that the requested path value exists on the server as a real directory
     *
     * @test
     * @return void
     */
    public function it_checks_that_a_gibberish_path_option_cant_be_passed(): void
    {
        $mls            = $this->faker->uuid;
        $state          = 'CA';
        $property_type  = 'SFR';
        $effective_date = date('Y-m-d');
        $mls_file       = "{$this->faker->md5}.json";
        $path           = 'some-gibberish-path';
        $command        = $this->getCommand($mls, $state, $property_type, $effective_date, $mls_file, false, $path, 'test-path-exists-on-server');
        $this
            ->artisan($command)
            ->assertExitCode(400);
    }

    /**
     * Test that the requested path value exists on the server as a real directory
     *
     * @test
     * @return void
     */
    public function it_checks_that_the_requested_path_option_exists_on_the_server(): void
    {
        $mls            = $this->faker->uuid;
        $state          = 'CA';
        $property_type  = 'SFR';
        $effective_date = date('Y-m-d');
        $mls_file       = "{$this->faker->md5}.json";
        $path           = '/usr/local/bin';
        $command        = $this->getCommand($mls, $state, $property_type, $effective_date, $mls_file, false, $path, 'test-path-exists-on-server');
        $this
            ->artisan($command)
            ->assertExitCode(
                FunWithText::integersInStringAsInt(md5($path))
            );
    }

    /**
     * Test that the requested path value exists on the server as a real directory
     *
     * @test
     * @return void
     */
    public function it_checks_that_a_path_that_doesnt_exist_cant_be_used(): void
    {
        $mls            = $this->faker->uuid;
        $state          = 'CA';
        $property_type  = 'SFR';
        $effective_date = date('Y-m-d');
        $mls_file       = "{$this->faker->md5}.json";
        $path           = '/some/path/that/does/not/really/exist';
        $command        = $this->getCommand($mls, $state, $property_type, $effective_date, $mls_file, false, $path, 'test-path-exists-on-server');
        $this
            ->artisan($command)
            ->assertExitCode(404);
    }

    /**
     * Test that the file to process exists on the server as a real file
     *
     * @test
     * @return void
     */
    public function it_checks_that_the_file_to_process_exists_on_the_server(): void
    {
        $mls            = $this->faker->uuid;
        $state          = 'CA';
        $property_type  = 'SFR';
        $effective_date = date('Y-m-d');
        $mls_file       = "hosts";
        $path           = '/etc';
        $command        = $this->getCommand($mls, $state, $property_type, $effective_date, $mls_file, false, $path, 'test-mls-file-exists-on-server');
        $this
            ->artisan($command)
            ->assertExitCode(
                FunWithText::integersInStringAsInt(md5("$path/$mls_file"))
            );
    }

    /**
     * Test that the file to process doesn't exist on the server as a real file
     * and handle it as a thrown exception
     *
     * @test
     * @return void
     */
    public function it_checks_that_the_file_to_process_does_not_exist_on_the_server(): void
    {
        $mls            = $this->faker->uuid;
        $state          = 'CA';
        $property_type  = 'SFR';
        $effective_date = date('Y-m-d');
        $mls_file       = "{$this->faker->md5}.json";
        $command        = $this->getCommand($mls, $state, $property_type, $effective_date, $mls_file, false, '', 'test-mls-file-exists-on-server');
        $this
            ->artisan($command)
            ->assertExitCode(404);
    }

    /**
     * Test the north-star-new flag is false
     *
     * @test
     * @return void
     */
    public function it_checks_that_north_star_new_is_false(): void
    {
        $mls            = $this->faker->uuid;
        $state          = 'CA';
        $property_type  = 'SFR';
        $effective_date = '2022-14-03';
        $mls_file       = "{$this->faker->md5}.json";
        $north_star_new = false;
        $command        = $this->getCommand($mls, $state, $property_type, $effective_date, $mls_file, $north_star_new, '', 'test-north-star-new-is-false');
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
     * @return void
     */
    public function it_checks_that_north_star_new_is_set_with_the_right_mls(): void
    {
        $mls            = 'Northstar';
        $state          = 'CA';
        $property_type  = 'SFR';
        $effective_date = '2022-14-03';
        $mls_file       = "{$this->faker->md5}.json";
        $north_star_new = true;
        $command        = $this->getCommand($mls, $state, $property_type, $effective_date, $mls_file, $north_star_new, '', 'test-north-star-new-is-valid');
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
     * @return void
     */
    public function it_checks_that_north_star_new_is_set_with_the_wrong_mls(): void
    {
        $mls            = $this->faker->uuid;
        $state          = 'CA';
        $property_type  = 'SFR';
        $effective_date = '2022-14-03';
        $mls_file       = "{$this->faker->md5}.json";
        $north_star_new = true;
        $command        = $this->getCommand($mls, $state, $property_type, $effective_date, $mls_file, $north_star_new, '', 'test-north-star-new-is-valid');
        $this
            ->artisan($command)
            ->assertExitCode(400);
    }

    /**
     * @param string $mls
     * @param string $state
     * @param string $property_type
     * @param string $effective_date
     * @param string $mls_file
     * @param bool $north_star_new
     * @param string $path
     * @param string $test
     * @return string
     */
    private function getCommand(string $mls, string $state, string $property_type, string $effective_date, string $mls_file, bool $north_star_new, string $path = '', string $test = ''): string
    {
        $mls            = FunWithText::safeString($mls);
        $state          = FunWithText::safeString($state);
        $property_type  = FunWithText::safeString($property_type);
        $effective_date = FunWithText::safeString($effective_date);
        $mls_file       = FunWithText::safeString($mls_file);
        $path           = FunWithText::safeString($path);
        $test           = FunWithText::safeString($test);
        $command        = "cli:call-mls-engine $mls $state $property_type $effective_date $mls_file";

        if ($path !== '') {
            $command .= " -p $path";
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
