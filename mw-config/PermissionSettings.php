<?php

$wgGroupPermissions[ '*'             ][ 'edit'                    ] = false;
$wgGroupPermissions[ '*'             ][ 'createaccount'           ] = false;

$wgGroupPermissions[ 'user'          ][ 'move'                    ] = false;
$wgGroupPermissions[ 'user'          ][ 'move-subpages'           ] = false;
$wgGroupPermissions[ 'user'          ][ 'move-rootuserpages'      ] = false;
$wgGroupPermissions[ 'user'          ][ 'movefile'                ] = false;
$wgGroupPermissions[ 'user'          ][ 'move-categorypages'      ] = false;
$wgGroupPermissions[ 'user'          ][ 'createclass'             ] = false;
$wgGroupPermissions[ 'user'          ][ 'multipageedit'           ] = false;
// LiquidThreads is very fragile, disable advanced actions
$wgGroupPermissions[ 'user'          ][ 'lqt-split'               ] = false;
$wgGroupPermissions[ 'user'          ][ 'lqt-merge'               ] = false;

$wgGroupPermissions[ 'autoconfirmed' ][ 'sendemail'               ] = true;
$wgGroupPermissions[ 'autoconfirmed' ][ 'move'                    ] = true;
// Maximum of 10 moves per hour
$wgRateLimits['move']['user'] = [ 10, 3600 ];
// Increase limit for newbies from the default of 8 edits/minute
$wgRateLimits['edit']['newbie'] = [ 32, 3600 ]; // T386335

$wgGroupPermissions[ 'translator'    ][ 'editinterface'           ] = true;
$wgGroupPermissions[ 'translator'    ][ 'translate'               ] = true;
$wgGroupPermissions[ 'translator'    ][ 'translate-messagereview' ] = true;
$wgGroupPermissions[ 'translator'    ][ 'skipcaptcha'             ] = true;
$wgGroupsRemoveFromSelf[ 'translator' ] =
	[ 'translator' ];

$wgGroupPermissions[ 'offline'       ][ 'translate-import'        ] = true;

$wgGroupPermissions[ 'bot'           ][ 'skipcaptcha'             ] = true;
$wgGroupPermissions[ 'bot'           ][ 'move'                    ] = true;
$wgGroupPermissions[ 'bot'           ][ 'move-subpages'           ] = true;
$wgGroupPermissions[ 'bot'           ][ 'move-rootuserpages'      ] = true;
$wgGroupPermissions[ 'bot'           ][ 'noratelimit'             ] = true;

$wgGroupPermissions[ 'sysop'         ][ 'import'                  ] = false;
$wgGroupPermissions[ 'sysop'         ][ 'importupload'            ] = false;
$wgGroupPermissions[ 'sysop'         ][ 'replacetext'             ] = false;
$wgGroupPermissions[ 'sysop'         ][ 'delete'                  ] = true;
$wgGroupPermissions[ 'sysop'         ][ 'suppressredirect'        ] = true;
$wgGroupPermissions[ 'sysop'         ][ 'skipcaptcha'             ] = true;
$wgGroupPermissions[ 'sysop'         ][ 'pagelang'                ] = true;

$wgGroupPermissions[ 'bureaucrat'    ][ 'deletelogentry'          ] = true;
$wgGroupPermissions[ 'bureaucrat'    ][ 'deleterevision'          ] = true;
$wgGroupPermissions[ 'bureaucrat'    ][ 'interwiki'               ] = true;
$wgGroupPermissions[ 'bureaucrat'    ][ 'nuke'                    ] = true;
$wgGroupPermissions[ 'bureaucrat'    ][ 'pagetranslation'         ] = true;
$wgGroupPermissions[ 'bureaucrat'    ][ 'userrights'              ] = false;
$wgGroupPermissions[ 'bureaucrat'    ][ 'suppressionlog'          ] = true;
$wgGroupPermissions[ 'bureaucrat'    ][ 'translate-sandboxmanage' ] = true;
$wgGroupPermissions[ 'bureaucrat'    ][ 'invitesignup'            ] = true;
$wgGroupPermissions[ 'bureaucrat'    ][ 'suppressrevision'        ] = true;
$wgRemoveGroups[ 'bureaucrat' ] = $wgAddGroups[ 'bureaucrat' ] =
	[ 'sysop', 'translator', 'bot', 'offline' ];

$wgGroupPermissions[ 'transadmin'    ][ 'translate-manage'        ] = true;
$wgGroupPermissions[ 'transadmin'    ][ 'replacetext'             ] = true;
$wgGroupPermissions[ 'transadmin'    ][ 'translate-import'        ] = true;
$wgGroupPermissions[ 'transadmin'    ][ 'nuke'                    ] = true;
$wgGroupPermissions[ 'transadmin'    ][ 'pagetranslation'         ] = true;
$wgGroupPermissions[ 'transadmin'    ][ 'move'                    ] = true;
$wgGroupPermissions[ 'transadmin'    ][ 'move-subpages'           ] = true;
$wgGroupPermissions[ 'transadmin'    ][ 'suppressredirect'        ] = true;

$wgGroupPermissions[ 'staff'         ][ 'reset-passwords'         ] = true;
$wgGroupPermissions[ 'staff'         ][ 'usermerge'               ] = true;

$wgAddGroups[ 'staff' ] = $wgRemoveGroups[ 'staff' ] = true;

// Remove preset extension groups
$wgExtensionFunctions[] = static function () use ( &$wgGroupPermissions ) {
	$wgGroupPermissions[ 'staff' ] = array_merge(
		$wgGroupPermissions[ 'push-subscription-manager' ],
		$wgGroupPermissions[ 'checkuser' ],
		$wgGroupPermissions[ 'staff' ]
	);
	unset(
		$wgGroupPermissions[ 'push-subscription-manager' ],
		$wgGroupPermissions[ 'checkuser' ]
	);
};
