#!/bin/sh

set -eu

php artisan orders:release-expired-reservations --no-interaction
