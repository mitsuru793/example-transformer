#!/usr/bin/env fish

set root (realpath (dirname (status filename))/..)
set dist $root/openapi/generated

openapi-generator generate \
  -i ./openapi/index.yml \
  -o $dist/typescript-axios \
  -g typescript-axios \
  --type-mappings Date=string

openapi-generator generate \
  -i ./openapi/index.yml \
  -o $dist/typescript-fetch \
  -g typescript-fetch \
  --additional-properties=typescriptThreePlus=true

# vi: ft=fish
