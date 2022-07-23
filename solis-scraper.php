<?php
    // Configuration
    define('MQTT_BROKER',          '192.168.1.2' );
    define('MQTT_PORT',            1883 );
    define('MQTT_USER',            'username' );
    define('MQTT_PASSWD',          'password' );

    define('TOPIC_CURRENT_POWER',  'homeassistant/sensor/solis/current_power/state' );
    define('TOPIC_YIELD_TODAY',    'homeassistant/sensor/solis/solar_yield_today/state' );
    define('TOPIC_YIELD_TOTAL',    'homeassistant/sensor/solis/solar_yield_total/state' );

    define('SOLIS_ADDR',           '192.168.2.2' ); // assign a static IP in your router
    define('SOLIS_USER',           'username' );    // default: admin - please update!
    define('SOLIS_PASSWD',         'password' );    // default: admin - please update!

    // Load Bluerhino's phpMQTT class
    require './phpMQTT.php';

    // Fetch and read the solis-stick data
    $html = file_get_contents("http://" . SOLIS_ADDR . "/status.html", false, stream_context_create( [
            "http" => [ 
                "header" => "Authorization: Basic " . base64_encode(SOLIS_USER . ":" . SOLIS_PASSWD) 
            ],
        ])
    );

    preg_match_all('/var ([^ ]+) = "(.*)"/U', $html, $matches );

    foreach( $matches[1] AS $i => $key )
        $data[ $key ] = $matches[2][$i];

    if( empty($data) )
        $data = [ 'webdata_now_p' => 0 ];

    /*
        If the wifi-stick was up, array $data now contains:
    
        * webdata_now_p          * cover_mid
        * webdata_today_e        * cover_ver
        * webdata_total_e        * cover_wmode
        * webdata_alarm          * cover_ap_ssid
        * webdata_utime          * cover_ap_ip
                                 * cover_ap_mac
        * status_a               * cover_sta_ssid
        * status_b               * cover_sta_rssi
        * status_c               * cover_sta_ip
                                 * cover_sta_mac

        If the wifi-stick was NOT up, only webdata_now_p is available (being 0)
    */

    // Connect to the MQTT-broker and send the available data
    $mqtt = new Bluerhinos\phpMQTT( MQTT_BROKER, MQTT_PORT, uniqid() );

    if( $mqtt->connect(true, NULL, MQTT_USER, MQTT_PASSWD) )
    {
        if( empty($data['webdata_now_p']) )
            $data['webdata_now_p'] = 0;

        $mqtt->publish(TOPIC_CURRENT_POWER, $data['webdata_now_p'] );

        if( !empty($data['webdata_today_e']) )
            $mqtt->publish(TOPIC_YIELD_TODAY, $data['webdata_today_e'] );


        if( !empty($data['webdata_total_e']) )
            $mqtt->publish(TOPIC_YIELD_TOTAL, $data['webdata_total_e'] );

        $mqtt->close();
    }
