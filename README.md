# MailMine
IMAP-powered library for effortlessly extracting valuable data from incoming emails. Streamline your email data processing with ease and efficiency. 

## Why?
I needed a simple way to send a webhook whenever a specific email is received, this simplifies things greatly and allows me to run the script on another thread to prevent blocking.

## Limitations
The script as it stands was made for me to listen for attachments only.
This script allows subscribing to one email server, and launching a single webhook. Next update will allow multiple email servers, and multiple webhooks based on different filters.

## Requirements
This script requires [Composer](https://getcomposer.org/download)

### PHP Extensions:
- ext-mbstring
- ext-mcrypt

## Setup
Edit "mailmine.json" with IMAP server connection details. Then select relevant filters.

## Usage
Download this repo, ensure your PHP has the required extensions mentioned above.

Run:

`composer install`

Run

`php main.php`

to start the IMAP idle listener.

## Filters
#### address
The from address, only listen for emails from this address.
#### attachments
Specify an array of attachment extensions to listen for, or leave blank.
#### webhook
##### url
The url to send a webhook to
##### send_attachments
N/A
