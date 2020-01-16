#!/bin/bash
files="$1"
if [[ "$files" = "" ]]; then
  files="./tests"
fi
php -d zend.assertions=1 ./vendor/peridot-php/peridot/bin/peridot "$files"
