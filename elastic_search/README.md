# Script for Kibana alerts 
## How to use

Kibana Path: Stack Management > Rules and Connectors

Example of payload to trigger notifications
### Error Logs
```json
{
  "reason": "Matches {{context.matchingDocuments}} error logs",
  "alert": "no",
  "title": "App1 error notification",
  "url": "https://127.0.0.1:5601/goto/5f482010-8064-11ec-9844-df8c3c5ca50c",
  "app": "app1",
  "groups": "margay",
  "@timestamp": "{{date}}"
}
```
### PHP Slow Logs
```json
{
  "reason": " slow requests detected in less than 5 minutes",
  "alert": "no",
  "php": "yes",
  "title": "Accumulation of slow php requests ",
  "@timestamp": "{{date}}"
}
```
### Uptime Alert
#### Recovered
```json
{
  "reason": "{{alert.id}}",
  "alert": "no",
  "groups": "channel_1,channel_2",
  "title": "Server {{alert.actionGroupName}}",
  "@timestamp": "{{date}}"
}
```
#### Firing
```json
{
  "reason": "{{state.reason}}",
  "alert": "no",
  "groups": "channel_1,channel_2",
  "title": "Server Down",
  "@timestamp": "{{date}}"
}
```
