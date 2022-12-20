<?php

namespace App\Console\Commands;

use App\Utilities\FunWithText;
use Exception;
use Illuminate\Console\Command;

abstract class LegacySparkSynapseCommand extends Command
{
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
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            return $this->mainHandle();
        } catch (Exception $e) {
            echo json_encode([
                    'success' => false,
                    'errors' => [
                        $e->getMessage(),
                    ],
                    'code' => $e->getCode(),
                ]) . PHP_EOL;
            return $e->getCode();
        }
    }

    /**
     * @return void
     */
    protected function extractCommonArgumentAndOptionValues()
    {
        $this->mls = $this->argument('mls') ?? '';
        $this->mls = FunWithText::safeString($this->mls);

        $this->state = $this->argument('state') ?? '';
        $this->state = FunWithText::safeString($this->state);

        $this->property_type = $this->argument('property_type') ?? '';
        $this->property_type = FunWithText::safeString($this->property_type);

        $this->north_star_new = $this->option('north-star-new') ?? false;
        $this->north_star_new = (bool)$this->north_star_new;
    }
}
