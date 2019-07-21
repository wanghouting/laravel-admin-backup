#!/bin/sh

if [[ $# != 1 ]];then
    echo "need 1 param for backup path";
    exit
fi

BACKUP_DIR=$1

echo 'ok'