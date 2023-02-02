#!/usr/bin/env bash
set -e

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

python3 ${DIR}/checkout.py

if [[ -z ${1} ]]; then
    builds=( "base" "php7" "php5" )
else
    builds=( "$@" )
fi

buildtypes=( "build" "run" )

for b in "${builds[@]}"; do
    for btype in "${buildtypes[@]}"; do
        if docker build -t witcher/${b}${btype} -f "${DIR}/${b}${btype}.Dockerfile" "${DIR}/../${b}"; then
            # tag for remote deploy
            docker tag witcher/${b}${btype} witcherfuzz/${b}${btype}
            printf "\033[32mSucessfully built ${b}${btype} \033[0m\n"
        else
            printf "\033[31mFailed to build ${b}${btype} \033[0m\n"
            exit 191
        fi
        if [[ "$b" == 'base' && "$btype" == 'build' ]]; then
            docker build -t witcher/build-widash-x86 -f "${DIR}/build-widash-x86.dockerfile" "${DIR}/../${b}"
        fi
    done

done


# older method
if [[ -z ${1} ]]; then
    builds=( "base" "python" "java" "nodejs" )
else
    builds=( "$@" )
fi

for b in "${builds[@]}"; do
    dirname=${b/tests\//""}
    docker_img_name="witcher/${b}"
    puppeteer_img_name="puppeteer1337/${b}"
    docker_img_name=${docker_img_name/tests\//""}
    docker_img_name=${docker_img_name/\/base/"/basebuildrun"}
    dockerfile_path="${DIR}/${b}.Dockerfile"
    if [[ ! -f ${dockerfile_path} ]]; then
        dockerfile_path=${DIR}/../${b}/Dockerfile
    fi
    if [[ $docker_img_name == "witcher" ]]; then
        puppeteer_img_name="puppeteer1337/witcher-base"
    fi
    context="${DIR}/../${b}"

    printf "\033[34mBuilding ::> ${docker_img_name} using Dockerfile ${dockerfile_path} and context ${context}\033[0m\n"
    echo 'docker build -t '${docker_img_name}' -f "'${dockerfile_path}'" "'${context}'"'

    if docker build -t ${docker_img_name} -f "${dockerfile_path}" "${context}"; then
        docker tag ${docker_img_name} ${puppeteer_img_name}
        printf "\033[32mSucessfully built ${puppeteer_img_name} using Dockerfile ${dockerfile_path} and context ${context}\033[0m\n"
    else
        printf "\033[31mFailed to build ${docker_img_name} using Dockerfile ${dockerfile_path} and context ${context}\033[0m\n"
        exit 191
    fi
done
