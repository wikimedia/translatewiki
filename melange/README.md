MLEB Release Process
--------------------

Steps
=====

1. Clone translatewiki repository:

    git clone ssh://USERNAME@gerrit.wikimedia.org:29418/translatewiki

where USERNAME is your Gerrit shell username.

2. Go to melange directory:

    cd translatewiki/melange

melange is set of PHP scripts that will ease release process for MLEB.
To see all options available run,

    php melange.php

3. If needed update documentation pages on mediawiki.org:

    https://www.mediawiki.org/wiki/Help:Extension:Translate/Configuration
    https://www.mediawiki.org/wiki/Help:Extension:Translate/Hooks

4. Config/Version changes:

a. Submit config.ini changes:

You need to review config.ini changes to Gerrit before going ahead.

Following lines need to update in most cases:

    [common]
    releasever=
    releasever-prev=

    [extensions]
    UniversalLanguageSelector=

Most important change is to determine which is last commit to include for ULS.
If we go for master,

    UniversalLanguageSelector=origin/master

else, it can be particular commit, for example,

    UniversalLanguageSelector=1b55342

and submit it for review.

b. Update Translate and ULS extension versions. See https://gerrit.wikimedia.org/r/#/c/88213/
for example and submit it for review.

5. Release steps:

a. Clone the extensions:

    php melange.php clone-extensions

This is required only the first time or if you deleted extensions folder.

b. Update the extensions:

    php melange.php update-extensions

c. Checkout release:

    php melange.php checkout-release

d. Prepare release notes:

    php melange.php prepare-notes

Now, edit the text files with the release notes generated in notes/ folder.

e. Create tag for extensions:

Make sure you've permission to create tag on all extensions repository.

    php melange.php create-tag

f. Push tags to repository:

    php melange.php push-tag

g. Create archive:

    php melange.php create-archive

This will create .bz2 tarball and .sha256sum files in releases/ folder.

f. Prepare announcement to sent to mailing list:

    php melange.php prepare-announcement

6. Upload release to translatewiki.net

scp tarball and sha256sums files to your home directory on translatewiki.net
and with sudo permission copy it to /www/translatewiki.net/docroot/mleb folder.

7. Update relevant pages:

[1] MLEB Mediawiki page: https://www.mediawiki.org/wiki/MediaWiki_Language_Extension_Bundle
[2] Latest MLEB release: https://www.mediawiki.org/wiki/MediaWiki_Language_Extension_Bundle/latest

8. Blog post/Twitter/Facebook etc.
