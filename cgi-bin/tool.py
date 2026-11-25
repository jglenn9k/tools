#!/usr/bin/env python3
"""Secure CGI entry point for the network diagnostic tools."""
from __future__ import annotations

import html
import ipaddress
import os
import re
import shutil
import socket
import subprocess
import sys
from pathlib import Path
from urllib.parse import parse_qs

TOOLS = {
    "ping": ("Ping", ["ping", "-c", "6", "--"]),
    "whois": ("WHOIS", ["whois", "--"]),
    "traceroute": ("Traceroute", ["traceroute", "-m", "30", "--"]),
    "heartbleed": ("Heartbleed scan", ["nmap", "--script", "ssl-heartbleed", "--script-args", "vulns.showall", "-sV", "--"]),
    "poodle": ("TLS cipher scan", ["nmap", "--script", "ssl-enum-ciphers", "-p", "443", "--"]),
}
DNS_TYPES = {"A", "AAAA", "CAA", "CNAME", "MX", "NS", "PTR", "SOA", "SRV", "TXT", "ANY"}
HOST_RE = re.compile(r"(?=^.{1,253}$)(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)*[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.?$")

def valid_target(value: str) -> bool:
    if not value or any(c.isspace() for c in value): return False
    try: ipaddress.ip_address(value.strip("[]")); return True
    except ValueError: return bool(HOST_RE.fullmatch(value))

def page(title: str, output: str, *, error: bool = False) -> None:
    status = "400 Bad Request" if error else "200 OK"
    user_agent = os.environ.get("HTTP_USER_AGENT", "")
    if re.match(r"^(?:curl|wget)(?:/|\s|$)", user_agent, re.IGNORECASE):
        print(f"Status: {status}\r\nContent-Type: text/plain; charset=utf-8\r\nX-Content-Type-Options: nosniff\r\nVary: User-Agent\r\n")
        print(output)
        return
    print("Vary: User-Agent\r")
    print(f"Status: {status}\r\nContent-Type: text/html; charset=utf-8\r\nX-Content-Type-Options: nosniff\r\nContent-Security-Policy: default-src 'none'; style-src 'unsafe-inline'\r\n")
    color = "#ff8c8c" if error else "#70f0bd"
    print(f'''<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>{html.escape(title)}</title><style>:root{{color-scheme:dark;font-family:ui-monospace,SFMono-Regular,Consolas,monospace}}body{{margin:0;background:#07100e;color:#f0f8f4}}main{{width:min(calc(100% - 32px),1000px);margin:48px auto}}a{{color:#70f0bd}}h1{{font:700 clamp(1.5rem,4vw,2.5rem) system-ui;margin:0 0 24px}}pre{{padding:24px;overflow:auto;white-space:pre-wrap;word-break:break-word;background:#0d1916;border:1px solid #244138;border-radius:14px;border-top:3px solid {color};line-height:1.55}}nav{{margin-top:24px}}</style></head><body><main><h1>{html.escape(title)}</h1><pre>{html.escape(output)}</pre><nav><a href="/">← Back to all tools</a></nav></main></body></html>''')

def decode_timeout_output(value: bytes | str | None) -> str:
    if isinstance(value, bytes):
        return value.decode("utf-8", errors="replace")
    return value or ""

def run(command: list[str]) -> tuple[str, bool]:
    if shutil.which(command[0]) is None: return f"Required command is not installed: {command[0]}", True
    try:
        result = subprocess.run(command, capture_output=True, text=True, timeout=35, check=False, env={"PATH": "/usr/local/bin:/usr/bin:/bin", "LANG": "C.UTF-8"})
        output = (result.stdout + result.stderr).strip() or "Command completed without output."
        return output[:200_000], result.returncode != 0
    except subprocess.TimeoutExpired as exc:
        partial = (decode_timeout_output(exc.stdout) + decode_timeout_output(exc.stderr)).strip()
        return (partial + "\n\nRequest timed out after 35 seconds.").strip(), True

def main() -> None:
    name = Path(os.environ.get("SCRIPT_NAME", sys.argv[0])).name
    query = parse_qs(os.environ.get("QUERY_STRING", ""), keep_blank_values=True)
    target = query.get("ip", [""])[0].strip()
    if not valid_target(target): page("Invalid target", "Enter a valid hostname or IP address.", error=True); return
    if name == "dig":
        record = query.get("type", ["A"])[0].upper()
        if record not in DNS_TYPES: page("Invalid record type", "Choose a supported DNS record type.", error=True); return
        output, failed = run(["dig", "+noall", "+answer", "+comments", target, record]); page(f"DNS {record}: {target}", output, error=failed); return
    if name == "reversedig":
        try: ipaddress.ip_address(target.strip("[]"))
        except ValueError: page("Invalid IP address", "Reverse DNS requires an IPv4 or IPv6 address.", error=True); return
        output, failed = run(["dig", "+noall", "+answer", "+comments", "-x", target]); page(f"Reverse DNS: {target}", output, error=failed); return
    if name == "smtp":
        try:
            with socket.create_connection((target, 25), timeout=8) as connection:
                banner = connection.recv(4096).decode("utf-8", "replace").strip()
            page(f"SMTP check: {target}", banner or "Port 25 accepted a connection.")
        except OSError as exc: page(f"SMTP check: {target}", f"Connection failed: {exc}", error=True)
        return
    if name not in TOOLS: page("Unknown tool", "This diagnostic does not exist.", error=True); return
    title, command = TOOLS[name]
    output, failed = run(command + [target]); page(f"{title}: {target}", output, error=failed)

if __name__ == "__main__": main()
