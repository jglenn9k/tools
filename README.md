# Network Tools

A small, self-hosted web toolbox for DNS, routing, connectivity, and TLS diagnostics.

## What it includes

- Ping, WHOIS, traceroute, forward and reverse DNS
- SMTP port connectivity checks
- Nmap-based Heartbleed and TLS cipher scans
- A responsive, accessible interface with no client-side framework or third-party CDN
- Validated inputs and shell-free command execution

## Requirements

- A web server configured for PHP and CGI execution
- PHP 7.4 or newer
- Python 3.8 or newer
- `dig`, `ping`, `traceroute`, `whois`, and `nmap`

Serve `htdocs/` as the document root and expose `cgi-bin/` as the CGI directory. The web-server user must be allowed to execute the diagnostic binaries. For security, deploy behind HTTPS, apply request rate limits, and restrict access if the service is not intended to be public.

Example Apache configuration:

```apache
DocumentRoot /path/to/tools/htdocs
ScriptAlias /cgi-bin/ /path/to/tools/cgi-bin/

<Directory /path/to/tools/cgi-bin>
    Options +ExecCGI
    Require all granted
</Directory>
```

## Development checks

```sh
php -l htdocs/index.php
python3 -m py_compile cgi-bin/tool.py
for file in cgi-bin/dig cgi-bin/heartbleed cgi-bin/ping cgi-bin/poodle cgi-bin/reversedig cgi-bin/smtp cgi-bin/traceroute cgi-bin/whois; do
    sh -n "$file"
done
```

Issues and feature requests are welcome in the [GitHub issue tracker](https://github.com/jglenn9k/tools/issues).
