# XDebug

cd /tmp
wget https://github.com/xdebug/xdebug/archive/2.9.0.tar.gz

tar zxvf ./2.9.0.tar.gz
cd xdebug-2.9.0/

phpize

./configure --enable-xdebug
make
make install

# ------

rm -rf /tmp/xdebug-2.9.0/
rm -f /tmp/2.9.0.tar.gz
