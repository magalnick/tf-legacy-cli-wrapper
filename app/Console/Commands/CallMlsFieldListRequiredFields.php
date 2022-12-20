<?php

namespace App\Console\Commands;

class CallMlsFieldListRequiredFields extends CallMlsFieldList
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cli:call-mls-required-field-list {mls} {state} {property_type} {--special-field-modifier=} {--north-star-new} {--pretty} {--t|test=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call up the list of required fields for a specific MLS';

    /**
     * Legacy PHP file pulled from Spark / Synapse for building out this field list.
     *
     * @var string
     */
    protected string $legacy_array_file = 'mlsarrays.php';

    /**
     * Function name suffix for the legacy PHP file pulled from
     * Spark / Synapse for building out this field list.
     *
     * @var string
     */
    protected string $legacy_array_function_name_suffix = 'ArrayCreate';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

}
