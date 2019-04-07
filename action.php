<?php
require_once 'emailer.php';

if (isset($_REQUEST['cmd'])) {
	$cmd = $_REQUEST['cmd'];

	$em = rEmailer::load();

	switch($cmd) {
		case 'set': {
			
			$em->set();
			cachedEcho($em->get(), 'application/javascript');

			break;
		}
		case 'get': {
			
			//cachedEcho(safe_json_encode($em->getInfo()),"application/json");
			cachedEcho(safe_json_encode($em),"application/json");

			break;
		}
		case 'sendtest': {
			// send a test email using the settings provided, without saving the values
			$result = $em->sendTestEmail();

			// respond with a success or failure message
			cachedEcho(safe_json_encode($result), 'application/json');

			break;
		}
	}
}