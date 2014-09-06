#!/bin/bash

echo "Enter environment:"
read ENV;

sudo chmod 0777 -R app/cache/
php app/console bazinga:js-translation:dump

sudo chmod 0777 -R app/cache/
php app/console assetic:dump -e ${ENV}

sudo chmod 0777 -R app/cache/
php app/console assets:install --symlink

sudo rm -rf app/cache/${ENV}
sudo php app/console cache:clear -e ${ENV}
sudo chmod 0777 -R app/cache/

sudo service apache2 restart