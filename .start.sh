#!/usr/bin/env bash

# Alias the main interface to have a pseudo-static IP for XDebug
if [[ "$OSTYPE" == "darwin"* ]]; then
    sudo ifconfig en0 alias 10.254.254.254 255.255.255.0
else
    # LINUX
    main_interface=`ip addr show | awk '/inet.*brd/{print $NF; exit}'`
    sudo ip addr add 10.254.254.254/24 brd + dev $main_interface label $main_interface:1
fi

if [[ ! -d "$PWD/vendor" ]]; then
    composer install
fi

#sudo mkdir -p $PWD/var/cache $PWD/var/log
#sudo chmod -R 0777 $PWD/var

#docker-compose build --pull
docker-compose up "$@"