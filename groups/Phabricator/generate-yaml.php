<?php

$projects = glob( 'projects/*/*/en.json' );
sort( $projects );

$specification = '';

foreach ( $projects as $project ) {
	[ , $project, $component, ] = explode( '/', $project );
	$stuff = [
		'BASIC' => [
			'id' => "phabricator-$project-$component",
			'label' => "Phabricator ($project > $component)",
		],
		'FILES' => [
			'sourcePattern' => "%GROUPROOT%/phabricator/projects/$project/$component/%CODE%.json"
		],
		'MANGLER' => [
			'prefix' => "$project-$component-",
		],
	];

	$specification .= yaml_emit( $stuff ) . "\n";
}

echo str_replace( "...\n", '', $specification );
