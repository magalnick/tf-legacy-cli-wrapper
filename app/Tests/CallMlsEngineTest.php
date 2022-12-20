<?php

/**
 * Note that because of weirdness with capturing the output of an artisan call
 * when running it through PHP unit, I'm instead writing support for unit testing
 * as a special callable class from within the class being tested.
 *
 * File this under the category "exotic hacks".
 */

namespace App\Tests;

use Exception;
use App\Models\MvcBusinessLogic\Interfaces\LegacySparkSynapseModelInterface;
use App\Utilities\FunWithText;

class CallMlsEngineTest extends LegacySparkSynapseAbstractTest
{
    protected string $effective_date;
    protected string $mls_file;
    protected string $path;

    /**
     * @param LegacySparkSynapseModelInterface $legacy_spark_synapse_model
     * @param string $mls
     * @param string $state
     * @param string $property_type
     * @param string $effective_date
     * @param string $mls_file
     * @param string $path
     * @param bool $north_star_new
     * @param string $test
     * @return void
     */
    public function __construct(
        LegacySparkSynapseModelInterface $legacy_spark_synapse_model,
        string $mls,
        string $state,
        string $property_type,
        string $effective_date,
        string $mls_file,
        string $path,
        bool $north_star_new,
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

        $this->effective_date = $effective_date;
        $this->mls_file       = $mls_file;
        $this->path           = $path;
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
            'test-mls'                       => $this->testMls($this->mls),
            'test-mls-is-valid'              => $this->testMlsIsValid($this->mls),
            'test-state'                     => $this->testState($this->state),
            'test-state-is-valid'            => $this->testStateIsValid($this->state),
            'test-mls-state-combination'     => $this->testMlsStateCombination($this->mls, $this->state),
            'test-property-type'             => $this->testPropertyType($this->property_type),
            'test-property-type-is-valid'    => $this->testPropertyTypeIsValid($this->property_type),
            'test-effective-date'            => $this->testEffectiveDate($this->effective_date),
            'test-effective-date-is-valid'   => $this->testEffectiveDateIsValid($this->effective_date),
            'test-mls-file'                  => $this->testMlsFile($this->mls_file),
            'test-path-is-empty-or-default'  => $this->testPathIsEmptyOrDefault($this->path),
            'test-path-exists-on-server'     => $this->testPathExistsOnServer($this->path),
            'test-mls-file-exists-on-server' => $this->testMlsFileExistsOnServer($this->path, $this->mls_file),
            'test-north-star-new-is-false'   => $this->testNorthStarNewIsFalse($this->north_star_new),
            'test-north-star-new-is-valid'   => $this->testNorthStarNewIsValid($this->mls, $this->north_star_new),
            default                          => throw new Exception(
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
            'test-mls',
            'test-mls-is-valid',
            'test-state',
            'test-state-is-valid',
            'test-mls-state-combination',
            'test-property-type',
            'test-property-type-is-valid',
            'test-effective-date',
            'test-effective-date-is-valid',
            'test-mls-file',
            'test-path-is-empty-or-default',
            'test-path-exists-on-server',
            'test-mls-file-exists-on-server',
            'test-north-star-new-is-false',
            'test-north-star-new-is-valid',
        ];
    }

    /**
     * @param string $effective_date
     * @return int
     * @throws Exception
     */
    protected function testEffectiveDate(string $effective_date): int
    {
        if ($effective_date === '') {
            throw new Exception("Effective date is unclean or empty.", 400);
        }

        return FunWithText::integersInStringAsInt(md5($effective_date));
    }

    /**
     * @param string $effective_date
     * @return int
     * @throws Exception
     */
    protected function testEffectiveDateIsValid(string $effective_date): int
    {
        if ($effective_date === '') {
            throw new Exception("Effective date is unclean or empty.", 400);
        }

        if (!$this->legacy_spark_synapse_model->isEffectiveDateValid($effective_date)) {
            throw new Exception("Invalid effective date: $effective_date", 400);
        }

        return strtotime($effective_date);
    }

    /**
     * @param string $mls_file
     * @return int
     * @throws Exception
     */
    protected function testMlsFile(string $mls_file): int
    {
        if ($mls_file === '') {
            throw new Exception("MLS filename is unclean or empty.", 400);
        }

        return FunWithText::integersInStringAsInt($mls_file);
    }

    /**
     * @param string $path
     * @return int
     * @throws Exception
     */
    protected function testPathIsEmptyOrDefault(string $path): int
    {
        if ($path !== config('truefootage.default.cli.mls.incoming_file_base_path')) {
            throw new Exception("The path option is not empty or does not match the system default.", 400);
        }

        return FunWithText::integersInStringAsInt(md5($path));
    }

    /**
     * @param string $path
     * @return int
     * @throws Exception
     */
    protected function testPathExistsOnServer(string $path): int
    {
        if (!$this->legacy_spark_synapse_model->pathExistsOnServer($path)) {
            throw new Exception("The path \"$path\" does not exist on the server.", 404);
        }

        return FunWithText::integersInStringAsInt(md5($path));
    }

    /**
     * @param string $path
     * @param string $mls_file
     * @return int
     * @throws Exception
     */
    protected function testMlsFileExistsOnServer(string $path, string $mls_file): int
    {
        if (!$this->legacy_spark_synapse_model->mlsFileExistsOnServer($path, $mls_file)) {
            throw new Exception("The file \"{$this->legacy_spark_synapse_model->fullPathToMlsFile($path, $mls_file)}\" does not exist on the server.", 404);
        }

        return FunWithText::integersInStringAsInt(md5($this->legacy_spark_synapse_model->fullPathToMlsFile($path, $mls_file)));
    }
}
