<?php
declare( strict_types = 1 );

use JsonSchema\Constraints\Constraint;
use JsonSchema\Validator;
use Symfony\Component\Yaml\Yaml;

require 'vendor/autoload.php';

$data = Yaml::parse( file_get_contents( 'repoconfig.yaml' ) );
$schema = Yaml::parse( file_get_contents( 'repong/repoconfig.schema.yaml' ) );

$validator = new Validator();
$validator->validate( $data, $schema, Constraint::CHECK_MODE_TYPE_CAST );

if ( $validator->isValid() ) {
	echo "repoconfig.yaml validates against the schema.\n";
	exit( 0 );
} else {
	echo "repoconfig.yaml does not validate. Violations:\n";
	foreach ( $validator->getErrors() as $error ) {
		printf( "[%s] %s\n", $error['property'], $error['message'] );
	}
	exit( 1 );
}
