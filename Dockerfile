#
# MAINTAINER        Allen Luo <movoin@gmail.com>
# DOCKER-VERSION    18.09.0
#

FROM movoin/devops-swoole:4

COPY dockerfiles/etc/   /etc/
COPY dockerfiles/       $DOCKER_CONF_PATH

WORKDIR /app/

RUN set -x \
    # Bootstrap
    && $DOCKER_CONF_PATH/bin/bootstrap.sh
