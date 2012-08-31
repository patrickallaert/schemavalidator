# Schema Validator

## Purpose

Validates all ezxmltext attributes against a provided schema file (XSD) and
reports errors found.

## Important note:

The schema file is in **alpha** stage!

## Usage

### Run validation with built-in schema:

    ./extension/schemavalidator/bin/php/schemavalidator.php

### Run validation with the very latest available schema on GitHub:

    ./extension/schemavalidator/bin/php/schemavalidator.php --live

### Run validation with a specific schema:

    ./extension/schemavalidator/bin/php/schemavalidator.php --schema=path/to/schema.xsd
