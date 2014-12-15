#!/bin/bash

echo "Enter environment:"
read ENV;

chmod 0777 -R app/cache/
php app/console bazinga:js-translation:dump

chmod 0777 -R app/cache/
php app/console assetic:dump -e ${ENV}

chmod 0777 -R app/cache/
php app/console assets:install --symlink

rm -rf app/cache/${ENV}
php app/console cache:clear -e ${ENV}
chmod 0777 -R app/cache/

chmod 0777 -R web/uploads/

service apache2 restart