<?php

//  Created by Jimmy on 12/02/12.
//  Copyright (c) 2012 VarshylMobile. All rights reserved.


// Put device token here (without spaces)
$deviceToken = '';


// Put your private key's passphrase here: if 'apns-dev.pem' is encrypted with pass
$passphrase = '12345';

// Put your alert message here:This message appears as the notification message
$message = "Boooga Booga Hooga Chaa Chaa";
$badgeCount = 1;

////////////////////////////////////////////////////////////////////////////////

$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', 'apns-dev.pem');
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

// Open a connection to the APNS server
// During development use ssl://gateway.sandbox.push.apple.com:2195
// During production use ssl://gateway.push.apple.com:2195
$fp = stream_socket_client(
	'ssl://gateway.sandbox.push.apple.com:2195', $err,
	$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

if (!$fp)
	exit("Failed to connect: $err $errstr" . PHP_EOL);

echo 'Connected to APNS' . PHP_EOL;
   
// Create the payload body

$body['aps'] = array(
	'alert' => $message,
	'badge' => $badgeCount,
	'sound' => 'default',
	);	
	

// Encode the payload as JSON
$payload = json_encode($body);
echo($payload);
// Build the binary notification
$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

// Send it to the server
$result = fwrite($fp, $msg, strlen($msg));

if (!$result)
	echo 'Message not delivered' . PHP_EOL;
else
	echo 'Message successfully delivered' . PHP_EOL;

// Close the connection to the server
fclose($fp);
