<?php

namespace App\Tests;

use Exception;
use App\Models\MvcBusinessLogic\Interfaces\LegacySparkSynapseModelInterface;
use App\Tests\Interfaces\LegacySparkSynapseTestInterface;
use App\Utilities\FunWithText;

abstract class LegacySparkSynapseAbstractTest implements LegacySparkSynapseTestInterface
{
    protected LegacySparkSynapseModelInterface $legacy_spark_synapse_model;
    protected string $mls;
    protected string $state;
    protected string $property_type;
    protected bool $north_star_new;
    protected string $test;

    /**
     * @param LegacySparkSynapseModelInterface $legacy_spark_synapse_model
     * @param string $mls
     * @param string $state
     * @param string $property_type
     * @param bool $north_star_new
     * @param string $test
     * @return void
     */
    public function __construct(
        LegacySparkSynapseModelInterface $legacy_spark_synapse_model,
        string $mls,
        string $state,
        string $property_type,
        bool $north_star_new,
        string $test
    ) {
        $this->legacy_spark_synapse_model = $legacy_spark_synapse_model;
        $this->mls              = $mls;
        $this->state            = $state;
        $this->property_type    = $property_type;
        $this->north_star_new   = $north_star_new;
        $this->test             = $test;
    }

    /**
     * @param string $mls
     * @return int
     * @throws Exception
     */
    protected function testMls(string $mls): int
    {
        if ($mls === '') {
            throw new Exception("MLS is unclean or empty.", 400);
        }

        return FunWithText::integersInStringAsInt(
            substr($mls, 0, 20)
        );
    }

    /**
     * @param string $mls
     * @return int
     * @throws Exception
     */
    protected function testMlsIsValid(string $mls): int
    {
        if ($mls === '') {
            throw new Exception("MLS is unclean or empty.", 400);
        }

        if (!$this->legacy_spark_synapse_model->isMlsValid($mls)) {
            throw new Exception("Invalid MLS: $mls", 400);
        }

        return FunWithText::integersInStringAsInt(
            substr(md5($mls), 0, 20)
        );
    }

    /**
     * @param string $state
     * @return int
     * @throws Exception
     */
    protected function testState(string $state): int
    {
        if ($state === '') {
            throw new Exception("State is unclean or empty.", 400);
        }

        return FunWithText::integersInStringAsInt(md5($state));
    }

    /**
     * @param string $state
     * @return int
     * @throws Exception
     */
    protected function testStateIsValid(string $state): int
    {
        if ($state === '') {
            throw new Exception("State is unclean or empty.", 400);
        }

        if (!$this->legacy_spark_synapse_model->isStateValid($state)) {
            throw new Exception("Invalid state: $state", 400);
        }

        return FunWithText::integersInStringAsInt(md5($state));
    }

    /**
     * @param string $mls
     * @param string $state
     * @return int
     * @throws Exception
     */
    protected function testMlsStateCombination(string $mls, string $state): int
    {
        if ($mls === '') {
            throw new Exception("MLS is unclean or empty.", 400);
        }
        if ($state === '') {
            throw new Exception("State is unclean or empty.", 400);
        }

        if ($this->legacy_spark_synapse_model->engineCodeForMlsStateMapping($mls, $state) === '') {
            throw new Exception("Invalid mapping for MLS $mls and state $state", 400);
        }

        return FunWithText::integersInStringAsInt(md5($mls . $state));
    }

    /**
     * @param string $property_type
     * @return int
     * @throws Exception
     */
    protected function testPropertyType(string $property_type): int
    {
        if ($property_type === '') {
            throw new Exception("Property type is unclean or empty.", 400);
        }

        return FunWithText::integersInStringAsInt(md5($property_type));
    }

    /**
     * @param string $property_type
     * @return int
     * @throws Exception
     */
    protected function testPropertyTypeIsValid(string $property_type): int
    {
        if ($property_type === '') {
            throw new Exception("Property type is unclean or empty.", 400);
        }

        if (!$this->legacy_spark_synapse_model->isPropertyTypeValid($property_type)) {
            throw new Exception("Invalid property type: $property_type", 400);
        }

        return FunWithText::integersInStringAsInt(md5($property_type));
    }

    /**
     * @param bool $north_star_new
     * @return int
     * @throws Exception
     */
    protected function testNorthStarNewIsFalse(bool $north_star_new): int
    {
        if (false !== $north_star_new) {
            throw new Exception("The \"--north-star-new\" flag is set when it shouldn't be.", 400);
        }

        return FunWithText::integersInStringAsInt(md5($north_star_new));
    }

    /**
     * @param string $mls
     * @param bool $north_star_new
     * @return int
     * @throws Exception
     */
    protected function testNorthStarNewIsValid(string $mls, bool $north_star_new): int
    {
        if (!$north_star_new) {
            throw new Exception("The \"--north-star-new\" flag isn't set when it should be.", 400);
        }

        $this->legacy_spark_synapse_model->isNorthstarNewValid($mls, $north_star_new);
        return FunWithText::integersInStringAsInt(
            substr(md5(
                md5($mls) . md5($north_star_new)
            ), 0, 16)
        );
    }
}
