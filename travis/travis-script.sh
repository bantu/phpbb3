#!/bin/sh
#
# This file is part of the phpBB Forum Software package.
#
# @copyright (c) phpBB Limited <https://www.phpbb.com>
# @license GNU General Public License, version 2 (GPL-2.0)
#
# For full copyright and license information, please see
# the docs/CREDITS.txt file.
#

if [ "$TRAVIS_PHP_VERSION" == "5.5" -a "$DB" == "mysqli" ]
then
	travis/phing-sniff.sh
fi

if [ "$TRAVIS_PHP_VERSION" == "5.3.3" -a "$DB" == "mysqli" ]
then
	travis/check-sami-parse-errors.sh
	travis/check-image-icc-profiles.sh
	travis/check-executable-files.sh ./
fi

PHPUNIT_ARGS="--configuration travis/phpunit-$DB-travis.xml"
if [ "$SLOWTESTS" = '1' ]
then
	PHPUNIT_ARGS="$PHPUNIT_ARGS --group slow"
fi
phpBB/vendor/bin/phpunit $PHPUNIT_ARGS

if [ '$TRAVIS_PHP_VERSION' = '5.3.3' -a '$DB' = 'mysqli' -a '$TRAVIS_PULL_REQUEST' != 'false' ]
then
	git-tools/commit-msg-hook-range.sh origin/$TRAVIS_BRANCH..FETCH_HEAD
fi
