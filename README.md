# solis2mqtt
**Scrapes the Ginlong Solis wifi-stick and publishes the data to MQTT**

With this script, you can fetch the data from your PV inverter (given that a Ginlong Solis wifi stick is installed) **WITHOUT USING THE CLOUD**. Simply put your wifi stick in a wifi-network without internet access. As long as you can reach the web interface of  the stick, this script should work. No one in China will be able to read your data.

The web interface of the Ginlong Solis wifi stick uses javascript variables to display the metrics. This script simply fetches the status page and scrapes all javascript variables to an array. Of the available data, (1) the current power, (2) today's yield and (3) total yield will be published to the MQTT-broker of your choice.

This script relies on  the Blue Rhinos MQTT-class to be present in the same folder, in a file called 'phpMQTT.php'. You can download it here (shout out to Blue Rhinos!): 

-> https://github.com/bluerhinos/phpMQTT

I've been using this script with Home Assistant for 9 months without problems. This script does not provide automatic discovery, so you have to setup the MQTT-sensors by hand (pull request anyone?). Once done, it works great with HA Energy Management.

Use a crontab to automaticly run this script ("php -q solis-scraper.php").
