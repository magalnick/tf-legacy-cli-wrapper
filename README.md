<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# PHP Framework for calling legacy MLS data normalization operations via command line

## Special note

The actual legacy MLS-based PHP files *are not* present in this repository, as they're proprietary and owned by someone other than me.

This CLI wrapper is the utility so that a modern application can call the legacy code through a UNIX/Linux command line and have the original code still execute, safe from the various security vulnerabilities contained within.

## Installation

All commands are designed to be called on the Linux command line using PHP.
- The framework requires PHP 8.0 or higher.
- Coding and testing were done in PHP 8.1, so this is the recommended version.

After cloning the repository, go to the git root directory for the project and create an `.env` file.
- If this is a development environment, run the command:
  - ```echo dev > .env```
- If this is a production environment, run the command:
  - ```echo production > .env```

## Calling the MLS field lists

### Purpose

When uploading a CSV file containing property data (subject, comps, etc.),
we need to be able to validate that the data fields match what's expected for the MLS being used.

### The base command line calls are:

#### Required fields

  ```bash
  php /path/to/project/folder/artisan cli:call-mls-required-field-list {mls} {state} {property_type} {--special-field-modifier=} {--north-star-new} {--pretty}
  ```

#### Reordered fields

  ```bash
  php /path/to/project/folder/artisan cli:call-mls-reordered-field-list {mls} {state} {property_type} {--special-field-modifier=} {--north-star-new} {--pretty}
  ```

...where:
- _(required)_ `{mls}` is the MLS being called, eg. ARMLS, SanDiegoMLS_Paragon, etc.
  - Note that this value is case-sensitive so `ARMLS` is valid, but `armls` is not.
- _(required)_ `{state}` is the two-letter code for the property state being appraised, eg. `CA`, `CO`, `OR`, etc.
  - Note that this value is case-sensitive so `CA` is valid, but `ca` is not.
- _(required)_ `{property_type}` is one of: `SFR`, `Condo`, `Multi`, `Manufactured`. This value is case-sensitive.
- _(optional)_ `{--special-field-modifier=}` is an integer that, if included, must be 0, 1, or 2.
  - Note that this value will need to be figured out based on some legacy Spark logic that has been copied into the TrueSpark API code.
  - It's because of this value that this set of CLI wrappers were needed instead of reusing the existing Synapse API call.
- _(optional)_ `{--north-star-new}` is a boolean flag that is only allowed if the `{mls}` value is `Northstar`.
  - Note that this value will be originally passed in from the web interface based on some user action.
- _(optional)_ `{--pretty}` is a boolean flag that will output the JSON format as "pretty" instead of the default minimized single line.

The command performs basic validation on the incoming data. For example:
- The `{mls}`, `{state}`, and `{property_type}` values must exist in their respective pre-approved lists.
- The `{mls}` and `{state}` combination must exist in a pre-approved list.
- `{special-field-modifier}`, if included, must be 0, 1, or 2.

Successful output of the command will be a JSON string of the data from `{mls_file}`,
reorganized to its normalized value, and an exit code of 0.

If there are any errors, the output will be a RESTful style JSON error showing what went wrong.
The exit code will be the HTTP response code for the error, eg. 400 or 404.

### The two different commands

#### Required fields

This command returns the bare minimum list of fields for the MLS that are needed when uploading a CSV file.

#### Reordered fields

This command returns the list of all recognized fields that can be sent to the MLS engine call
in the order that they're needed.

If a field from this list is missing from the CSV, that's OK as long as it's not one of the required fields.

### Examples

#### Example call to the required fields command:

  ```bash
  php /path/to/project/folder/artisan cli:call-mls-required-field-list SanDiegoMLS_Paragon CA SFR --special-field-modifier=1 --pretty
  ```

...where:
- `{mls}` = SanDiegoMLS_Paragon
- `{state}` = CA
- `{property_type}` = SFR
- `{special-field-modifier=}` = 1
- The `{--north-star-new}` option is omitted since the `{mls}` value is not "Northstar".
- The `{--pretty}` option is included to make the output more readable.

#### Successfl output:

  ```json
  [
    "MLS #",
    "Address",
    "City",
    "State",
    "Zip",
    "County",
    "Listing Date",
    "Days On Market",
    "Baths Full",
    "Baths Half",
    "Bedrooms",
    "Estimated Square Feet",
    "Assessors Parcel #",
    "Close of Escrow Date",
    "Attached Style",
    "Status Date",
    "Ownership",
    "Pool",
    "Status",
    "Complex\/Park",
    "Pending Date",
    "Year Built",
    "Architectural Style",
    "Complex Features",
    "Cooling",
    "Heat Equipment",
    "Heat Source",
    "Equipment",
    "Water Heater Type",
    "Patio",
    "Miscellaneous",
    "Fencing",
    "Fireplaces(s)",
    "Parkng Non-Garaged Spaces",
    "Parking Garage Spaces",
    "Parking Non-Garage",
    "Spa",
    "Max Search Price",
    "Financing",
    "Unit #\/Space #",
    "Parking Garage",
    "Original Price",
    "Price Date",
    "View",
    "Guest House",
    "Security",
    "Listing Type",
    "Searchable Rooms"
  ]
  ```

#### Example call to the command with an error that the `--north-star-new` flag was included by mistake:
  ```bash
  php /path/to/project/folder/artisan cli:call-mls-required-field-list SanDiegoMLS_Paragon CA SFR --special-field-modifier=1 --north-star-new
  ```

#### Error output:

  ```json
  {"success":false,"errors":["The '--north-star-new' option is only allowed if the MLS is Northstar."],"code":400}
  ```

## Calling the MLS engines

### The base command line call is:

  ```bash
  php /path/to/project/folder/artisan cli:call-mls-engine {mls} {state} {property_type} {effective_date} {mls_file} {--p|path=} {--north-star-new}
  ```

...where:
- _(required)_ `{mls}` is the MLS being called, eg. ARMLS, SanDiegoMLS_Paragon, etc.
  - Note that this value is case-sensitive so `ARMLS` is valid, but `armls` is not.
- _(required)_ `{state}` is the two-letter code for the property state being appraised, eg. `CA`, `CO`, `OR`, etc.
  - Note that this value is case-sensitive so `CA` is valid, but `ca` is not.
- _(required)_ `{property_type}` is one of: `SFR`, `Condo`, `Multi`, `Manufactured`. This value is case-sensitive.
- _(required)_ `{effective_date}` must be in a valid date format, like yyyy-mm-dd.
- _(required)_ `{mls_file}` is the name of the JSON data file on the server containing the parsed CSV data.
- _(optional)_ `{--p|path=}` is the path on the server to `{mls_file}`. Even though this is optional, it is highly recommended. Omitting this value will default it to `/tmp`.
- _(optional)_ `{--north-star-new}` is a boolean flag that is only allowed if the `{mls}` value is `Northstar`.
  - Note that this value will be originally passed in from the web interface based on some user action.

The command performs basic validation on the incoming data. For example:
- The `{mls}`, `{state}`, and `{property_type}` values must exist in their respective pre-approved lists.
- The `{mls}` and `{state}` combination must exist in a pre-approved list.
- `{effective_date}` must be in a valid date format.
- `{path}` must exist as a directory on the server, along with a file called `{mls_file}` in that directory.

Successful output of the command will be a JSON string of the data from `{mls_file}`,
reorganized to its normalized value, and an exit code of 0.

If there are any errors, the output will be a RESTful style JSON error showing what went wrong.
The exit code will be the HTTP response code for the error, eg. 400 or 404.

### Examples

#### Example call to the command:

  ```bash
  php /path/to/project/folder/artisan cli:call-mls-engine ARMLS AZ SFR 2022-03-14 az-armls-70168-2022-08-29.json -p '/tmp/data-samples/'
  ```

...where:
- `{mls}` = ARMLS
- `{state}` = AZ
- `{property_type}` = SFR
- `{effective_date}` = 2022-03-14
- `{mls_file}` = az-armls-70168-2022-08-29.json
- `{path=}` = '/tmp/data-samples/'
- The `{--north-star-new}` option is omitted since the `{mls}` value is not "Northstar".

#### Example call to the command with an error that the MLS and state don't match:
  ```bash
  php /path/to/project/folder/artisan cli:call-mls-engine SanDiegoMLS_Paragon CO SFR 2022-03-14 az-armls-70168-2022-08-29.json -p '/tmp/data-samples/'
  ```

#### Error output:

  ```json
  {"success":false,"errors":["The state of CO is not covered by the MLS SanDiegoMLS_Paragon"],"code":400}
  ```

## Putting this all together

1. Upload a CSV file for an MLS.
2. Figure out what values are needed (if any) for `--special-field-modifier` and `--north-star-new`.
3. Call the MLS field list command line script for the required fields.
4. Validate that all required fields are in the CSV file.
5. Call the MLS field list command line script for the reordered fields.
6. Extract the columns from the CSV that are in the reordered fields list, and put them in the right order.
7. Save the extracted data as JSON to a file on the server that can be used as the `mls_file` argument for the MLS engine script.
8. Call the MLS engine command line script.
