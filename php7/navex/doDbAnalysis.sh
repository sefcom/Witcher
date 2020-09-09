#!/usr/bin/env bash


if [[ ! -f $1 ]]; then
    echo "ERROR the first argument must be a fully qualified path to the sql dump file"
    exit 33
fi
sqlfile=$(basename $1)
sqlpath=$(dirname $1)
if [[ ! -d $sqlpath ]]; then
    echo "ERROR the first argument must be a fully qualified path "
    exit 44
fi

schemapath=${sqlpath}/schema
mkdir -p ${schemapath}

# Remove everything from the sqldump except the create tables
egrep $'CREATE TABLE|^  `[a-zA-Z0-9_]*`|^  [A-Z ]+ .*[\),]$|\) ENGINE=' ${sqlpath}/${sqlfile} |sed -r 's/^  ([A-Z ]* )(`[a-zA-Z0-9_]*`.*`)\([0-9]*\)(.*)/\1\2\3/g' > ${schemapath}/${sqlfile}

cd /navex || exit 55

sudo chown wc:wc /navex

echo "${schemapath}" | java -classpath '/navex/joern/projects/extensions/joern-php/build/libs/*:/navex/joern/projects/extensions/joern-php/lib/*' dbAnalysis/DBAnalysis
exit 33

if echo "${schemapath}" | java -classpath '/navex/joern/projects/extensions/joern-php/build/libs/*:/navex/joern/projects/extensions/joern-php/lib/*' dbAnalysis/DBAnalysis; then

    printf "\033[31mSUCCESS created a CSV with %s lines\033[0m\n" "$(wc -l schema.csv)"
else
    printf "\033[31m;Creation Failed\033[0m\n"
fi




