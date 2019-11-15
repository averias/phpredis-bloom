#!/bin/sh

blu=$'\e[1;34m'
end=$'\e[0m'

printf "\n${blu}*** Running test against Redis 5 + RedisBloom module docker service ***${end}\n\n"
./vendor/bin/phpunit --configuration ./docker-tests/phpunit_docker.xml
