#
# MAINTAINER        Allen Luo <movoin@gmail.com>
# DOCKER-VERSION    18.09.0
#

FROM movoin/one-project:swoole_only

ENV ONE_PROJECT one.project

# 运行模式：local, test, devel, deploy
ENV ONE_MODE local

WORKDIR /app/

RUN set -x \
    # Bootstrap
    && $DOCKER_CONF_PATH/bin/bootstrap.sh
