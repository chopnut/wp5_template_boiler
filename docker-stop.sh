#!/usr/bin/env sh
docker stop $(docker ps -a -q)
docker update --restart=no $(docker ps -a -q)
docker rm -f $(docker ps -aq)
#docker rmi $(docker images -q)
#winpty docker exec -it eb2 bash