# ruTorrent Email Plugin
This plugin adds email notifications to ruTorrent/rTorrent when downloads finish

> **Note:** Currently only supports Gmail.

![screenshot-1](https://user-images.githubusercontent.com/122149/55686481-b403e580-592f-11e9-9123-647b5e459877.PNG)

## Installation instructions
1. ```cd /var/www/rutorrent/plugins (or your ruTorrent insallation directory```
2. ```git clone https://github.com/ecko/rutorrent-emailer-plugin.git emailer```
> **Note:** It is important that the plugin directory is named 'emailer' so that the supporting files are loaded correctly.
3. ```chown -R www-data:www-data emailer```
4. Enable "Less secure app access" in Gmail -- https://support.google.com/accounts/answer/6010255
5. Start ruTorrent and configure settings for your Gmail account. Use the test button to confirm the settings are correct.

### Known Limitations
- Currently this plugin only supports sending from Gmail. Future versions will include support for additional mail providers.

### Issues / Feedback / Requests
Please submit an issue on github https://github.com/ecko/rutorrent-emailer-plugin/issues and include as much detail as possible. Error messages, logs, browser, server OS, and webserver software will be important.
