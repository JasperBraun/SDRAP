#!/bin/bash

set -o errexit

doc_line="  To display documentation, execute: $0 help"
nucleus=$1
file=$2
destination_file="input/${nucleus}/${file}"
php_username="apache"
php_read_permissions="500"
new_list_item="<!--new-input-${nucleus}-sequence-file-->"

function usage {
  printf "%s\n" "Usage: $0 {precursor|product} FILE"
  printf "%s\n"
  printf "%s\n" "  {precursor|product}    specifies the nucleus"
  printf "%s\n" "  FILE                   must be a valid .fna, or .fasta"
  printf "%s\n" "						                file containing nucleotide"
  printf "%s\n" "                           sequences from the specified"
  printf "%s\n" "                           nucleus"
  printf "%s\n"
  printf "%s\n" "This command may need to be executed with superuser privileges"
  printf "%s\n" "For further information, see the README.md file"
  exit 1
}

# test input for errors
if [[ $1 = 'help' || $1 = '--help' || $1 = '-h' || $1 = 'usage' ]]; then
  usage
elif [[ -z $nucleus ]]; then
  error="  ERROR: invalid arguments."
  message="  Please specify the nucleus and a file containing its nucleotide sequences."
  printf "%s\n" "$error" "$message" "$doc_line"
  exit 22
elif [[ $nucleus != 'precursor' && $nucleus != 'product' ]]; then
  error="  ERROR: invalid arguments."
  message="  Nucleus must be specified as 'precursor' or 'product'."
  printf "%s\n" "$error" "$message" "$doc_line"
  exit 22
elif [[ -z $file ]]; then
  error="  ERROR: invalid arguments."
  message="  Please specify file containing nucleotide sequences for indicated nucleus."
  printf "%s\n" "$error" "$message" "$doc_line"
  exit 22
elif [[ ! -r $file ]]; then
  error="  ERROR: invalid arguments."
  message="  File not found, or invalid file type."
  printf "%s\n" "$error" "$message" "$doc_line"
  exit 22
elif [[ "${file##*.}" != "fna" && "${file##*.}" != "fasta" ]]; then
  error="  ERROR: invalid arguments."
  message="  Invalid file extension; must be '.fna'."
  printf "%s\n" "$error" "$message" "$doc_line"
  exit 22
elif [[ $# -gt 2 ]]; then
  error="  ERROR: invalid arguments."
  message="  Too many arguments provided."
  printf "%s\n" "$error" "$message" "$doc_line"
  exit 22
elif [[ -f $destination_file ]]; then
  error="  ERROR: invalid arguments."
  message="  File already exists."
  printf "%s\n" "$error" "$message" "$doc_line"
  exit 22

# if no errors detected, copy file to destination
# and add to list of options in menu
else
  cp $file $destination_file
  chown $php_username $destination_file
  chmod $php_read_permissions $destination_file

  sed -i "s%${new_list_item}%<option>${file}</option>${new_list_item}%" index.html
fi
