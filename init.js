plugin.loadMainCSS();
plugin.loadLang();


// show the current values in the options page
plugin.addAndShowSettings = theWebUI.addAndShowSettings;
theWebUI.addAndShowSettings = function( arg ) {
	if (plugin.enabled) {
		// set the values
		//console.log("EMAILER SHOULD BE SETTING CONFIG VALUES");

		// apply values to UI fields
		$('#emailerOptionSmtpServer').val(theWebUI.emailer.emailerOptionSmtpServer);
		$('#emailerOptionSmtpPort').val(theWebUI.emailer.emailerOptionSmtpPort);
		$('#emailerOptionSmtpTls').prop("checked",(theWebUI.emailer.emailerOptionSmtpTls==1));
		$('#emailerOptionUsername').val(theWebUI.emailer.emailerOptionUsername);
		$('#emailerOptionPassword').val(theWebUI.emailer.emailerOptionPassword);
		$('#emailerOptionRecipientEmail').val(theWebUI.emailer.emailerOptionRecipientEmail);
	}

	plugin.addAndShowSettings.call(theWebUI, arg);
}

// set configuration changes
plugin.setSettings = theWebUI.setSettings;
theWebUI.setSettings = function()
{
	//console.log('setSettings');

	plugin.setSettings.call(this);
	if (plugin.enabled) {
		// could also check if the SMTP settings have changed
		this.request("?action=setsmtp");
	}
}

rTorrentStub.prototype.setsmtp = function()
{
	//console.log('rTorrentStub.prototype.setsmtp');

	theWebUI.emailer.emailerOptionSmtpServer = $('#emailerOptionSmtpServer').val();
	theWebUI.emailer.emailerOptionSmtpPort = $('#emailerOptionSmtpPort').val();
	theWebUI.emailer.emailerOptionSmtpTls = $('#emailerOptionSmtpTls').prop("checked") ? 1 : 0;
	theWebUI.emailer.emailerOptionUsername = $('#emailerOptionUsername').val();
	theWebUI.emailer.emailerOptionPassword = $('#emailerOptionPassword').val();
	theWebUI.emailer.emailerOptionRecipientEmail = $('#emailerOptionRecipientEmail').val();

	this.content = "cmd=set&test=whatup"
		+ "&emailerOptionSmtpServer=" + theWebUI.emailer.emailerOptionSmtpServer
		+ "&emailerOptionSmtpPort=" + theWebUI.emailer.emailerOptionSmtpPort
		+ "&emailerOptionSmtpTls=" + theWebUI.emailer.emailerOptionSmtpTls
		+ "&emailerOptionUsername=" + theWebUI.emailer.emailerOptionUsername
		+ "&emailerOptionPassword=" + theWebUI.emailer.emailerOptionPassword
		+ "&emailerOptionRecipientEmail=" + theWebUI.emailer.emailerOptionRecipientEmail
		;


	this.contentType = "application/x-www-form-urlencoded";
	this.mountPoint = "plugins/emailer/action.php";
	this.dataType = "script";
}

//plugin.emailerSendTest = theWebUI.emailerSendTest;
theWebUI.emailerSendTest = function(e)
{
	//console.log("emailerSendTest: ", e);

	//plugin.emailerSendTest.class(this);

	// @TODO: make a POST request that will send a test email using our script
	if (plugin.enabled) {
		//this.request('?action=sendtestemail');
		$('#emailerResultContainer').text('');
		theWebUI.request('?action=sendtestemail', [theWebUI.showTestResults, this]);
	}	
}

theWebUI.showTestResults = function (reply)
{
	//console.log('showTestResults,reply: ', reply);
	// @TODO: display the error message?

	var message = reply.message ? reply.message : 'Error!';

	$('#emailerResultContainer').html(message.join('<br>'));
}

rTorrentStub.prototype.sendtestemail = function()
{
	//console.log('rTorrentStub.prototype.sendtestemail');

	var testcfg = {
		emailer: {},
	};

	//console.log('testcfg: ', testcfg);

	testcfg.emailer.emailerOptionSmtpServer = $('#emailerOptionSmtpServer').val();
	testcfg.emailer.emailerOptionSmtpPort = $('#emailerOptionSmtpPort').val();
	testcfg.emailer.emailerOptionSmtpTls = $('#emailerOptionSmtpTls').prop("checked") ? 1 : 0;
	testcfg.emailer.emailerOptionUsername = $('#emailerOptionUsername').val();
	testcfg.emailer.emailerOptionPassword = $('#emailerOptionPassword').val();
	testcfg.emailer.emailerOptionRecipientEmail = $('#emailerOptionRecipientEmail').val();

	this.content = "cmd=sendtest"
		+ "&emailerOptionSmtpServer=" + testcfg.emailer.emailerOptionSmtpServer
		+ "&emailerOptionSmtpPort=" + testcfg.emailer.emailerOptionSmtpPort
		+ "&emailerOptionSmtpTls=" + testcfg.emailer.emailerOptionSmtpTls
		+ "&emailerOptionUsername=" + testcfg.emailer.emailerOptionUsername
		+ "&emailerOptionPassword=" + testcfg.emailer.emailerOptionPassword
		+ "&emailerOptionRecipientEmail=" + testcfg.emailer.emailerOptionRecipientEmail
		;

	//console.log('testcfg: ', testcfg);
	//console.log('content: ', this.content);

	this.contentType = "application/x-www-form-urlencoded";
	this.mountPoint = "plugins/emailer/action.php";
	this.dataType = "json";
}


plugin.onLangLoaded = function ()
{
	//console.log("EMAILER: onLangLoaded called");
	//console.log('thePlugins: ', thePlugins);
	//console.log('theDialogManager', theDialogManager);
	//console.log('theUILang', theUILang);

	//console.log('theWebUI:', theWebUI);
	//console.log('theWebUI.emailer:', theWebUI.emailer);

	var s = '';

	// display the options menu
	s += '<fieldset>';
	s += 	'<legend>'+theUILang.emailerOptionsPanelTitle+'</legend>';
	s +=	'<table>';
	s +=		'<tr>';
	s +=			'<td><label for="emailerOptionSmtpServer">'+theUILang.emailerOptionSmtpServer+':</label></td>';
	s +=			'<td><input type="text" id="emailerOptionSmtpServer" class="TextboxLarge"></td>';
	s +=		'</tr>';
	s +=		'<tr>';
	s +=			'<td><label for="emailerOptionSmtpPort">'+theUILang.emailerOptionSmtpPort+':</label></td>';
	s +=			'<td><input type="text" id="emailerOptionSmtpPort" class="Textbox num"></td>';
	s +=		'</tr>';
	s +=		'<tr>';
	s +=			'<td><label for="emailerOptionSmtpTls">'+theUILang.emailerOptionSmtpTls+':</label></td>';
	s +=			'<td><input type="checkbox" id="emailerOptionSmtpTls"></td>';
	s +=		'</tr>';
	s +=		'<tr>';
	s +=			'<td><label for="emailerOptionUsername">'+theUILang.emailerOptionUsername+':</label></td>';
	s +=			'<td><input type="text" id="emailerOptionUsername" class="TextboxLarge"></td>';
	s +=		'</tr>';
	s +=		'<tr>';
	s +=			'<td><label for="emailerOptionPassword">'+theUILang.emailerOptionPassword+':</label></td>';
	s +=			'<td><input type="password" id="emailerOptionPassword" class="TextboxLarge"></td>';
	s +=		'</tr>';
	s +=	'</table>';
	s += '</fieldset>';

	s += '<fieldset>';
	s += 	'<legend>'+theUILang.emailerOptionLegendRecipient+'</legend>';
	s +=	'<table>';
	s +=		'<tr>';
	s +=			'<td><label for="emailerOptionRecipientEmail">'+theUILang.emailerOptionRecipientEmail+':</label></td>';
	s +=			'<td><input type="text" id="emailerOptionRecipientEmail" class="TextboxLarge"></td>';
	s +=		'</tr>';
	s +=	'</table>';
	s += '</fieldset>';

	s += '<fieldset>';
	s += 	'<div>';
	//s +=	'<button type="button">'+theUILang.emailerOptionTestButtonLabel+'</button>';
	s +=		'<input type="button" id="emailerTestButton" class="button" value="'+theUILang.emailerOptionTestButtonLabel+'">';
	s += 	'</div>';

	s += 	'<div id="emailerSettingsInform">Put a message here about needing additional config settings for Gmail (legacy app settings).';
	s += 	'</div>';
	s += 	'<div id="emailerResultContainer">';
	s +=		'Status...';
	s += 	'</div>';
	s += '</fieldset>';

	this.attachPageToOptions($("<div>").attr("id","st_emailer").html(s).get(0),theUILang.emailerOptionsName);


	// add click listener to our test button
	$("#emailerTestButton").click(theWebUI.emailerSendTest);

}

plugin.langLoaded = function ()
{
	//console.log("EMAILER: langLoaded -> plugin: ", plugin);

	if (plugin.enabled) {
		plugin.onLangLoaded();
	}
}

plugin.onRemove = function()
{
	this.removePageFromOptions("st_emailer");
}