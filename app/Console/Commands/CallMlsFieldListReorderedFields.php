<?php

namespace App\Console\Commands;

class CallMlsFieldListReorderedFields extends CallMlsFieldList
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cli:call-mls-reordered-field-list {mls} {state} {property_type} {--special-field-modifier=} {--north-star-new} {--pretty} {--t|test=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call up the list of reordered allowed fields for a specific MLS that can be handed to the call MLS engine program';

    /**
     * Legacy PHP file pulled from Spark / Synapse for building out this field list.
     *
     * @var string
     */
    protected string $legacy_array_file = 'reorderarrays.php';

    /**
     * Function name suffix for the legacy PHP file pulled from
     * Spark / Synapse for building out this field list.
     *
     * @var string
     */
    protected string $legacy_array_function_name_suffix = 'ReorderCreate';

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
