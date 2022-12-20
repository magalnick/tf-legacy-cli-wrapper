<?php

/**
 * Note that because of weirdness with capturing the output of an artisan call
 * when running it through PHP unit, I'm instead writing support for unit testing
 * as a special callable class from within the class being tested.
 *
 * File this under the category "exotic hacks".
 */

namespace App\Tests;

use App\Models\MvcBusinessLogic\Interfaces\MlsFieldListModelAllowedCallingClassInterface;
use Exception;
use App\Models\MvcBusinessLogic\Interfaces\LegacySparkSynapseModelInterface;

class CallMlsFieldListTest extends LegacySparkSynapseAbstractTest
{
    protected MlsFieldListModelAllowedCallingClassInterface $calling_class;
    protected mixed $special_field_modifier;
    protected array $supplemental_data;

    protected array $allowed_calling_classes;
    protected string $legacy_array_file;
    protected string $legacy_array_function_name_suffix;

    /**
     * @param MlsFieldListModelAllowedCallingClassInterface $calling_class
     * @param LegacySparkSynapseModelInterface $legacy_spark_synapse_model
     * @param string $mls
     * @param string $state
     * @param string $property_type
     * @param mixed $special_field_modifier
     * @param bool $north_star_new
     * @param array $supplemental_data
     * @param string $test
     * @return void
     */
    public function __construct(
        MlsFieldListModelAllowedCallingClassInterface $calling_class,
        LegacySparkSynapseModelInterface $legacy_spark_synapse_model,
        string $mls,
        string $state,
        string $property_type,
        mixed $special_field_modifier,
        bool $north_star_new,
        array $supplemental_data,
        string $test
    ) {
        parent::__construct(
            $legacy_spark_synapse_model,
            $mls,
            $state,
            $property_type,
            $north_star_new,
            $test
        );

        $this->calling_class                     = $calling_class;
        $this->special_field_modifier            = $special_field_modifier;
        $this->supplemental_data                 = $supplemental_data;
        $this->allowed_calling_classes           = $supplemental_data['allowed_calling_classes'] ?? [];
        $this->legacy_array_file                 = $supplemental_data['legacy_array_file'] ?? '';
        $this->legacy_array_function_name_suffix = $supplemental_data['legacy_array_function_name_suffix'] ?? '';
    }

    /**
     * @return int
     * @throws Exception
     */
    public function runUnitTests(): int
    {
        if (!in_array($this->test, $this->validUnitTests())) {
            throw new Exception("Invalid unit test: $this->test", 400);
        }

        return match ($this->test) {
            'test-calling-class-is-allowed'           => $this->testCallingClassIsAllowed($this->calling_class, $this->allowed_calling_classes),
            'test-fake-calling-class-is-not-allowed'  => $this->testFakeCallingClassIsNotAllowed($this->calling_class, $this->allowed_calling_classes),
            'test-mls'                                => $this->testMls($this->mls),
            'test-mls-is-valid'                       => $this->testMlsIsValid($this->mls),
            'test-state'                              => $this->testState($this->state),
            'test-state-is-valid'                     => $this->testStateIsValid($this->state),
            'test-mls-state-combination'              => $this->testMlsStateCombination($this->mls, $this->state),
            'test-property-type'                      => $this->testPropertyType($this->property_type),
            'test-property-type-is-valid'             => $this->testPropertyTypeIsValid($this->property_type),
            'test-special-field-modifier-is-valid'    => $this->testSpecialFieldModifierIsValid($this->special_field_modifier),
            'test-north-star-new-is-false'            => $this->testNorthStarNewIsFalse($this->north_star_new),
            'test-north-star-new-is-valid'            => $this->testNorthStarNewIsValid($this->mls, $this->north_star_new),
            'test-legacy-array-file-exists-on-server' => $this->testLegacyArrayFileExistsOnServer($this->legacy_array_file),
            'test-legacy-array-function-exists'       => $this->testLegacyArrayFunctionExists(
                $this->mls,
                $this->state,
                $this->property_type,
                $this->legacy_array_function_name_suffix,
                $this->legacy_array_file
            ),
            'test-all-legacy-array-functions'         => $this->testAllLegacyArrayFunctionsForErrors($this->legacy_array_function_name_suffix, $this->legacy_array_file),
            default                                   => throw new Exception(
                "Whoops, it looks like unit test \"$this->test\" has been defined but not yet handled. Please write the function to handle it.",
                404
            ),
        };
    }

    /**
     * @return array
     */
    protected function validUnitTests(): array
    {
        return [
            'test-calling-class-is-allowed',
            'test-fake-calling-class-is-not-allowed',
            'test-mls',
            'test-mls-is-valid',
            'test-state',
            'test-state-is-valid',
            'test-mls-state-combination',
            'test-property-type',
            'test-property-type-is-valid',
            'test-special-field-modifier-is-valid',
            'test-north-star-new-is-false',
            'test-north-star-new-is-valid',
            'test-legacy-array-file-exists-on-server',
            'test-legacy-array-function-exists',
            'test-all-legacy-array-functions',
        ];
    }

    /**
     * @param MlsFieldListModelAllowedCallingClassInterface $calling_class
     * @param array $allowed_calling_classes
     * @return int
     * @throws Exception
     */
    protected function testCallingClassIsAllowed(MlsFieldListModelAllowedCallingClassInterface $calling_class, array $allowed_calling_classes): int
    {
        if (!$this->legacy_spark_synapse_model->isCallingClassAllowed($calling_class, $allowed_calling_classes)) {
            throw new Exception("The calling class \"" . get_class($calling_class) . "\" is not allowed to run this command.", 403);
        }

        return 0;
    }

    /**
     * This is a hack that keeps the calling class intact but changes the list of allowed ones
     * before calling the test to check that the calling class is allowed.
     *
     * @param MlsFieldListModelAllowedCallingClassInterface $calling_class
     * @param array $allowed_calling_classes
     * @return int
     * @throws Exception
     */
    protected function testFakeCallingClassIsNotAllowed(MlsFieldListModelAllowedCallingClassInterface $calling_class, array $allowed_calling_classes): int
    {
        $fake_calling_classes = array_map(
            function($allowed_calling_class) {
                return "Fake$allowed_calling_class";
            }, $allowed_calling_classes
        );
        return $this->testCallingClassIsAllowed($calling_class, $fake_calling_classes);
    }

    /**
     * @param mixed $special_field_modifier
     * @return int
     * @throws Exception
     */
    protected function testSpecialFieldModifierIsValid(mixed $special_field_modifier): int
    {
        if (!$this->legacy_spark_synapse_model->isSpecialFieldModifierValid($special_field_modifier)) {
            throw new Exception("The \"--special-field-modifier\" flag is not one of the acceptable values.", 400);
        }

        if (is_null($special_field_modifier)) {
            return -1;
        }

        return $special_field_modifier;
    }

    /**
     * @param string $legacy_array_file
     * @return int
     * @throws Exception
     */
    protected function testLegacyArrayFileExistsOnServer(string $legacy_array_file): int
    {
        if (!$this->legacy_spark_synapse_model->legacyArrayFileExistsOnServer($legacy_array_file)) {
            throw new Exception("The legacy array file \"$legacy_array_file\" does not exist on the server.", 404);
        }

        return 0;
    }

    /**
     * @param string $mls
     * @param string $state
     * @param string $property_type
     * @param string $legacy_array_function_name_suffix
     * @param string $legacy_array_file
     * @return int
     * @throws Exception
     */
    protected function testLegacyArrayFunctionExists(
        string $mls,
        string $state,
        string $property_type,
        string $legacy_array_function_name_suffix,
        string $legacy_array_file
    ): int {
        if (!$this->legacy_spark_synapse_model->legacyArrayFunctionExists(
            $mls,
            $state,
            $property_type,
            $legacy_array_function_name_suffix,
            $legacy_array_file
        )) {
            throw new Exception("The legacy array function for [$mls, $state, $property_type] does not exist.", 404);
        }

        return 0;
    }

    /**
     * This particular test is a little different from the others, as it gets a list of all MLSes
     * so that it can do a sanity check on every function.
     * It might not need to run as part of the standard test matrix once code is deployed.
     * Therefore, there is an exit clause at the top, controlled by an env value, that will immediately
     * exit with the successful exit code unless this test is supposed to run.
     *
     * Very extra special note on the env value:
     * It *IS NOT* controlled by the .env.testing/dev/production files,
     * rather it's controlled by the phpunit.xml file. And since it's only used for unit tests,
     * there's no need to add it to the .env.xxx files.
     *
     * Note after running this on all MLSes, for SFR and Multi, for both function files,
     * when suppressing errors, they all ran just fine with no changes needed.
     * When running without suppressing errors, the only errors I saw were
     * undefined variables and missing array keys, both of which are non-issues
     * since any null values in the final output are being handled.
     *
     * @param string $legacy_array_function_name_suffix
     * @param string $legacy_array_file
     * @return int
     * @throws Exception
     */
    protected function testAllLegacyArrayFunctionsForErrors(string $legacy_array_function_name_suffix, string $legacy_array_file): int
    {
        $run_this_test = env('RUN_SANITY_CHECK_ON_ALL_LEGACY_ARRAY_FUNCTIONS', false);
        if (!$run_this_test) {
            throw new Exception("The test of all legacy array functions does not need to run.", 1);
        }

        $property_types = ['SFR', 'Multi'];
        foreach ($property_types as $property_type) {
            $this->testAllLegacyArrayFunctionsForErrors_byPropertyType(
                $property_type,
                $legacy_array_function_name_suffix,
                $legacy_array_file
            );
        }

        return 1;
    }

    /**
     * @param string $property_type
     * @param string $legacy_array_function_name_suffix
     * @param string $legacy_array_file
     * @return void
     * @throws Exception
     */
    private function testAllLegacyArrayFunctionsForErrors_byPropertyType(
        string $property_type,
        string $legacy_array_function_name_suffix,
        string $legacy_array_file
    ): void {

        try {
            $static_engines = config('truefootage.mls.engines.static');
            foreach ($static_engines as $engine) {
                $state = substr($engine, 0, 2);
                $mls   = substr($engine, 3);

                $this->testAllLegacyArrayFunctionsForErrors_byMlsStatePropertyType(
                    $mls,
                    $state,
                    $property_type,
                    $legacy_array_function_name_suffix,
                    $legacy_array_file
                );
            }
        } catch (Exception $e) {
            $code = $e->getCode() ?? 400;
            $code = (int) $code;
            throw new Exception($e->getMessage(), $code);
        }
    }

    /**
     * @param string $mls
     * @param string $state
     * @param string $property_type
     * @param string $legacy_array_function_name_suffix
     * @param string $legacy_array_file
     * @return void
     * @throws Exception
     */
    private function testAllLegacyArrayFunctionsForErrors_byMlsStatePropertyType(
        string $mls,
        string $state,
        string $property_type,
        string $legacy_array_function_name_suffix,
        string $legacy_array_file
    ): void {

        try {
            // if the function doesn't exist, keep going
            // there's already a unit test to check that the function exists works as intended
            // this is for checking the functions that do exist
            if (!$this->legacy_spark_synapse_model->legacyArrayFunctionExists(
                $mls,
                $state,
                $property_type,
                $legacy_array_function_name_suffix,
                $legacy_array_file
            )) {
                //echo "The legacy array function for [$mls, $state, $property_type] does not exist." . PHP_EOL;
                return;
            }

            $function_name = $this->legacy_spark_synapse_model->legacyArrayFunctionName(
                $mls,
                $state,
                $property_type,
                $legacy_array_function_name_suffix
            );

            // running this for the 3 "hasit" values that generally exist
            // echo $function_name . PHP_EOL;
            $function_name(0);
            $function_name(1);
            $function_name(2);
        } catch (Exception $e) {
            $code = $e->getCode() ?? 400;
            $code = (int) $code;
            throw new Exception($e->getMessage(), $code);
        }
    }
}
