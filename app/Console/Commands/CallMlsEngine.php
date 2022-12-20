<?php

namespace App\Console\Commands;

use App\Console\Commands\Interfaces\LegacySparkSynapseCommandInterface;
use App\Models\MvcBusinessLogic\MlsEngineModel;
use App\Tests\CallMlsEngineTest;
use App\Utilities\FunWithText;
use Exception;

class CallMlsEngine extends LegacySparkSynapseCommand implements LegacySparkSynapseCommandInterface
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cli:call-mls-engine {mls} {state} {property_type} {effective_date} {mls_file} {--p|path=} {--north-star-new} {--t|test=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call up the specific MLS engine for processing the uploaded property data';

    /**
     * List of properties that are available through the __get() function
     *
     * @var array
     */
    protected array $gettable_properties = [
        'mls',
        'state',
        'property_type',
        'effective_date',
        'mls_file',
        'path',
    ];

    protected string $mls;
    protected string $state;
    protected string $property_type;
    protected string $effective_date;
    protected string $mls_file;
    protected string $path;
    protected bool $north_star_new;
    protected string $test;

    protected MlsEngineModel $mls_engine_model;

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
        $this->mls_engine_model = new MlsEngineModel(
            $this->mls,
            $this->state,
            $this->property_type,
            $this->effective_date,
            $this->mls_file,
            $this->path,
            $this->north_star_new
        );

        if ($this->test !== '') {
            return $this->runUnitTests();
        }

        $json_output = $this->mls_engine_model->runEngine();
        echo $json_output . PHP_EOL;

//        $to_dump = [
//            'base_path' => base_path(),
//            'mls' => $this->mls,
//            'state' => $this->state,
//            'property_type' => $this->property_type,
//            'effective_date' => $this->effective_date,
//            'mls_file' => $this->mls_file,
//            'path' => $this->path,
//            'signature' => $this->signature,
//            'description' => $this->description,
//            'arguments' => $this->arguments(),
//            'options' => $this->options(),
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

        $this->effective_date = $this->argument('effective_date') ?? '';
        $this->effective_date = FunWithText::safeString($this->effective_date);

        $this->mls_file = $this->argument('mls_file') ?? '';
        $this->mls_file = FunWithText::safeString($this->mls_file);

        $this->path = $this->option('path') ?? '';
        $this->path = FunWithText::safeString($this->path);
        if ($this->path === '') {
            $this->path = config('truefootage.default.cli.mls.incoming_file_base_path');
        }

        $this->test = $this->option('test') ?? '';
        $this->test = FunWithText::safeString($this->test);
    }

    /**
     * @param $property
     * @return string
     * @throws Exception
     */
    public function __get($property): string
    {
        $property = FunWithText::safeString($property);
        if (!in_array($property, $this->gettable_properties)) {
            throw new Exception("Property '$property' does not exist.");
        }

        return $this->{$property};
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
            new CallMlsEngineTest(
                $this->mls_engine_model,
                $this->mls,
                $this->state,
                $this->property_type,
                $this->effective_date,
                $this->mls_file,
                $this->path,
                $this->north_star_new,
                $this->test
            )
        )->runUnitTests();

        echo json_encode([
                'mls' => $this->mls,
                'state' => $this->state,
                'property_type' => $this->property_type,
                'effective_date' => $this->effective_date,
                'mls_file' => $this->mls_file,
                'path' => $this->path,
                'north_star_new' => $this->north_star_new,
                'test' => $this->test,
                'exit_code' => $exit_code,
            ]) . PHP_EOL;

        return $exit_code;
    }
}
