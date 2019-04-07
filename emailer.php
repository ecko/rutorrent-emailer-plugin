<?php

require_once( dirname(__FILE__)."/../../php/settings.php");
require_once( dirname(__FILE__)."/../../php/cache.php");
eval(getPluginConf('emailer'));

class rEmailer
{
	public $hash = "emailer.dat";

	public $emailerOptionEnabled = true;

	public $emailerOptionSmtpServer = "";
	public $emailerOptionSmtpPort = 0;
	public $emailerOptionSmtpTls = 0;
	public $emailerOptionUsername = "";
	public $emailerOptionPassword = "";
	public $emailerOptionRecipientEmail = "";


	static public function load()
	{
		$cache = new rCache();

		$em = new rEmailer();
		$cache->get($em);

		return $em;
	}


	public function store()
	{
		$cache = new rCache();
		return($cache->set($this));
	}

	public function get()
	{
		return("theWebUI.emailer = ".safe_json_encode($this).";");
	}

	public function set()
	{
		if (isset($_REQUEST['emailerOptionSmtpServer'])) {
			$this->emailerOptionSmtpServer = $_REQUEST['emailerOptionSmtpServer'];
		}
		if (isset($_REQUEST['emailerOptionSmtpPort'])) {
			$this->emailerOptionSmtpPort = $_REQUEST['emailerOptionSmtpPort'];
		}
		if (isset($_REQUEST['emailerOptionSmtpTls'])) {
			$this->emailerOptionSmtpTls = $_REQUEST['emailerOptionSmtpTls'];
		}
		if (isset($_REQUEST['emailerOptionUsername'])) {
			$this->emailerOptionUsername = $_REQUEST['emailerOptionUsername'];
		}
		if (isset($_REQUEST['emailerOptionPassword'])) {
			$this->emailerOptionPassword = $_REQUEST['emailerOptionPassword'];
		}
		if (isset($_REQUEST['emailerOptionRecipientEmail'])) {
			$this->emailerOptionRecipientEmail = $_REQUEST['emailerOptionRecipientEmail'];
		}

		$this->store();

		// register the action with rtorrent?
		$this->setHandlers();
	}

	public function setHandlers()
	{
		$pathToEmailer = dirname(__FILE__);
		$theSettings = rTorrentSettings::get();
		$req = new rXMLRPCRequest();


		// https://github.com/rakshasa/rtorrent/wiki/Common-Tasks-in-rTorrent#send-email-for-completed-downloads
		// method.set_key = event.download.finished,notify_me,"execute=~/rtorrent_mail.sh,$d.name="

		$cmd = getCmd('cat=');
		// need to check if the emailer has been enabled
		if ($this->emailerOptionEnabled) {
			/*
			$cmd = getCmd('execute.capture')
				.'={'.escapeshellarg(getPHP())
			//	.',2,$'
			//	.getCmd('d.get_name').'=,$'
			//	.getCmd('d.get_size_bytes').'=,$'
			//	.getCmd('d.get_bytes_done').'=,$'
				//.getCmd('d.get_up_total').'=,$'
				//.getCmd('d.get_ratio').'=,$'
			//	.getCmd('d.get_creation_date').'=,$'
				//.getCmd('d.get_custom').'=addtime,$'
				//.getCmd('d.get_custom').'=seedingtime'
			*/
			
			$cmd = getCmd('d.custom1.set').'=x-emailer,"$'.getCmd('execute_capture')
				.'={'.getPHP().','.$pathToEmailer.'/update.php,$'
				.getCmd('d.get_name').'=,$'
				.getCmd('d.get_size_bytes').'=,$'
				.getCmd('d.get_bytes_done').'=,$'
				.getCmd('d.get_creation_date').'=,$'
				.getCmd('d.get_custom').'=addtime,'
				.getUser().'}"';

		}

		$finishedCmd = $theSettings->getOnFinishedCommand(array('emailer'.getUser(), $cmd));
		$req->addCommand($finishedCmd);

		return($req->success());
	}

	public function sendTestEmail()
	{
		$result = array(
		//	'success' => false,
		//	'message' => array('Could not send email')
			'message' => array()
		);


		$emailerOptionSmtpServer = "";
		$emailerOptionSmtpPort = "";
		$emailerOptionSmtpTls = "";
		$emailerOptionUsername = "";
		$emailerOptionPassword = "";
		$emailerOptionRecipientEmail = "";

		if (isset($_REQUEST['emailerOptionSmtpServer'])) {
			$emailerOptionSmtpServer = trim($_REQUEST['emailerOptionSmtpServer']);
			
			if (empty($emailerOptionSmtpServer)) {
				$result['success'] = false;
				$result['message'][] = 'Missing SMTP server.';
			}
		}
		if (isset($_REQUEST['emailerOptionSmtpPort'])) {
			$emailerOptionSmtpPort = trim($_REQUEST['emailerOptionSmtpPort']);
		}
		if (isset($_REQUEST['emailerOptionSmtpTls'])) {
			$emailerOptionSmtpTls = $_REQUEST['emailerOptionSmtpTls'];
		}
		if (isset($_REQUEST['emailerOptionUsername'])) {
			$emailerOptionUsername = trim($_REQUEST['emailerOptionUsername']);
			
			if (empty($emailerOptionUsername)) {
				$result['success'] = false;
				$result['message'][] = 'Missing SMTP username.';
			}
		}
		if (isset($_REQUEST['emailerOptionPassword'])) {
			$emailerOptionPassword = $_REQUEST['emailerOptionPassword'];
			
			if (empty($emailerOptionPassword)) {
				$result['success'] = false;
				$result['message'][] = 'Missing SMTP password.';
			}
		}
		if (isset($_REQUEST['emailerOptionRecipientEmail'])) {
			$emailerOptionRecipientEmail = trim($_REQUEST['emailerOptionRecipientEmail']);
		}

		// send the test email
		//if ($result['success'] === true) {
		if (empty($result['success'])) {
			require_once __DIR__ . '/swiftmailer-5.4.12/lib/swift_required.php';

			// Create the Transport
			$transport = (new Swift_SmtpTransport($emailerOptionSmtpServer, $emailerOptionSmtpPort, 'ssl'))
			->setUsername($emailerOptionUsername)
			->setPassword($emailerOptionPassword)
			;

			// Create the Mailer using your created Transport
			$mailer = new Swift_Mailer($transport);

			// Create a message
			$message = (new Swift_Message('Emailer Plugin Test Email'))
			->setFrom([$emailerOptionUsername => 'ruTorrent Emailer'])
			->setTo([$emailerOptionRecipientEmail])
			->setBody("This is a test email sent by the ruTorrent Emailer Plugin. It looks like everything is working.\r\n\r\nEnjoy!")
			;

			
			// Send the message
			//$send = $mailer->send($message);
			//$result['message'] = $send;

			if ($mailer->send($message)) {
				$result['sucess'] = true;
				$result['message'][] = 'Email sent!';
			}
		}

		return $result;
	}

	public function sendEmailOnCompletion($details, $debugMsg = "")
	{
		require_once __DIR__ . '/swiftmailer-5.4.12/lib/swift_required.php';

		$message = '<html><head><style>body{font-family:monospace;font-size:16px;}</style></head><body>';


		/*
		$message = $message 
			. "<em>A download has finished.</em>\r\n"
			//."----------------------------------------\r\n"
			. '<hr>'
			."Name:      <strong>". $details['name']."</strong>\r\n"
			."Size:      ". self::bytes($details['size'])."\r\n"
			."Created:   ". strftime('%c',$details['created'])."\r\n"
			."Added:     ". strftime('%c',$details['added'])."\r\n"
			."Done:      ". strftime('%c',$details['finished'])."\r\n"
			//."----------------------------------------\r\n"
			. '<hr>'
			;
		*/
		$message = $message 
			. "<em>A download has finished.</em><br>"
			//."----------------------------------------\r\n"
			. '<hr>'
			."Name:      <strong>". $details['name']."</strong><br>"
			."Size:      ". self::bytes($details['size'])."<br>"
			."Created:   ". strftime('%A, %B %d, %Y %I:%M:%S %p',$details['created'])."<br>"
			."Added:     ". strftime('%A, %B %d, %Y %I:%M:%S %p',$details['added'])."<br>"
			."Done:      ". strftime('%A, %B %d, %Y %I:%M:%S %p',$details['finished'])."<br>"
			//."----------------------------------------\r\n"
			. '<hr>'
			;

		if (!empty($debugMsg)) {
			$message = $message . "<pre>\r\n\r\n" . $debugMsg . '</pre>';
		}

		$message = $message . '</body></html>';
		
		// send the email
		$result = $this->sendEmail(
			$this->emailerOptionSmtpServer, 
			$this->emailerOptionSmtpPort,
			$this->emailerOptionUsername,
			$this->emailerOptionPassword,
			'ruTorrent - Download Completed',
			$this->emailerOptionUsername,
			'ruTorrent Emailer',
			$this->emailerOptionRecipientEmail,
			$message
		);

		return $result;
	}

	private function sendEmail($server, $port, $username, $password, $subject, $fromUsername, $fromName = 'ruTorrent Emailer', $toEmail, $emailBody)
	{
		// Create the Transport
		$transport = (new Swift_SmtpTransport($server, $port, 'ssl'))
			->setUsername($username)
			->setPassword($password)
			;

		// Create the Mailer using your created Transport
		$mailer = new Swift_Mailer($transport);

		// Create a message
		$message = (new Swift_Message($subject))
			->setFrom([$fromUsername => $fromName])
			->setTo([$toEmail])
			//->setBody($emailBody, 'text/plain')
			->setBody($emailBody, 'text/html')
			;
		
		// Send the message
		$sentCount = $mailer->send($message);

		return $sentCount;		
	}

	static protected function bytes( $bt )
	{
		$a = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB');
		$ndx = 0;
		if($bt == 0)
			$ndx = 1;
		else
		{
			if($bt < 1024)
			{
				$bt = $bt / 1024;
				$ndx = 1;
			}
			else
			{
				while($bt >= 1024)
				{
       		    			$bt = $bt / 1024;
      					$ndx++;
	         		}
			}
		}
		return((floor($bt*10)/10)." ".$a[$ndx]);
	}

}