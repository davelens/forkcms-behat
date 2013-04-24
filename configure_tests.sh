#!/bin/bash

if [ -f 'index.php' ] && [ -f 'composer.json' ] && grep -Rq "Fork CMS" index.php; then
	echo 'Configuring Behat+Mink for Fork CMS...'
else
	echo 'ERROR: You should call this script from a Fork project root.'
	exit
fi

# Composer; global install if necessary
if ! which composer >/dev/null; then
	echo 'Installing composer...'
	cd /usr/local/bin
	curl -sS https://getcomposer.org/installer | php
	mv composer.phar composer && chmod a+x composer
	cd - >/dev/null
fi

# Install composer if necessary
if [ ! -f /usr/local/bin/selenium-server-standalone-2.31.0.jar ]; then
	echo 'Installing Selenium'
	cd /usr/local/bin
	curl -O http://selenium.googlecode.com/files/selenium-server-standalone-2.31.0.jar
	cd - >/dev/null

	echo 'It would be wise to create an alias in your profile config of choice.'
	echo "	alias selenium='java -jar /usr/local/bin/selenium-server-standalone-2.31.0.jar'"
fi

# If anyone knows a less messy routine without using json libs; let me know!
# Append the behat packages to the require section of composer.json
if ! grep -Rq '"behat/behat"' composer.json; then
	awk '{
		if ( $0 ~ /"php"/ ) {
			printf( "%s\n\t\t%s\n\t\t", $0, "\"behat/behat\": \"2.4.*\"," );
			printf( "%s\n\t\t", "\"behat/mink\": \"1.4.*\"," );
			printf( "%s\n\t\t", "\"behat/mink-extension\": \"*\"," );
			printf( "%s\n\t\t", "\"behat/mink-goutte-driver\": \"*\"," );
			printf( "%s\n\t\t", "\"behat/mink-selenium2-driver\": \"*\"," );
			printf( "%s\n\t\t", "\"behat/mink-zombie-driver\": \"*\"," );
			printf( "%s\n\t\t", "\"behat/symfony2-extension\": \"*\"," );
			printf( "%s\n", "\"fzaninotto/faker\": \"1.1.*\"," );
		} else {
			print $0;
		}
	}' composer.json > composer.json.new && mv composer.json.new composer.json
fi


# Append the bin-dir to the config section of composer.json
if ! grep -Rq '"bin-dir"' composer.json; then
	if ! grep -Rq '"config"' composer.json; then
		awk '{
			if ( $0 ~ /"require"/ ) {
				printf( "\t%s\n%s\n", "\"config\": {\n\t\t\"bin-dir\": \"bin/\"\n\t},", $0);
			} else {
				print $0;
			}
		}' composer.json > composer.json.new && mv composer.json.new composer.json
	else
		awk '{
			if ( $0 ~ /"config"/ ) {
				printf( "%s\n\t\t%s\n", $0, "\"bin-dir\": \"bin/\"," );
			} else {
				print $0;
			}
		}' composer.json > composer.json.new && mv composer.json.new composer.json
	fi
fi

composer update >/dev/null
echo 'done!'
