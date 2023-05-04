# petition.town is a tool for effective mass email campaigns

A self-hosted, file-based solution for mass email campaigns.

- Generates a pre-formatted email to be sent to decision-makers via the user's own email account
- Users can edit the default message and subject line before sending, to add legitimacy & personality
- Campaign data and site settings are stored in a `.yml` text file
- The organizer's email address is CCed by default, so users can keep them in the loop
- Sending through users' own email app avoids the need for third-party email APIs and spam protection measures

This app was first developed for [Hold The Line Waterloo Region](https://holdthelinewr.org)'s campaign against Bill 66. We coordinated with dozens of local advocacy groups. Community members sent over 18,000 emails to local mayors, councillors, and members of provincial parliament. In less than a month, Bill 66 was amended to respect our demands.

Sending these messages as individual emails was more effective than a standard petition. Users could customize their message, and recipients can reply to emails in a genuine one-on-one conversation. (We also flooded each recipient's inbox with hundreds of messages.)

Use this tool wisely.

## Installation & setup

### 1. Add the entire repo to your server's document root

If you're using git, `cd` into your desired directory and copy this repo:

````
git clone https://github.com/samnabi/traction.git .
````

All dependencies are included, so we don't need to load them with submodules or composer or anything like that.

*Note: it's best to install this in the document root of a domain or subdomain. Some links may not work if you install it into a subfolder.*

### 2. Set the app-wide password

When you first point your browser to `index.php`, it should ask you to create a password. Once logged in, it will redirect you to `account.php`, where you can change site settings or create a new campaign.

All account settings (just `passwordhash` at the moment) are stored in the `settings.yml` file, which looks like this:

```yaml
password: passwordhash
```

### 3. Set up your first campaign

From `account.php` or `edit.php`, you can create a new campaign from the sidebar. It will redirect you to `edit.php`, where you can enter all the Campaign information in a web form.

Campaign data is stored under `campaigns/` in YAML files, using the `.yml` extension. There is one file per campaign. This is what the data files look like:

```yaml
organization: Your organization name
email: Your organization's email address
logo: filename.png
title: A title for your campaign
info: Message people see above the form.
recipients:
  - name: First Recipient
    email: first@example.com
  - name: Second Recipient
    email: second@example.com
  - name: Third Recipient
    email: third@example.com
subject: Subject line of the email
message: Body of the email
social_text: Pre-populated text for social sharing.
twitter: yourtwitterusername
```

For more information about the YAML file format, please see <https://yaml.org/start.html>

## Usage

### Loading the campaign

To load a particular campaign, pass its filename as a query parameter.

So, `http://example.com?campaign=test` will load the campaign located at `camapigns/test.yml`.

If there is no `campaign` parameter in the URL, the first file found under `campaigns/` will be loaded.

### Sending emails

This app generates a `mailto` link that the user can send using their own default email app. This structure helps avoid petitions being marked as spam and puts total control in the hands of the user.

There is no tracking or analytics code in the emails. Once the message is sent, Traction doesn't keep tabs on further replies between the user and recipients.

After submitting, the user is prompted to share the campaign on Twitter or Facebook.


## Requirements

- PHP 5.4+
- Apache server with `.htaccess`


## Dependencies

Traction comes bundled with one dependency:

- [Kirby Toolkit v2](https://github.com/getkirby-v2/toolkit) (located in `inc/toolkit`)


## License 

<http://www.opensource.org/licenses/mit-license.php>


## Author

Sam Nabi
<sam@samnabi.com>  
<https://samnabi.com>  