
sudo pear channel-discover pear.phing.info
sudo pear install phing/phing
sudo pear install Log

sudo svn checkout http://svn.propelorm.org/branches/1.6 /usr/local/propel

cd /usr/local/bin
sudo ln -s /usr/local/propel/generator/bin/propel-gen propel-gen
