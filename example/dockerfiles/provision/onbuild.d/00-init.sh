###
 # Common Functions
 ##
source "$DOCKER_CONF_PATH/bin/functions.sh"

# Create swoole.ini
copyFileTo "$DOCKER_CONF_PATH/etc/php/extends/xdebug.ini" "$PHP_INI_DIR/conf.d/zz-xdebug.ini"

/usr/local/bin/docker-clean
