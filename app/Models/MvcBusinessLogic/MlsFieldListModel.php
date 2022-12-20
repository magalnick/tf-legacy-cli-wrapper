<?php

namespace App\Models\MvcBusinessLogic;

use App\Models\MvcBusinessLogic\Interfaces\MlsFieldListModelAllowedCallingClassInterface;
use Exception;

class MlsFieldListModel extends LegacySparkSynapseAbstractModel
{
    protected MlsFieldListModelAllowedCallingClassInterface $calling_class;
    protected mixed $special_field_modifier;
    protected array $supplemental_data;

    protected array $allowed_calling_classes;
    protected array $allowed_special_field_modifiers;
    protected string $legacy_array_file;
    protected string $legacy_array_file_dir;
    protected string $legacy_array_function_name_suffix;
    protected bool $is_legacy_array_file_included = false;

    /**
     * @param MlsFieldListModelAllowedCallingClassInterface $calling_class
     * @param string $mls
     * @param string $state
     * @param string $property_type
     * @param mixed $special_field_modifier
     * @param bool $north_star_new
     * @param array $supplemental_data
     */
    public function __construct(
        MlsFieldListModelAllowedCallingClassInterface $calling_class,
        string $mls,
        string $state,
        string $property_type,
        mixed $special_field_modifier,
        bool $north_star_new,
        array $supplemental_data
    ) {
        parent::__construct(
            $mls,
            $state,
            $property_type,
            $north_star_new
        );

        $this->calling_class                     = $calling_class;
        $this->special_field_modifier            = $special_field_modifier;
        $this->supplemental_data                 = $supplemental_data;
        $this->allowed_calling_classes           = $supplemental_data['allowed_calling_classes'] ?? [];
        $this->allowed_special_field_modifiers   = $supplemental_data['allowed_special_field_modifiers'] ?? [];
        $this->legacy_array_file                 = $supplemental_data['legacy_array_file'] ?? '';
        $this->legacy_array_function_name_suffix = $supplemental_data['legacy_array_function_name_suffix'] ?? '';
        $this->legacy_array_file_dir             = realpath(__DIR__ . '/../../../resources/legacy/spark-synapse/field-lists');

        // culling some potential errors by setting default values on some super globals
        $_POST['dualmlshasit'] = $_POST['dualmlshasit'] ?? '';
        $_COOKIE["id"]         = $_COOKIE["id"] ?? '';
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getFieldList(): array
    {
        return $this
            ->validateIncomingData()
            ->runFieldList()
            ;
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function validateIncomingData(): self
    {
        // calling these 2 functions already does the work of validating the data fields
        // and throwing exceptions if anything is wrong, so they're being used for validation
        $this->engineCodeForMlsStateMapping($this->mls, $this->state);
        $this->isNorthstarNewValid($this->mls, $this->north_star_new);

        if (!$this->isSpecialFieldModifierValid($this->special_field_modifier)) {
            throw new Exception("The \"--special-field-modifier\" flag is not one of the acceptable values.", 400);
        }
        if (!$this->isPropertyTypeValid($this->property_type)) {
            throw new Exception("Invalid property type: $this->property_type", 400);
        }
        if (!$this->legacyArrayFunctionExists(
            $this->mls,
            $this->state,
            $this->property_type,
            $this->legacy_array_function_name_suffix,
            $this->legacy_array_file
        )) {
            throw new Exception("The legacy array function for [$this->mls, $this->state, $this->property_type] does not exist.", 404);
        }

        return $this;
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function runFieldList(): array
    {
        $function_name = $this->legacyArrayFunctionName(
            $this->mls,
            $this->state,
            $this->property_type,
            $this->legacy_array_function_name_suffix
        );

        /*
         * Errors are not being suppressed in the function call since Brandon and Kyle
         * went and tracked down all the undefined variables and fixed them up.
         * However, the return array_map is still in place so that if there is somehow a null value,
         * it can still be handled.
         *
         * Since the undefined variables in the returned array are coming through as null,
         * they're being changed to empty string.
         *
         * After checking the MLS engine file for one of the ones with null values,
         * the place in the array for that value is expected to be the undefined variable.
         */
        $legacy_array = $function_name($this->special_field_modifier);
        return array_map(function($value) {
            if (is_null($value)) {
                return '';
            }
            return $value;
        }, $legacy_array);
    }

    /**
     * @param MlsFieldListModelAllowedCallingClassInterface $calling_class
     * @param array $allowed_calling_classes
     * @return bool
     */
    public function isCallingClassAllowed(MlsFieldListModelAllowedCallingClassInterface $calling_class, array $allowed_calling_classes): bool
    {
        return in_array(get_class($calling_class), $allowed_calling_classes);
    }

    /**
     * @param mixed $special_field_modifier
     * @return bool
     */
    public function isSpecialFieldModifierValid(mixed $special_field_modifier): bool
    {
        return in_array($special_field_modifier, $this->allowed_special_field_modifiers, true);
    }

    /**
     * @param string $legacy_array_file
     * @return bool
     * @throws Exception
     */
    public function legacyArrayFileExistsOnServer(string $legacy_array_file): bool
    {
        $legacy_array_file = trim($legacy_array_file);
        if ($legacy_array_file === '') {
            throw new Exception("The legacy array file name is empty.", 400);
        }

        if (!is_file($this->legacyArrayFileName($legacy_array_file))) {
            return false;
        }

        return true;
    }

    /**
     * @param string $mls
     * @param string $state
     * @param string $property_type
     * @param string $legacy_array_function_name_suffix
     * @param string $legacy_array_file
     * @return bool
     * @throws Exception
     */
    public function legacyArrayFunctionExists(
        string $mls,
        string $state,
        string $property_type,
        string $legacy_array_function_name_suffix,
        string $legacy_array_file
    ): bool {
        $legacy_array_function_name_suffix = trim($legacy_array_function_name_suffix);
        if ($legacy_array_function_name_suffix === '') {
            throw new Exception("The legacy array function name suffix is empty.", 400);
        }

        $this->requireLegacyArrayFile($legacy_array_file);

        return function_exists(
            $this->legacyArrayFunctionName(
                $mls,
                $state,
                $property_type,
                $legacy_array_function_name_suffix
            )
        );
    }

    /**
     * @param string $legacy_array_file
     * @return $this
     * @throws Exception
     */
    protected function requireLegacyArrayFile(string $legacy_array_file): self
    {
        if (!$this->legacyArrayFileExistsOnServer($legacy_array_file)) {
            throw new Exception("The legacy array file \"$legacy_array_file\" does not exist on the server.", 404);
        }
        if ($this->is_legacy_array_file_included) {
            return $this;
        }

        require_once $this->legacyArrayFileName($legacy_array_file);
        $this->is_legacy_array_file_included = true;
        return $this;
    }

    /**
     * @param string $legacy_array_file
     * @return string
     */
    protected function legacyArrayFileName(string $legacy_array_file): string
    {
        return "$this->legacy_array_file_dir/$legacy_array_file";
    }

    /**
     * @param string $mls
     * @param string $state
     * @param string $property_type
     * @param string $legacy_array_function_name_suffix
     * @return string
     */
    public function legacyArrayFunctionName(
        string $mls,
        string $state,
        string $property_type,
        string $legacy_array_function_name_suffix
    ): string {
        if (!$this->isPropertyTypeValid($property_type)) {
            throw new Exception("Invalid property type: $property_type", 400);
        }

        $engine_state_map = $this->engineCodeForMlsStateMapping($mls, $state);
        $function_name    = strtolower($engine_state_map) . $legacy_array_function_name_suffix;

        // checking as in_array instead of direct value
        // because the function name is hard-coded with Multi
        // and the check may also need to include Condo
        // this way it can be added in without rewriting the if logic
        if (in_array($property_type, ['Multi'])) {
            $function_name .= 'Multi';
        }

        return $function_name;
    }
}
