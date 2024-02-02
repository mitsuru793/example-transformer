#!/bin/bash

function main() {
  git stash
  require_not_dev_packages
  require_dev_packages
  git stash pop
}

function require_not_dev_packages() {
  packages=$(jq -r '.require | keys | .[]' composer.json)
  for p in $packages; do
    composer require --update-with-all-dependencies "$p"
    if [ -z "$(git status --porcelain)" ]; then
      echo "not update $p"
      continue
    fi

    git add .
    git commit -m "composer require --update-with-all-dependencies $p"
  done
}

function require_dev_packages() {
  packages=$(jq -r '."require-dev" | keys | .[]' composer.json)
  for p in $packages; do
    composer require --dev --update-with-all-dependencies "$p"
    if [ -z "$(git status --porcelain)" ]; then
      echo "not update $p"
      continue
    fi

    git add .
    git commit -m "composer require --dev --update-with-all-dependencies $p"
  done
}

main
