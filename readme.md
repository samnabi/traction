# Traction: Send emails to multiple recipients with PHP + Mailjet

A self-hosted, file-based solution for mass email campaigns.

- Let people send the same email to multiple recipients with the click of a button
- Recipients get individual emails with a personalized salutation
- Users can edit the default message and subject line before sending, to add legitimacy & personality
- Campaign data and site settings are stored in a `.yml` text file
- The user's email address is listed in the reply-to field, so recipients can reply like normal and continue the conversation
- Reliable email delivery via Mailjet integration

Traction was developed for [Hold The Line Waterloo Region](https://holdthelinewr.org)'s campaign against Bill 66. We coordinated with dozens of local advocacy groups. Community members sent over 18,000 emails to local mayors, councillors, and members of provincial parliament. In less than a month, Bill 66 was amended to respect our demands.

Sending these messages as individual emails was more effective than a petition. Users could customize their message, and recipients could reply in a genuine one-on-one conversation. (We also flooded each recipient's inbox with hundreds of messages.)

Use this tool wisely.

## Installation & setup

### 1. Get a Mailjet account

Traction uses the third-party email service [Mailjet](https://mailjet.com) to send your messages. Sending emails through Mailjet helps avoid being marked as spam, and allows Traction to work on servers that don't have PHP's `mail()` enabled.

You can send up to 200 emails per day with a [free account](https://www.mailjet.com/pricing/). 

### 2. Add the entire repo to your server's document root

If you're using git, `cd` into your desired directory and copy this repo:

````
git clone https://github.com/samnabi/traction.git .
````

All dependencies are included, so we don't need to load them with submodules or composer or anything like that.

*Note: it's best to install this in the document root of a domain or subdomain. Some links may not work if you install it into a subfolder.*

### 3. Set up your settings

When you first point your browser to `index.php`, it should ask you to create a password. Once logged in, it will redirect you to `account.php`, where you can enter your Mailjet settings, as well as the site-wide settings such as your organization name and logo.

All account settings are stored in the `settings.yml` file, which looks like this:

```yaml
title: Your organization name
logo: filename.png
mailjet_key_public: xxxxxxxxx
mailjet_key_secret: xxxxxxxxx
mailjet_email_from: your@email.com
password: passwordhash
```

### 4. Set up your first campaign

From `account.php` or `edit.php`, you can create a new campaign from the sidebar. It will redirect you to `edit.php`, where you can enter all the Campaign information in a web form.

Campaign data is stored under `campaigns/` in YAML files, using the `.yml` extension. There is one file per campaign. This is what the data files look like:

```yaml
title: A title for your campaign
info: Message people see above the form.
recipients:
  - name: First Recipient
    email: first@example.com
    salutation: Dear Ms. Recipient,
  - name: Second Recipient
    email: second@example.com
    salutation: Dear Sir,
  - name: Third Recipient
    email: third@example.com
    salutation: Good afternoon Mr. Recipient,
subject: Subject line of the email
message: Body of the email
social_text: Pre-populated text for social sharing.
social_url: https://LinkForSocialMediaPosts.com
twitter: yourtwitterusername
```

For more information about the YAML file format, please see <https://yaml.org/start.html>

## Usage

### Loading the campaign

To load a particular campaign, pass its filename as a query parameter.

So, `http://example.com?campaign=test` will load the campaign located at `camapigns/test.yml`.

If there is no `campaign` parameter in the URL, the first file found under `campaigns/` will be loaded.

### Sending emails

An individual email will be sent to each recipient, each time someone submits the form.

There is no tracking or analytics code in the emails. Once the message is sent, Traction doesn't keep tabs on further replies between the user and recipients.

After submitting, the user is prompted to share the campaign on Twitter or Facebook.


## Requirements

- PHP 5.4+
- Mailjet account
- Apache server with `.htaccess`


## Dependencies

Traction comes bundled with two dependencies:

- [Mailjet API v3](https://github.com/mailjet/mailjet-apiv3-php) (located in `inc/mailjet`)
- [Kirby Toolkit v2](https://github.com/getkirby-v2/toolkit) (located in `inc/toolkit`)


## License 

<http://www.opensource.org/licenses/mit-license.php>


## Author

Sam Nabi
<sam@samnabi.com>  
<https://samnabi.com>  
<https://twitter.com/samnabi>
