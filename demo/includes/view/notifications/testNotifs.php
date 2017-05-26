<?php
    	require_once('sendNotifications.php');
	// Message payload
	$msg_payload = array (
		'mtitle' => 'Test push notification title',
		'mdesc' => 'Test push notification body',
	);
	
	// For Android
	$regId = 'APA91bHdOmMHiRo5jJRM1jvxmGqhComcpVFDqBcPfLVvaieHeFI9WVrwoDeVVD1nPZ82rV2DxcyVv-oMMl5CJPhVXnLrzKiacR99eQ_irrYogy7typHQDb5sg4NB8zn6rFpiBuikNuwDQzr-2abV6Gl_VWDZlJOf4w';
	// For iOS
	$deviceToken = '305393e0a0c760821dd03f0d660358fcb88a70960fa4ce714d3de75225694d2c';
	// For WP8
	$uri = 'http://s.notify.live.net/u/1/sin/HmQAAAD1XJMXfQ8SR0b580NcxIoD6G7hIYP9oHvjjpMC2etA7U_xy_xtSAh8tWx7Dul2AZlHqoYzsSQ8jQRQ-pQLAtKW/d2luZG93c3Bob25lZGVmYXVsdA/EKTs2gmt5BG_GB8lKdN_Rg/WuhpYBv02fAmB7tjUfF7DG9aUL4';
	
	// Replace the above variable values
	
	
    	//PushNotifications::android($msg_payload, $regId);
    	
    	//PushNotifications::WP8($msg_payload, $uri);
    	
    	PushNotifications::iOS($msg_payload, $deviceToken);
?>