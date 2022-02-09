#!/bin/bash
echo "Floated: `date +%Y%m%d_%H:%M:%S`" >> /var/log/keepalived.log
channel_1="https://webhook.office.com/webhookb2/36374362-6c..."
channel_2="https://webhook.office.com/webhookb2/363234t5-b7..."
read -d '' payLoad << EOF
{
    "@type": "MessageCard",
    "@context": "http://schema.org/extensions",
    "summary": "Floating IP Change",
    "themeColor": "ffff00",
    "title": "Floating ip: `hostname -I | awk '{print $2}'` Host change",
    "sections": [
        {
            "facts": [
                {
                    "name": "Destination IP",
                    "value": "`hostname -I | awk '{print $1}'`"
                },
                {
                    "name": "Destination Host",
                    "value": "`hostname`"
                },
                {
                    "name": "Jump Date",
                    "value": "`date +%Y%m%d_%H:%M:%S`"
                }
            ]
        }
    ]
}
EOF
curl -H "Content-Type: application/json" -d "${payLoad}" "${channel_1}"
curl -H "Content-Type: application/json" -d "${payLoad}" "${channel_2}"