<?php

namespace App\Models\MvcBusinessLogic;

use Exception;
use App\Utilities\FunWithText;

class MlsEngineModel extends LegacySparkSynapseAbstractModel
{
    protected string $effective_date;
    protected string $mls_file;
    protected string $path;

    protected string $mls_engine_code;
    protected string $mls_engine_file;
    protected string $mls_data_file;
    protected array $mls_data_file_contents;
    protected array $engine_user_settings;

    protected bool $path_existence_has_been_checked = false;
    protected bool $path_existence_check_results    = false;

    /**
     * @param string $mls
     * @param string $state
     * @param string $property_type
     * @param string $effective_date
     * @param string $mls_file
     * @param string $path
     * @param bool $north_star_new
     */
    public function __construct(
        string $mls,
        string $state,
        string $property_type,
        string $effective_date,
        string $mls_file,
        string $path,
        bool $north_star_new
    ) {
        parent::__construct(
            $mls,
            $state,
            $property_type,
            $north_star_new
        );

        $this->effective_date = $effective_date;
        $this->mls_file       = $mls_file;
        $this->path           = $path;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function runEngine(): string
    {
        return $this
            ->validateIncomingData()
            ->setValuesForCrunchingTheData()
            ->extractGlobalsFromEngineUserSettings()
            ->hackForSuppressingVariousUndefinedArrayKeyWarnings()
            ->requireMlsEngineFile()
            ->runCrunchIt()
        ;
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function validateIncomingData(): self
    {
        // calling these 3 functions already does the work of validating the data fields
        // and throwing exceptions if anything is wrong, so they're being used for validation
        $this->engineCodeForMlsStateMapping($this->mls, $this->state);
        $this->mlsFileExistsOnServer($this->path, $this->mls_file);
        $this->isNorthstarNewValid($this->mls, $this->north_star_new);

        if (!$this->isPropertyTypeValid($this->property_type)) {
            throw new Exception("Invalid property type: $this->property_type", 400);
        }
        if (!$this->isEffectiveDateValid($this->effective_date)) {
            throw new Exception("Invalid effective date: $this->effective_date", 400);
        }

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function setValuesForCrunchingTheData(): self
    {
        $this->mls_engine_code        = $this->engineCodeForMlsStateMapping($this->mls, $this->state);
        $this->mls_data_file          = $this->fullPathToMlsFile($this->path, $this->mls_file);
        $this->engine_user_settings   = config("truefootage.mls.engine-default-user-settings.$this->mls_engine_code");
        $this->mls_engine_file        = $this->mls_engine_code . ($this->north_star_new ? 'New' : '');
        $this->mls_data_file_contents = json_decode(
            FunWithText::basicStringCleanup(
                file_get_contents($this->mls_data_file)
            ), true
        );
        return $this;
    }

    /**
     * Extract the engine user setting as variables (eg. $datasource7, $concessions7, etc.)
     * and set them as global so that they can be picked up and used in the legacy
     * Spark / Synapse MLS engines files.
     *
     * @return $this
     */
    protected function extractGlobalsFromEngineUserSettings(): self
    {
        foreach ($this->engine_user_settings as $key => $value) {
            $GLOBALS[$key] = $value;
        }

        return $this;
    }

    /**
     * This function will also set up $_POST and $_COOKIE as needed.
     *
     * @return $this
     */
    protected function hackForSuppressingVariousUndefinedArrayKeyWarnings(): self
    {
        if (!isset($this->mls_data_file_contents[1]['formtype'])) {
            $this->mls_data_file_contents[1]['formtype'] = null;
        }
        if (!isset($this->mls_data_file_contents[1]['uniqueid'])) {
            $this->mls_data_file_contents[1]['uniqueid'] = null;
        }

        $for_post = 'fhavalue=&uaddate=&legalcasesetting=&namecasesetting=&tsswitch=';
        parse_str($for_post, $_POST);

        $legacy_default_user_id = config('truefootage.mls.spark.legacy_default_user_id');
        $for_cookie = "id=$legacy_default_user_id&reordercomps=0&unknownabbreviation=%3f&nonuadseparator=%3b";
        parse_str($for_cookie, $_COOKIE);

        return $this;
    }

    /**
     * @return $this
     */
    protected function requireMlsEngineFile(): self
    {
        $mls_engine_file_php = realpath(__DIR__ . '/../../../resources/legacy/spark-synapse/mls-engines') . "/$this->mls_engine_file.php";
        require_once $mls_engine_file_php;
        return $this;
    }

    /**
     * This is the one that runs the engine file.
     *
     * @return string
     */
    protected function runCrunchIt(): string
    {
        /*
         * Note that the errors are being suppressed because in any random MLS engine,
         * there could be $some_random_variable that's being called but never defined,
         * and PHP 8+ isn't as forgiving as 5.x over these things.
         *
         * The undefined issues that look like they're in all the engines have been
         * addressed (to the best of my ability without going through every line of every file)
         * in the "hack..." function above.
         */
        return @crunchIt(
            $this->mls_data_file_contents,
            'adjustment',
            'sales',
            $this->property_types[$this->property_type],
            'Non',
            1,
            $this->effective_date,
            '0',
            0
        );
    }

    /**
     * @param string $path
     * @return bool
     * @throws Exception
     */
    public function pathExistsOnServer(string $path): bool
    {
        // preserve results since this function can be called multiple times,
        // and it interacts with the file system,
        // and the results aren't going to change during the run of the script
        if ($this->path_existence_has_been_checked) {
            return $this->path_existence_check_results;
        }
        $this->path_existence_has_been_checked = true;

        $path = FunWithText::safeString($path);
        if ($path === '') {
            throw new Exception("The path to check is empty.", 400);
        }
        if (!str_starts_with($path, '/')) {
            throw new Exception("The path must be an absolute file path.", 400);
        }

        if (!is_dir($path)) {
            return false;
        }

        $this->path_existence_check_results = true;
        return true;
    }

    /**
     * @param string $path
     * @param string $mls_file
     * @return bool
     * @throws Exception
     */
    public function mlsFileExistsOnServer(string $path, string $mls_file): bool
    {
        if (!$this->pathExistsOnServer($path)) {
            throw new Exception("The path \"$path\" does not exist on the server.", 404);
        }

        $full_path_to_file = $this->fullPathToMlsFile($path, $mls_file);
        if (!is_file($full_path_to_file)) {
            throw new Exception("The file \"$full_path_to_file\" does not exist on the server.", 404);
        }

        return true;
    }

    /**
     * @param string $path
     * @param string $mls_file
     * @return string
     * @throws Exception
     */
    public function fullPathToMlsFile(string $path, string $mls_file): string
    {
        if (!$this->pathExistsOnServer($path)) {
            throw new Exception("The path \"$path\" does not exist on the server.", 404);
        }

        $mls_file = FunWithText::safeString($mls_file);
        if ($mls_file === '') {
            throw new Exception("The MLS file name is empty.", 400);
        }

        if (!str_ends_with($path, '/')) {
            $path .= '/';
        }

        return $path . $mls_file;
    }
}
