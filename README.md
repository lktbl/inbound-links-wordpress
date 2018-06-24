# Inbound Links Wordpress plugin

This plugin helps you organize incoming traffic to your website. You can set up a GET parameter, witch will be tracked.
For example:
- If you share `www.example.com?source=facebook` the plugin will count every request that is coming from Facebook.

## Features
- Pie chart statistic for incoming traffic
- Logs every request
- Option to ignore repeating requests

## Creating production files
To build production files install Node libraries with
```
npm install
```
When complete, run
```
gulp build
```

The finished plugin will be `inbound-links.zip`
