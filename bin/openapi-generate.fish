#!/usr/bin/env fish

set root (realpath (dirname (status filename))/..)
set dist $root/openapi/generated

set types \
  # client
  php \
  # server-side
  php-laravel \
  php-lumen \
  php-silex \
  php-slim4 \
  php-symfony \
  php-ze-ph \

for type in $types
  openapi-generator generate \
    -i ./openapi/index.yml \
    -o $dist/$type \
    -g $type
end

openapi-generator generate \
  -i ./openapi/index.yml \
  -o $dist/javascript \
  -g javascript

openapi-generator generate \
  -i ./openapi/index.yml \
  -o $dist/typescript-node \
  -g typescript-node

openapi-generator generate \
  -i ./openapi/index.yml \
  -o $dist/typescript-axios \
  -g typescript-axios

openapi-generator generate \
  -i ./openapi/index.yml \
  -o $dist/typescript-fetch \
  -g typescript-fetch \
  --additional-properties=typescriptThreePlus=true \
  --auth hoge

# vi: ft=fish
