<?php

return [

    /*
    |--------------------------------------------------------------------------
    | True Footage - MLS - Engine State Map
    |--------------------------------------------------------------------------
    |
    | For each MLS, maps which states are covered and what the base
    | {state}_{engine} naming convention will be.
    |
    | This covers cases where 1 MLS runs through multiple states, but there's
    | only the 1 engine file for all of them.
    |
    | Note that the format is set up this way specifically to future-proof
    | a potential issue that doesn't exist yet but could.
    |
    | For example, right now there's ARMLS for Arizona, and what if some day
    | Arkansas decides to also have an ARMLS. That would need to be a
    | different engine config called AR_ARMLS that would be different than
    | the current AZ_ARMLS format for Arizona.
    |
    */

    'engine_state_map' => [
        'ABoR_ACTRIS' => [
            'TX' => 'TX_ABoR_ACTRIS',
        ],
        'ARMLS' => [
            'AZ' => 'AZ_ARMLS',
        ],
        'BAREISPlus' => [
            'CA' => 'CA_BAREISPlus',
        ],
        'BeachesMLS_Flex' => [
            'FL' => 'FL_BeachesMLS_Flex',
        ],
        'BeachesMLS_Matrix' => [
            'FL' => 'FL_BeachesMLS_Matrix',
        ],
        'Bright' => [
            'PA' => 'PA_Bright',
            'DC' => 'PA_Bright',
            'DE' => 'PA_Bright',
            'MA' => 'PA_Bright',
            'MD' => 'PA_Bright',
            'NC' => 'PA_Bright',
            'NJ' => 'PA_Bright',
            'SC' => 'PA_Bright',
            'VA' => 'PA_Bright',
            'WV' => 'PA_Bright',
        ],
        'CAAR' => [
            'VA' => 'VA_CAAR',
        ],
        'CanopyMLS' => [
            'NC' => 'NC_CanopyMLS',
            'SC' => 'NC_CanopyMLS',
        ],
        'CLAW' => [
            'CA' => 'CA_CLAW',
        ],
        'ColumbusMLS' => [
            'OH' => 'OH_ColumbusMLS',
        ],
        'CRMLS_Flex' => [
            'CA' => 'CA_CRMLS_Flex',
        ],
        'CRMLS_Matrix' => [
            'CA' => 'CA_CRMLS_Matrix',
        ],
        'CRMLS_Paragon' => [
            'CA' => 'CA_CRMLS_Paragon',
        ],
        'CVRMLS' => [
            'VA' => 'VA_CVRMLS',
        ],
        'FMLS' => [
            'GA' => 'GA_FMLS',
        ],
        'GAMLS' => [
            'GA' => 'GA_GAMLS',
        ],
        'GreenvilleMLS' => [
            'SC' => 'SC_GreenvilleMLS',
        ],
        'GSMLS' => [
            'NJ' => 'NJ_GSMLS',
        ],
        'GSREIN' => [
            'LA' => 'LA_GSREIN',
        ],
        'HARMLS' => [
            'TX' => 'TX_HARMLS',
        ],
        'HeartlandMLSNew' => [
            'MO' => 'MO_HeartlandMLSNew',
            'KS' => 'MO_HeartlandMLSNew',
        ],
        'IMLS' => [
            'ID' => 'ID_IMLS',
        ],
        'IRES' => [
            'CO' => 'CO_IRES',
        ],
        'IRMLS' => [
            'IN' => 'IN_IRMLS',
        ],
        'LVR' => [
            'NV' => 'NV_LVR',
        ],
        'MARIS' => [
            'MO' => 'MO_MARIS',
            'IL' => 'MO_MARIS',
        ],
        'MAXEBRD' => [
            'CA' => 'CA_MAXEBRD',
        ],
        'MetroMLS' => [
            'WI' => 'WI_MetroMLS',
            'MN' => 'WI_MetroMLS',
        ],
        'MIBOR' => [
            'IN' => 'IN_MIBOR',
        ],
        'MichRIC' => [
            'MI' => 'MI_MichRIC',
        ],
        'MLSListings' => [
            'CA' => 'CA_MLSListings',
        ],
        'MLSNow' => [
            'OH' => 'OH_MLSNow',
            'PA' => 'OH_MLSNow',
            'WV' => 'OH_MLSNow',
        ],
        'MLSPIN' => [
            'MA' => 'MA_MLSPIN',
            'NH' => 'MA_MLSPIN',
        ],
        'MLSSAZ' => [
            'AZ' => 'AZ_MLSSAZ',
        ],
        'Monmouth_Ocean' => [
            'NJ' => 'NJ_Monmouth_Ocean',
        ],
        'MRED' => [
            'IL' => 'IL_MRED',
        ],
        'NEFAR' => [
            'FL' => 'FL_NEFAR',
        ],
        'NEREN' => [
            'NH' => 'NH_NEREN',
            'MA' => 'NH_NEREN',
            'ME' => 'NH_NEREN',
            'VT' => 'NH_NEREN',
        ],
        'Northstar' => [
            'MN' => 'MN_Northstar',
        ],
        'Northstar_Paragon' => [
            'MN' => 'MN_Northstar_Paragon',
        ],
        'NTREIS' => [
            'TX' => 'TX_NTREIS',
        ],
        'NWMLS' => [
            'WA' => 'WA_NWMLS',
        ],
        'OneKeyMLS_Stratus' => [
            'NY' => 'NY_OneKeyMLS_Stratus',
        ],
        'PPMLS' => [
            'CO' => 'CO_PPMLS',
        ],
        'ProspectorPlus' => [
            'CA' => 'CA_ProspectorPlus',
        ],
        'Realcomp' => [
            'MI' => 'MI_Realcomp',
        ],
        'RealTracs' => [
            'TN' => 'TN_RealTracs',
            'AL' => 'TN_RealTracs',
        ],
        'REColorado' => [
            'CO' => 'CO_REColorado',
        ],
        'REIN' => [
            'VA' => 'VA_REIN',
        ],
        'RMLS' => [
            'OR' => 'OR_RMLS',
            'WA' => 'OR_RMLS',
        ],
        'RMLS_Paragon' => [
            'OR' => 'OR_RMLS_Paragon',
            'WA' => 'OR_RMLS_Paragon',
        ],
        'RVAR' => [
            'VA' => 'VA_RVAR',
        ],
        'SABOR' => [
            'TX' => 'TX_SABOR',
        ],
        'SanDiegoMLS_Paragon' => [
            'CA' => 'CA_SanDiegoMLS_Paragon',
        ],
        'SEF' => [
            'FL' => 'FL_SEF',
        ],
        'SFARMLSPlus' => [
            'CA' => 'CA_SFARMLSPlus',
        ],
        'SmartMLS' => [
            'CT' => 'CT_SmartMLS',
        ],
        'SpartanburgMLS' => [
            'SC' => 'SC_SpartanburgMLS',
        ],
        'StatewideMLS' => [
            'RI' => 'RI_StatewideMLS',
            'MA' => 'RI_StatewideMLS',
        ],
        'StellarMLS' => [
            'FL' => 'FL_StellarMLS',
        ],
        'SWFLAMLS' => [
            'FL' => 'FL_SWFLAMLS',
        ],
        'SWMLS_GAAR' => [
            'NM' => 'NM_SWMLS_GAAR',
        ],
        'Triangle' => [
            'NC' => 'NC_Triangle',
        ],
        'Trident' => [
            'SC' => 'SC_Trident',
        ],
        'WestPenn' => [
            'PA' => 'PA_WestPenn',
        ],
        'WFRMLS' => [
            'UT' => 'UT_WFRMLS',
            'AZ' => 'UT_WFRMLS',
            'CO' => 'UT_WFRMLS',
            'ID' => 'UT_WFRMLS',
            'WY' => 'UT_WFRMLS',
        ],
        'WVMLS' => [
            'OR' => 'OR_WVMLS',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | True Footage - MLS - Static and Dynamic Engines
    |--------------------------------------------------------------------------
    |
    | Static engines are the original ones hard-coded from Spark, or any
    | others that may be added to work in the same fashion.
    |
    | Dynamic engines are data driven. As of this writing, it's not determined
    | how exactly, so it could be database or coded as configs.
    |
    */

    'engines' => [
        'static' => [
            'AZ_ARMLS',
            'AZ_MLSSAZ',
            'CA_BAREISPlus',
            'CA_CLAW',
            'CA_CRMLS_Flex',
            'CA_CRMLS_Matrix',
            'CA_CRMLS_Paragon',
            'CA_MAXEBRD',
            'CA_MLSListings',
            'CA_ProspectorPlus',
            'CA_SanDiegoMLS_Paragon',
            'CA_SFARMLSPlus',
            'CO_IRES',
            'CO_PPMLS',
            'CO_REColorado',
            'CT_SmartMLS',
            'FL_BeachesMLS_Flex',
            'FL_BeachesMLS_Matrix',
            'FL_NEFAR',
            'FL_SEF',
            'FL_StellarMLS',
            'FL_SWFLAMLS',
            'GA_FMLS',
            'GA_GAMLS',
            'ID_IMLS',
            'IL_MRED',
            'IN_IRMLS',
            'IN_MIBOR',
            'LA_GSREIN',
            'MA_MLSPIN',
            'MI_MichRIC',
            'MI_Realcomp',
            'MN_Northstar',
            'MN_Northstar_Paragon',
            'MO_HeartlandMLSNew',
            'MO_MARIS',
            'NC_CanopyMLS',
            'NC_Triangle',
            'NH_NEREN',
            'NJ_GSMLS',
            'NJ_Monmouth_Ocean',
            'NM_SWMLS_GAAR',
            'NV_LVR',
            'NY_OneKeyMLS_Stratus',
            'OH_ColumbusMLS',
            'OH_MLSNow',
            'OR_RMLS',
            'OR_RMLS_Paragon',
            'OR_WVMLS',
            'PA_Bright',
            'PA_WestPenn',
            'RI_StatewideMLS',
            'SC_GreenvilleMLS',
            'SC_SpartanburgMLS',
            'SC_Trident',
            'TN_RealTracs',
            'TX_ABoR_ACTRIS',
            'TX_HARMLS',
            'TX_NTREIS',
            'TX_SABOR',
            'UT_WFRMLS',
            'VA_CAAR',
            'VA_CVRMLS',
            'VA_REIN',
            'VA_RVAR',
            'WA_NWMLS',
            'WI_MetroMLS',
        ],

        'dynamic' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | True Footage - MLS - States
    |--------------------------------------------------------------------------
    |
    | States that we have MLS engines for.
    |
    */

    'states' => [
        'AL', 'AZ', 'CA', 'CO', 'CT', 'DC', 'DE', 'FL', 'GA', 'ID',
        'IL', 'IN', 'KS', 'LA', 'MA', 'MD', 'ME', 'MI', 'MN', 'MO',
        'NC', 'NH', 'NJ', 'NM', 'NV', 'NY', 'OH', 'OR', 'PA', 'RI',
        'SC', 'TN', 'TX', 'UT', 'VA', 'VT', 'WA', 'WI', 'WV', 'WY',
    ],

    /*
    |--------------------------------------------------------------------------
    | True Footage - MLS - Property types
    |--------------------------------------------------------------------------
    */

    'property_types' => [
        'SFR'          => '1004 - SFR',
        'Condo'        => '1073 - Condo',
        'Multi'        => '1025 - Multi',
        'Manufactured' => '1004C - Manu',
    ],

    /*
    |--------------------------------------------------------------------------
    | True Footage - MLS - Spark legacy
    |--------------------------------------------------------------------------
    |
    | Spark values in case they're needed. For example the default user ID.
    |
    */

    'spark' => [
        'legacy_default_user_id' => env('SPARK_LEGACY_DEFAULT_USER_ID'),
    ],

];
