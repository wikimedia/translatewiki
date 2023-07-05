<?php
/**
 * List of validations that should not be performed.
 *
 * @todo Use YAML?
 * @file
 * @author Niklas Laxström
 * @license GPL-2.0-or-later
 */

/**
 * The array takes input of arrays which define constraints. Validations which match
 * those constrains are skipped. Possible constrains are <tt>group</tt>,
 * <tt>check</tt>, <tt>code</tt> and <tt>message</tt>.
 */
$validationExclusionList = [
	[
		'group' => 'core',
		'check' => 'variable',
		'message' => [
			'search-thumbnail-extra-namespaces-message', // Optional PLURAL parameter $4
		]
	],
	[
		'group' => 'core',
		'check' => 'plural',
		'code' => [ 'fr' ],
		'message' => [
			'viewcount', // PLURAL not needed in French
		]
	],
	[
		'group' => 'ext-abusefilter-user',
		'check' => 'variable',
		'message' => [
			'abusefilter-edit-lastmod-text', // Optional username parameter for GENDER, optional time parameters
			'abusefilter-reautoconfirm-none', // Optional username parameter for GENDER
		]
	],
	[
		'group' => 'ext-articlefeedbackv5',
		'check' => 'variable',
		'message' => [
			'articlefeedbackv5-activity-item-request', // Optional GENDER parameter
			'articlefeedbackv5-activity-item-clear-flags', // Optional GENDER parameter
		]
	],
	[
		'group' => 'ext-babel-user',
		'check' => 'variable',
		'message' => [
			'babel', // Optional GENDER parameter
			'babel-0', // Optional GENDER parameter
			'babel-1', // Optional GENDER parameter
			'babel-2', // Optional GENDER parameter
			'babel-3', // Optional GENDER parameter
			'babel-4', // Optional GENDER parameter
			'babel-5', // Optional GENDER parameter
			'babel-N', // Optional GENDER parameter
			'babel-0-n', // Optional GENDER parameter
			'babel-1-n', // Optional GENDER parameter
			'babel-2-n', // Optional GENDER parameter
			'babel-3-n', // Optional GENDER parameter
			'babel-4-n', // Optional GENDER parameter
			'babel-5-n', // Optional GENDER parameter
			'babel-N-n', // Optional GENDER parameter
		]
	],
	[
		'group' => 'ext-call',
		'check' => 'links',
		'message' => [
			'call-text', // Contains links that are translated
		]
	],
	[
		'group' => 'ext-categorysortheaders',
		'check' => 'links',
		'message' => [
			'categorysortheaders-desc', // Contains links that are translated
		]
	],
	[
		'group' => 'ext-categorytree-user',
		'check' => 'variable',
		'message' => [
			'Categorytree-member-counts', // Optional counts: $4, and $5
		]
	],
	[
		'group' => 'ext-centralauth-user',
		'check' => 'links',
		'message' => [
			'centralauth-readmore-text', // Contains link to page that may be available in a translated version
			'centralauth-finish-problems', // Contains link to page that may be available in a translated version
		]
	],
	[
		'group' => 'ext-centralauth-user',
		'check' => 'variable',
		'message' => [
			'centralauth-centralautologin-logged-in', // Optional GENDER parameter
		]
	],
	[
		'group' => 'ext-checkpoint',
		'check' => 'links',
		'message' => [
			'checkpoint-notice', // Contains link parts that may need translation
		]
	],
	[
		'group' => 'ext-checkuser-user',
		'check' => 'variable',
		'message' => [
			'group-checkuser-member', // Optional GENDER parameter
		]
	],
	[
		'group' => 'ext-citethispage',
		'check' => 'links',
		'message' => [
			'citethispage-content', // Contains link parts that may need translation
		]
	],
	[
		'group' => 'ext-codereview',
		'check' => 'variable',
		'message' => [
			'code-stats-main', // Optional time parameters
			'group-svnadmins-member', // Optional GENDER parameter
		]
	],
	[
		'group' => 'ext-confirmaccount',
		'check' => 'variable',
		'message' => [
			'requestaccount-email-body', // Optional time parameters
			'confirmaccount-reject', // Optional time parameters
			'confirmaccount-held', // Optional time parameters
		]
	],
	[
		'group' => 'ext-configure',
		'check' => 'variable',
		'message' => [
			'configure-condition-description-4', // Optional parameter for PLURAL
			'configure-edit-old', // Optional time parameters
			'configure-old-summary-datetime', // Optional time parameters
			'configure-viewconfig-line', // Optional time parameters
		]
	],
	[
		'group' => 'ext-configure-settings',
		'check' => 'links',
		'message' => [
			'configure-setting-wgMaxRedirectLinksRetrieved', // Translatable link
		]
	],
	[
		'group' => 'ext-contributionseditcount',
		'check' => 'variable',
		'message' => [
			'contributionseditcount', // Optional GENDER parameter
		]
	],
	[
		'group' => 'ext-echo-interface',
		'check' => 'variable',
		'message' => [
			'notification-header-mention-failure-too-many', // GENDER parameter not needed e.g. in uk
			'notification-header-user-rights-add-and-remove', // Optional PLURAL parameters $3, $5
			'notification-header-user-rights-add-only', // Optional PLURAL parameter $3
			'notification-header-user-rights-remove-only', // Optional PLURAL parameter $3
		]
	],
	[
		'group' => 'ext-editsubpages',
		'check' => 'links',
		'message' => [
			'unlockedpages', // Contains links that are translated
		]
	],
	[
		'group' => 'ext-flagpage',
		'check' => 'links',
		'message' => [
			'flagpage-templatelist', // Contains link in HTML comment
		]
	],
	[
		'group' => 'ext-flaggedrevs-flaggedrevs',
		'check' => 'variable',
		'message' => [
			'group-editor-member', // Optional GENDER parameter
			'group-reviewer-member', // Optional GENDER parameter
			'group-autoreview-member', // Optional GENDER parameter
		]
	],
	[
		'group' => 'ext-flaggedrevs-configuredpages',
		'check' => 'variable',
		'message' => [
			'configuredpages-list', // Parameter $1 only used when required for plural
		]
	],
	[
		'group' => 'ext-flaggedrevs-pendingchanges',
		'check' => 'variable',
		'message' => [
			'pendingchanges-list', // Parameter $1 only used when required for plural
		]
	],
	[
		'group' => 'ext-flaggedrevs-qualityoversight',
		'check' => 'variable',
		'message' => [
			'qualityoversight-list', // Parameter $1 only used when required for plural
		]
	],
	[
		'group' => 'ext-flaggedrevs-stabilization',
		'check' => 'variable',
		'message' => [
			'stabilize-expiring', // Optional time parameters
		]
	],
	[
		'group' => 'ext-flaggedrevs-stablepages',
		'check' => 'variable',
		'message' => [
			'stablepages-list', // Parameter $1 only used when required for plural
		]
	],
	[
		'group' => 'ext-flaggedrevs-unreviewedpages',
		'check' => 'variable',
		'message' => [
			'unreviewedpages-list', // Parameter $1 only used when required for plural
		]
	],
	[
		'group' => 'ext-growthexperiments-homepage',
		'check' => 'variable',
		'message' => [
			'growthexperiments-homepage-suggestededits-footer-suffix', // Parameter $1 can be replaced to disable formatting
		]
	],
	[
		'group' => 'ext-linkfilter',
		'check' => 'variable',
		'message' => [
			'group-linkadmin-member', // Optional GENDER parameter
		]
	],
	[
		'group' => 'ext-liquidthreads',
		'check' => 'variable',
		'message' => [
			'lqt-feed-title-all-from', // Optional PLURAL parameter ($2)
			'lqt-feed-title-new-threads-from', // Optional PLURAL parameter ($2)
			'lqt-feed-title-replies-from', // Optional PLURAL parameter ($2)
			'lqt-thread-edited-others', // Optional date and time parameters ($3/$4)
			'lqt-thread-edited-author', // Optional count, date and time parameters ($2/$3/$4)
		]
	],
	[
		'group' => 'ext-livetranslate',
		'check' => 'variable',
		'message' => [
			'group-tmxadmin-member', // Optional GENDER parameter
		]
	],
	[
		'group' => 'ext-mirrortools',
		'check' => 'variable',
		'message' => [
			'group-MirrorTools-member', // Optional GENDER parameter
		]
	],
	[
		'group' => 'ext-newusernotification',
		'check' => 'variable',
		'message' => [
			'newusernotifbody', // Optional time parameters
		]
	],
	[
		'group' => 'ext-onlinestatus',
		'check' => 'variable',
		'message' => [
			'onlinestatus-subtitle-offline', // Optional GENDER parameter
			'onlinestatus-subtitle-online', // Optional GENDER parameter
		]
	],
	[
		'group' => 'ext-openstackmanager',
		'check' => 'plural',
		'code' => [ 'fr' ],
		'message' => [
			'openstackmanager-instancetypelabel', // PLURAL not needed in French
		]
	],
	[
		'group' => 'ext-patroller',
		'check' => 'variable',
		'message' => [
			'group-patroller-member', // Optional GENDER parameter
		]
	],
	[
		'group' => 'ext-push',
		'check' => 'variable',
		'message' => [
			'group-pusher-member', // Optional GENDER parameter
			'group-bulkpusher-member', // Optional GENDER parameter
			'group-filepusher-member', // Optional GENDER parameter
		]
	],
	[
		'group' => 'mwgithub-semanticmediawiki',
		'check' => 'links',
		'message' => [
			'smw_allows_pattern', // Contains property links
			'smw_notinenum', // Contains property links
			'smw-datavalue-external-identifier-formatter-missing', // Contains property links
			'smw-datavalue-reference-invalid-fields-definition', // Contains namespace links
			'smw-edit-protection', // Contains property links
			'smw-property-predefined-boo', // Contains namespace links
			'smw-property-predefined-dat', // Contains namespace links
			'smw-property-predefined-eid', // Contains namespace links
			'smw-property-predefined-keyw', // Contains namespace links
			'smw-property-predefined-list', // Contains namespace links
			'smw-property-predefined-long-errp', // Contains property links
			'smw-property-predefined-long-pefu', // Contains namespace links
			'smw-property-predefined-num', // Contains namespace links
			'smw-property-predefined-qty', // Contains namespace links
			'smw-property-predefined-uri', // Contains namespace links
			'smw-type-anu', // Contains namespace links
			'smw-type-cod', // Contains namespace links
			'smw-type-eid', // Contains property links
			'smw-type-keyw', // Contains namespace links
			'smw-type-mlt-rec', // Contains property links
			'smw-statistics-query-inline', // Contains property links
			'smw-statistics-subobject-count', // Contains property links
		]
	],
	[
		'group' => 'mwgithub-semanticcite',
		'check' => 'links',
		'message' => [
			'sci-metadata-search-intro-doi', // Contains property links
			'sci-metadata-search-intro-pubmed', // Contains property links
			'sci-metadata-search-intro-oclc', // Contains property links
			'sci-metadata-search-intro-ol', // Contains property links
			'sci-metadata-search-intro-viaf', // Contains property links
		]
	],
	[
		'group' => 'ext-semanticwatchlist',
		'check' => 'variable',
		'message' => [
			'group-swladmins-member', // Optional GENDER parameter
		]
	],
	[
		'group' => 'ext-qpoll',
		'check' => 'variable',
		'message' => [
			'qp_user_polls_link', // Optional GENDER parameter
			'qp_user_missing_polls_link', // Optional GENDER parameter
		]
	],
	[
		'group' => 'ext-regexblock',
		'check' => 'variable',
		'message' => [
			'regexblock-match-stats-record', // Optional time parameters
			'regexblock-view-time', // Optional time parameters
		]
	],
	[
		'group' => 'ext-survey',
		'check' => 'variable',
		'message' => [
			'group-surveyadmin-member', // Optional GENDER parameter
			'group-surveysubmit-member', // Optional GENDER parameter
		]
	],
	[
		'group' => 'ext-timedmediahandler-user',
		'check' => 'links',
		'message' => [
			'timedmedia-subtitle-new-desc', // Contains links that are translated
		]
	],
	[
		'group' => 'ext-titleblacklist',
		'check' => 'variable',
		'code' => [
			'gan', 'gan-hans', 'gan-hant', 'gn', 'hak', 'hu', 'ja',
			'ka', 'kk-arab', 'kk-cyrl', 'kk-latn', 'ko', 'lzh', 'mn', 'ms', 'sah', 'sq',
			'tet', 'th', 'wuu', 'xmf', 'yue', 'zh', 'zh-classical', 'zh-cn', 'zh-hans',
			'zh-hant', 'zh-hk', 'zh-sg', 'zh-tw', 'zh-yue'
		],
		'message' => [
			'titleblacklist-invalid', // Param only used in plural
		]
	],
	[
		'group' => 'ext-translate-core',
		'check' => 'links',
		'message' => [
			'supportedlanguages-summary', // Contains links that are translated
		]
	],
	[
		'group' => 'ext-translate-core',
		'check' => 'plural',
		'message' => [
			'translate-checks-plural-dupe', // Message explaining the check.
		]
	],
	[
		'group' => 'ext-uploadwizard',
		'check' => 'variable',
		'message' => [
			'group-upwizcampeditors-member', // Optional GENDER parameter
		]
	],
	[
		'group' => 'ext-whitelistedit',
		'check' => 'variable',
		'message' => [
			'group-restricted-member', // Optional GENDER parameter
			'group-manager-member', // Optional GENDER parameter
		]
	],
	[
		'group' => 'ext-widgets',
		'check' => 'variable',
		'message' => [
			'group-widgeteditor-member', // Optional GENDER parameter
		]
	],
	[
		'group' => 'ext-wikieditor',
		'check' => 'links',
		'message' => [
			'wikieditor-toolbar-help-content-ilink-syntax', // Contains links that are translated
			'wikieditor-toolbar-help-content-file-syntax', // Contains links that are translated
		]
	],
	[
		'group' => 'ext-wikibase-repo',
		'check' => 'links',
		'message' => [
			'wikibase-newproperty-summary', // Contains links that are translated
			'wikibase-createitem-summary', // Contains links that are translated
		]
	],
	[
		'group' => 'ext-wikiforum',
		'check' => 'variable',
		'message' => [
			'group-forumadmin-member', // Optional GENDER parameter
		]
	],
	[
		'group' => 'ext-wikilog',
		'check' => 'variable',
		'message' => [
			'wikilog-comment-note-edited', // Optional parameter $3
			'wikilog-summary-categories', // Optional PLURAL parameter ($1)
			'wikilog-summary-footer', // Optional parameters $3, $4, $5, $6
			'wikilog-summary-footer-single', // Optional parameters $1, $2, $3, $4, $5, $6
		]
	],
	[
		'group' => 'ext-wikimediaincubator',
		'check' => 'links',
		'message' => [
			'wminc-code-macrolanguage', // Contains link that can be translated
		]
	],
	[
		'group' => 'ext-wikimediaincubator',
		'check' => 'variable',
		'message' => [
			'group-test-sysop-member', // Optional GENDER parameter
		]
	],
	[
		'group' => 'out-blockly-core',
		'check' => 'variable',
		'message' => [
			'PROCEDURES_CALLNORETURN_HELPURL', // Has URL encoded %2 in message
			'PROCEDURES_CALLRETURN_HELPURL', // %2
			'MATH_CONSTANT_HELPURL', // %8
		]
	],
	[
		'group' => 'out-fudforum',
		'check' => 'variable',
		'message' => [
			'page_timings', // Optional parameter for PLURAL
		]
	],
	[
		'group' => 'out-fudforum',
		'check' => 'parameters',
		'message' => [
			'page_timings', // Optional parameter for PLURAL
		]
	],
	[
		'group' => 'out-osm-site',
		'check' => 'parameters',
		'message' => [
			'notifier.signup_confirm_html.user_wiki_page', // Contains links that are translated
			'notifier.signup_confirm_plain.user_wiki_2', // Contains links that are translated
		]
	],
	[
		'group' => 'tsint-pb',
		'check' => 'links',
		'message' => [
			'pb-pb-footer', // Acceptable links
		]
	],
// translatewiki.net specific
	[
		'group' => 'page-Translating:Intro',
		'check' => 'links',
		'message' => [
			'Translating:Intro/intro', // Contains links that are translated
		]
	],
	[
		'group' => 'wikidata-lexeme-forms',
		'check' => 'variable',
		'message' => [
			'Wikidata-lexeme-forms-bulk-not-allowed',
		]
	],
];
