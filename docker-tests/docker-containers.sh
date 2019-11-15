#!/bin/sh

yel=$'\e[1;33m'
end=$'\e[0m'

docker-compose up --build -d
docker exec -i phpredis-bloom bash < ./docker-tests/docker-tests.sh
printf "\n${yel}*** Stopping containers... ***${end}\n\n"
docker stop redislab-rebloom
docker stop phpredis-bloom