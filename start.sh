#ª/bin/bash

echo "Package name (with dash): "
read -r packageName;

echo "PSR-4 name (UpperCase): "
read -r psr4Name;

find . -type f -print0 | xargs -0 sed -i "s/php-library-template/$packageName/g"
find . -type f -print0 | xargs -0 sed -i "s/PhpLibraryTemplate/$psr4Name/g"