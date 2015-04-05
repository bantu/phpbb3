#!/bin/bash
#
# This file is part of the phpBB Forum Software package.
#
# @copyright (c) phpBB Limited <https://www.phpbb.com>
# @license GNU General Public License, version 2 (GPL-2.0)
#
# For full copyright and license information, please see
# the docs/CREDITS.txt file.
#
set -e

# Workarounds for
# https://github.com/fabpot/Sami/issues/116
# and
# https://github.com/fabpot/Sami/issues/117
errors=$(
	unbuffer phpBB/vendor/bin/sami.php parse build/sami-checkout.conf.php -v | \
	sed "s,\x1B\[[0-9;]*[a-zA-Z],,g" | \
	grep "ERROR: " | \
	tee /dev/tty | \
	wc -l
)
if [ "$errors" != "0" ]
then
	exit 1
fi
