#!/bin/bash

set -o errexit

doc_line="  To display documentation, execute: $0 help"
nucleus=$1
file=$2
target_file="input/${nucleus}/${file}"
php_username="apache"
php_read_permissions="500"

function usage {
  printf "%s\n" "Usage: $0 {precursor|product} FILE"
  printf "%s\n"
  printf "%s\n" "  {precursor|product}    specifies the nucleus"
  printf "%s\n" "  FILE                   the name of a file on the list of"
  printf "%s\n" "                           input files of SDRAP for the"
  printf "%s\n" "                           specified nucleus"
  printf "%s\n"
  printf "%s\n" "Shell may ask whether or not to remove the specified file from"
  printf "%s\n" "'input/precursor' or 'input/product' directory; if this file"
  printf "%s\n" "was added to the list of input files of SDRAP according to"
  printf "%s\n" "documentation, it is only a copy of the original file."
  printf "%s\n" "This command may need to be executed with superuser privileges"
  printf "%s\n" "For further information, see the README.md file"
  exit 1
}

# test input for errors
if [[ $1 = 'help' || $1 = '--help' || $1 = '-h' || $1 = 'usage' ]]; then
  usage
elif [[ -z $nucleus ]]; then
  error="  ERROR: invalid arguments."
  message="  Please specify the nucleus and a filename."
  printf "%s\n" "$error" "$message" "$doc_line"
  exit 22
elif [[ $nucleus != 'precursor' && $nucleus != 'product' ]]; then
  error="  ERROR: invalid arguments."
  message="  Nucleus must be specified as 'precursor' or 'product'."
  printf "%s\n" "$error" "$message" "$doc_line"
  exit 22
elif [[ -z $file ]]; then
  error="  ERROR: invalid arguments."
  message="  Please specify name of the file that needs to be removed."
  printf "%s\n" "$error" "$message" "$doc_line"
  exit 22
elif [[ ! -f $target_file ]]; then
  error="  ERROR: invalid arguments."
  message=" Invalid filename."
  printf "%s\n" "$error" "$message" "$doc_line"
  exit 22
elif [[ "${file##*.}" != "fna" ]]; then
  error="  ERROR: invalid arguments."
  message="  Invalid file extension; must be '.fna'."
  printf "%s\n" "$error" "$message" "$doc_line"
  exit 22
elif [[ $# -gt 2 ]]; then
  error="  ERROR: invalid arguments."
  message="  Too many arguments provided."
  printf "%s\n" "$error" "$message" "$doc_line"
  exit 22

# if no errors detected, remove target file from input files
# and remove from list of options in menu
else
  rm $target_file

  sed -i "s%<option>${file}</option>%%g" index.html
fi
