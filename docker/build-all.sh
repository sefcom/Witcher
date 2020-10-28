#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

if [[ -z ${1} ]]; then
    builds=( "base" "php7" "php5" "php5/tests/wackopicko" "python" "ruby" "java" )
else
    builds=( "$@" )
fi

for b in "${builds[@]}"; do
    dirname=${b/tests\//""}
    docker_img_name="witcher/${b}"
    docker_img_name=${docker_img_name/tests\//""}
    docker_img_name=${docker_img_name/\/base/""}
    dockerfile_path="${DIR}/${b}.Dockerfile"
    if [[ ! -f ${dockerfile_path} ]]; then
        dockerfile_path=${DIR}/../${b}/Dockerfile
    fi
    context="${DIR}/../${b}"
    printf "\033[34mBuilding ::> ${docker_img_name} using Dockerfile ${dockerfile_path} and context ${context}\033[0m\n"
    if docker build -t ${docker_img_name} -f "${dockerfile_path}" "${context}"; then
        printf "\033[32mSucessfully built ${docker_img_name} using Dockerfile ${dockerfile_path} and context ${context}\033[0m\n"
    else
        printf "\033[31mFailed to build ${docker_img_name} using Dockerfile ${dockerfile_path} and context ${context}\033[0m\n"
        exit 191
    fi
done
