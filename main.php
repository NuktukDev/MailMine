<?php

use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\IMAP;

require __DIR__ . '/vendor/autoload.php';
$DEFAULT_MAILBOX = 'INBOX';
$CONFIG_PATH = './mailmine.json';


function sendWebhook($url, $attachments) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
  curl_setopt($ch, CURLOPT_POST, 1);
  if($attachments) {
    curl_setopt($ch, CURLOPT_POSTFIELDS, $attachments);
  }
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
};

try {
  $config = json_decode(file_get_contents($CONFIG_PATH), true);
  $filters = $config['filter'];
  $cm = new ClientManager([
    'fetch' => IMAP::FT_PEEK
  ]);

  $client = $cm->make($config['imap_server']);
  $client->connect();
  print "Connected to IMAP server\n";

  $inbox = $client->getFolderByName($DEFAULT_MAILBOX);
  print "Selected ".$DEFAULT_MAILBOX." Mailbox\n";
  print "Idle on ".$DEFAULT_MAILBOX."\n";

  // listen for new messages and dispatch events
  $inbox->idle(function ($message) use ($config, $filters) {
    if($message->from->first()->mail != $filters['address'] || $message->subject != $filters['subject']) {
      return 1;
    }

    $attachments = $message->getAttachments()->filter(fn ($attachment) => in_array($attachment->getExtension(), $filters['attachments']));
    if($attachments->count() === 0) {
      return 1;
    }

    sendWebhook($config['webhook']['url'], $config['webhook']['sendAttachments'] ? $attachments->toJson() : null);
    echo "New message with the subject '".$message->subject."' received\n";
    return 1;
  }, 1200, true);
} catch (Exception $e) {
  print($e);
}