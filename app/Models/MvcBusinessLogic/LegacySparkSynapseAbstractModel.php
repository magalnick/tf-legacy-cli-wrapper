<?php

namespace App\Models\MvcBusinessLogic;

use Exception;
use App\Models\MvcBusinessLogic\Interfaces\LegacySparkSynapseModelInterface;
use App\Utilities\FunWithText;

abstract class LegacySparkSynapseAbstractModel implements LegacySparkSynapseModelInterface
{
    protected string $mls;
    protected string $state;
    protected string $property_type;
    protected bool $north_star_new;

    protected array $states;
    protected array $engines;
    protected array $engine_state_map;
    protected array $property_types;

    /**
     * @param string $mls
     * @param string $state
     * @param string $property_type
     * @param bool $north_star_new
     */
    public function __construct(
        string $mls,
        string $state,
        string $property_type,
        bool $north_star_new
    ) {
        $this->mls            = $mls;
        $this->state          = $state;
        $this->property_type  = $property_type;
        $this->north_star_new = $north_star_new;

        $this->states           = config('truefootage.mls.states');
        $this->engines          = config('truefootage.mls.engines');
        $this->engine_state_map = config('truefootage.mls.engine_state_map');
        $this->property_types   = config('truefootage.mls.property_types');
    }

    /**
     * @param string $mls
     * @return bool
     */
    public function isMlsValid(string $mls): bool
    {
        if (!array_key_exists($mls, $this->engine_state_map)) {
            return false;
        }
        return true;
    }

    /**
     * @param string $state
     * @return bool
     */
    public function isStateValid(string $state): bool
    {
        if (!in_array($state, $this->states)) {
            return false;
        }
        return true;
    }

    /**
     * @param string $mls
     * @param string $state
     * @return string
     * @throws Exception
     */
    public function engineCodeForMlsStateMapping(string $mls, string $state): string
    {
        if (!$this->isMlsValid($mls)) {
            throw new Exception("Invalid MLS: $mls", 400);
        }
        if (!$this->isStateValid($state)) {
            throw new Exception("Invalid state: $state", 400);
        }
        if (!array_key_exists($state, $this->engine_state_map[$mls])) {
            throw new Exception("The state of $state is not covered by the MLS $mls", 400);
        }

        return $this->engine_state_map[$mls][$state];
    }

    /**
     * @param string $property_type
     * @return bool
     */
    public function isPropertyTypeValid(string $property_type): bool
    {
        if (!array_key_exists($property_type, $this->property_types)) {
            return false;
        }
        return true;
    }

    /**
     * @param string $effective_date
     * @return bool
     */
    public function isEffectiveDateValid(string $effective_date): bool
    {
        return FunWithText::isDate($effective_date);
    }

    /**
     * @param string $mls
     * @param bool $north_star_new
     * @return bool
     * @throws Exception
     */
    public function isNorthstarNewValid(string $mls, bool $north_star_new): bool
    {
        if ($north_star_new && $mls !== 'Northstar') {
            throw new Exception("The '--north-star-new' option is only allowed if the MLS is Northstar.", 400);
        }
        return true;
    }
}
