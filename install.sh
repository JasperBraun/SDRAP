#!/bin/bash

set -o errexit

doc_line="  To display documentation, execute: $0 help"
php_username="apache"
php_read_permissions="500"
php_output_permissions="755"
php_execute_permissions="700"

function usage {
  printf "%s\n" "Usage: $0"
  printf "%s\n"
  printf "%s\n" "This command may need to be executed with superuser privileges"
  printf "%s\n" "For further information, see the README.md file"
  exit 1
}

# test input for errors
if [[ $1 = 'help' || $1 = '--help' || $1 = '-h' || $1 = 'usage' ]]; then
  usage
elif [[ $# -gt 0 ]]; then
  error="  ERROR: invalid arguments."
  message="  Too many arguments provided."
  printf "%s\n" "$error" "$message" "$doc_line"
  exit 22

# if no errors detected, make the necessary modifications
else
  mkdir annotations
  chown -R $php_username input
  chown -R $php_username annotations
  chown -R $php_username php
  chmod -R $php_execute_permissions php
  chmod -R $php_read_permissions input
  chmod -R $php_output_permissions annotations
  chmod -R $php_output_permissions tmp
fi
