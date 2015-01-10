#!/bin/bash

echo "Enter environment:"
read ENV;

chmod 0777 -R app/logs/

chmod 0777 -R app/cache/
php app/console bazinga:js-translation:dump

chmod 0777 -R app/cache/
php app/console assetic:dump -e ${ENV}

chmod 0777 -R app/cache/
php app/console assets:install --symlink

rm -rf app/cache/${ENV}
php app/console cache:clear -e ${ENV}
chmod 0777 -R app/cache/

uploadsDirectory="web/uploads/"
if [ ! -d "${uploadsDirectory}" ]; then
  mkdir ${uploadsDirectory}
fi
chmod 0777 -R ${uploadsDirectory}

service apache2 restart