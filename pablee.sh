#!/bin/bash

build() {
    docker build -t acudeen/pablee-api .
}

deploy() {
    docker run --restart always --name pablee-api --link pablee-api-db:pablee-api-db -d acudeen/pablee-api
}

undeploy() {
    docker stop pablee-api
    docker rm pablee-api
}


case "$1" in
    build)
        build
        ;;
    deploy)
        deploy $2
        ;;
    undeploy)
        undeploy
        ;;
    *)
        echo "pablee-api build|deploy|undeploy"
esac
