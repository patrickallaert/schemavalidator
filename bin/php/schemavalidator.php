#!/usr/bin/env php
<?php
/**
 * File containing the schemavalidation.php script.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version 0.1
 */

require 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance(
    array(
        'description' => "eZ Publish ezxml schema validator\n.",
        'use-session' => false,
        'use-modules' => false,
    )
);

$script->startup();
$options = $script->getOptions(
    "[live][schema:]",
    array(
        "live" => "Use the most recent schema available on GitHub",
        "schema" => "XSD file to use for validation",
    )
);

$script->initialize();

$schema = file_get_contents(
    $options["live"] ?
        "https://raw.github.com/gist/aa8a1ab1666d7d387039/ezxml.xsd" :
        ( $options["schema"] ?: ( __DIR__ . "/../../schemas/ezxml.xsd" ) )
);

libxml_use_internal_errors( true );

$doc = new DOMDocument;
$errorsFound = false;

foreach (
    eZDB::instance()->arrayQuery(
        "SELECT data_text FROM ezcontentobject_attribute WHERE data_type_string = 'ezxmltext'"
    )
    as $row
)
{
    if ( empty( $row["data_text"] ) )
        continue;

    $doc->loadXML( $row["data_text"] );
    libxml_clear_errors();
    if ( !$doc->schemaValidateSource( $schema ) )
    {
        $errorsFound = true;
        echo "===================================\n",
             $row["data_text"],
             "\n-----------------------------------\n";

        foreach ( libxml_get_errors() as $error )
            echo trim( $error->message ), "\n";

        echo "\n";
    }
}

if ( !$errorsFound )
    echo "No error has been found!\n";

$script->shutdown();
