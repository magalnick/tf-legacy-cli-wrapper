<?php

namespace App\Console\Commands;

use App\Console\Commands\Interfaces\LegacySparkSynapseCommandInterface;
use App\Models\MvcBusinessLogic\Interfaces\MlsFieldListModelAllowedCallingClassInterface;
use App\Models\MvcBusinessLogic\MlsFieldListModel;
use App\Tests\CallMlsFieldListTest;
use App\Utilities\FunWithText;
use Exception;

abstract class CallMlsFieldList extends LegacySparkSynapseCommand implements LegacySparkSynapseCommandInterface, MlsFieldListModelAllowedCallingClassInterface
{
    protected string $mls;
    protected string $state;
    protected string $property_type;
    protected bool $north_star_new;
    protected int $json_output_format;
    protected string $test;

    // this is the replacement variable for "hasit" from the legacy code
    // it's of type "mixed" because it is nullable, otherwise it will be an integer
    protected mixed $special_field_modifier;

    protected MlsFieldListModel $mls_field_list_model;

    protected array $allowed_calling_classes = [
        'App\Console\Commands\CallMlsFieldListRequiredFields',
        'App\Console\Commands\CallMlsFieldListReorderedFields',
    ];
    protected array $allowed_special_field_modifiers = [
        null,
        0,
        1,
        2,
    ];

    // this is to build a single value containing allowed calling classes,
    // allowed special field modifiers, and legacy array file.
    protected array $supplemental_data;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Main class handler for executing the console command.
     *
     * @return int
     * @throws Exception
     */
    public function mainHandle(): int
    {
        $this->extractArgumentAndOptionValues();
        $this->mls_field_list_model = new MlsFieldListModel(
            $this,
            $this->mls,
            $this->state,
            $this->property_type,
            $this->special_field_modifier,
            $this->north_star_new,
            $this->supplemental_data
        );

        if ($this->test !== '') {
            return $this->runUnitTests();
        }

        // the legacy field list functions all return arrays
        // do the JSON encoding here
        $field_list = $this->mls_field_list_model->getFieldList();
        echo json_encode($field_list, $this->json_output_format) . PHP_EOL;

//        $to_dump = [
//            'base_path' => base_path(),
//            'mls' => $this->mls,
//            'state' => $this->state,
//            'property_type' => $this->property_type,
//            'signature' => $this->signature,
//            'description' => $this->description,
//            'special-field-modifier' => $this->special_field_modifier,
//            'arguments' => $this->arguments(),
//            'options' => $this->options(),
//            'class_name' => get_class($this),
//            'exit_code' => 0,
//        ];
//        var_dump($to_dump);

        return 0;
    }

    /**
     * @return void
     */
    protected function extractArgumentAndOptionValues(): void
    {
        $this->extractCommonArgumentAndOptionValues();

        // This specifically must be either null or an integer.
        // If the CLI command puts it as text, set it to null instead of 0
        // since 0 is one of the valid integer values used.
        $this->special_field_modifier = $this->option('special-field-modifier') ?? null;
        if (!is_null($this->special_field_modifier)) {
            $this->special_field_modifier = is_numeric($this->special_field_modifier) ? (int) $this->special_field_modifier : null;
        }

        $pretty_print             = $this->option('pretty') ?? false;
        $this->json_output_format = $pretty_print ? JSON_PRETTY_PRINT : 0;

        $this->test = $this->option('test') ?? '';
        $this->test = FunWithText::safeString($this->test);

        // while technically not an extracted argument from the CLI call,
        // this array is used as an additional argument into the field list model
        $this->supplemental_data = [
            'allowed_calling_classes'           => $this->allowed_calling_classes,
            'allowed_special_field_modifiers'   => $this->allowed_special_field_modifiers,
            'legacy_array_file'                 => $this->legacy_array_file,
            'legacy_array_function_name_suffix' => $this->legacy_array_function_name_suffix,
        ];
    }

    //-------------------------------------------------------------------------------------------//
    //-------------------------------------------------------------------------------------------//
    //-------------------------------------------------------------------------------------------//

    /**
     * @return int
     * @throws Exception
     */
    protected function runUnitTests(): int
    {
        $exit_code = (
            new CallMlsFieldListTest(
                $this,
                $this->mls_field_list_model,
                $this->mls,
                $this->state,
                $this->property_type,
                $this->special_field_modifier,
                $this->north_star_new,
                $this->supplemental_data,
                $this->test
            )
        )->runUnitTests();

        echo json_encode([
                'mls' => $this->mls,
                'state' => $this->state,
                'property_type' => $this->property_type,
                'special_field_modifier' => $this->special_field_modifier,
                'north_star_new' => $this->north_star_new,
                'calling_class' => get_class($this),
                'legacy_array_file' => $this->legacy_array_file,
                'legacy_array_function_name_suffix' => $this->legacy_array_function_name_suffix,
                'test' => $this->test,
                'exit_code' => $exit_code,
            ]) . PHP_EOL;

        return $exit_code;
    }
}
