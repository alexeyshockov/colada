#!/bin/bash

echo "// Call use_colada() function to benefit from all shortcuts ;)"

env php -d "auto_prepend_file=./shell.php" -d "display_errors=1" -a
