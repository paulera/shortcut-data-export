#!/bin/bash

TESTFOLDER=$(readlink -m $(dirname $0))"/"
TMPFILE=$(mktemp /tmp/basic-info-iteration-176981.XXXXXX)
EXPECTEDFILE="detailed_history_iteration_149839.txt"

curl "http://localhost:8080/api.php?action=detailedhistory&iterationid=149839" -o $TMPFILE

echo "\n--------------------\n"

if [ ! -z "$(diff ${TESTFOLDER}${EXPECTEDFILE} ${TMPFILE})" ]
then
    echo "Failed! Output file (first) differs from Expected (second):\n\n"
    DIFFCMD="diff --color=always ${TMPFILE} ${EXPECTEDFILE}"
    echo ${DIFFCMD}
    echo "\n"
    ${DIFFCMD}
    exit 1
fi

echo "Passed!"
rm ${TMPFILE}
