<?php
declare(strict_types=1);
function uptime(): string { $raw = @file_get_contents('/proc/uptime'); if ($raw === false) return 'Unavailable'; $seconds = (int) floor((float) explode(' ', trim($raw))[0]); return sprintf('%d days, %d hours, %d minutes', intdiv($seconds, 86400), intdiv($seconds % 86400, 3600), intdiv($seconds % 3600, 60)); }
function e(string $value): string { return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
$remoteAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
$remoteHost = filter_var($remoteAddress, FILTER_VALIDATE_IP) ? (gethostbyaddr($remoteAddress) ?: 'Unknown') : 'Unknown';
$serverName = php_uname('n');
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$tools = [
 ['ping', 'Ping', 'Check whether a host responds and view round-trip times.', 'Host or IP address'],
 ['whois', 'WHOIS', 'Look up public registration information for a domain or IP.', 'Domain or IP address'],
 ['traceroute', 'Traceroute', 'Follow the network path between this server and a host.', 'Host or IP address'],
 ['reversedig', 'Reverse DNS', 'Find the hostname associated with an IP address.', 'IP address'],
 ['smtp', 'SMTP check', 'Test whether TCP port 25 is reachable on a mail server.', 'Mail server hostname'],
 ['heartbleed', 'Heartbleed scan', 'Run the Nmap TLS Heartbleed detection script.', 'Host or IP address'],
 ['poodle', 'TLS cipher scan', 'Inspect port 443 cipher support with Nmap.', 'Host or IP address'],
];
?>
<!doctype html>
<html lang="en">
<head>
 <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
 <meta name="description" content="Fast, focused network diagnostics from your browser."><meta name="color-scheme" content="dark light">
 <title>Network Tools</title><link rel="icon" href="/favicon.ico"><link rel="stylesheet" href="/app.css">
</head>
<body>
<header class="site-header"><h1>Network Tools</h1></header>
<main>
 <section class="status" aria-labelledby="connection-title">
  <h2 id="connection-title">Connection details</h2>
  <dl class="status-grid"><div><dt>Your IP</dt><dd><?= e($remoteAddress) ?></dd></div><div><dt>Hostname</dt><dd><?= e($remoteHost) ?></dd></div><div><dt>Server</dt><dd><?= e($serverName) ?></dd></div><div><dt>Server uptime</dt><dd><?= e(uptime()) ?></dd></div></dl>
  <p>Browser: <?= e($userAgent) ?></p>
 </section>
 <section aria-label="Diagnostics"><div class="tool-grid">
 <?php foreach ($tools as [$endpoint, $name, $description, $placeholder]): ?>
  <article class="tool-card"><div><h2><?= e($name) ?></h2><p><?= e($description) ?></p></div><form action="/cgi-bin/<?= e($endpoint) ?>" method="get"><label class="sr-only" for="<?= e($endpoint) ?>-target"><?= e($placeholder) ?></label><input id="<?= e($endpoint) ?>-target" name="ip" type="text" inputmode="url" required maxlength="253" autocomplete="off" spellcheck="false" placeholder="<?= e($placeholder) ?>"><button type="submit">Run <span aria-hidden="true">→</span></button></form></article>
 <?php endforeach; ?>
  <article class="tool-card tool-card-wide"><div><h2>DNS lookup</h2><p>Query a specific DNS record type for a hostname.</p></div><form action="/cgi-bin/dig" method="get"><label class="sr-only" for="dns-target">Domain or IP address</label><input id="dns-target" name="ip" type="text" required maxlength="253" autocomplete="off" spellcheck="false" placeholder="Domain or IP address"><label class="sr-only" for="dns-type">Record type</label><select id="dns-type" name="type"><?php foreach (['A','AAAA','CNAME','MX','NS','PTR','SOA','SRV','TXT','CAA','ANY'] as $type): ?><option value="<?= $type ?>"><?= $type ?></option><?php endforeach; ?></select><button type="submit">Look up <span aria-hidden="true">→</span></button></form></article>
 </div></section>
</main>
<footer><a href="https://github.com/jglenn9k/tools">GitHub</a> · <a href="https://github.com/jglenn9k/tools/issues">Report an issue</a></footer>
</body></html>
