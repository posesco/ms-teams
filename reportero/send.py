import smtplib
import ssl
import os
import json
from email.mime.base import MIMEBase
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from urllib.request import Request, urlopen


artifact = os.environ['ARTIFACT']
env = os.environ['ENV']
domain = os.environ['DOMAIN']
project = os.environ['PROJECT']
name_report = os.environ['NAME_REPORT']
smtp_server = os.environ['SMTP_SERVER']
password = os.environ['PASSWORD']
sender_email = os.environ['SENDER_EMAIL']


def email():
    recipients = ["user1@example.com", "user2@example.com"]
    msg = MIMEMultipart("alternative")
    msg["Subject"] = f"{name_report} - {project}"
    msg["From"] = f"Security Report<{sender_email}>"
    msg["To"] = ", ".join(recipients)
    report = open(artifact, "r")
    part = MIMEText(report.read(), "html")
    print(part)
    msg.attach(part)
    context = ssl.create_default_context()
    server = smtplib.SMTP(smtp_server, 587)
    server.starttls(context=context)
    server.login(sender_email, password)
    server.sendmail(sender_email, recipients, msg.as_string())


def teams():
    web_hook_url = 'https://webhook.office.com/webhookb2/36374362-6fb7...'
    with open("payload.json", "r") as payload:
        msg_teams = json.load(payload)
        msg_teams['sections'][0]['title'] = f"**Reporte: {name_report}**"
        msg_teams['sections'][0]['facts'][0]['value'] = project
        msg_teams['sections'][0]['facts'][1]['value'] = env
        
        if domain == '':
            del msg_teams['sections'][0]['facts'][2]
        else:
            msg_teams['sections'][0]['facts'][2]['value'] = domain
        
        msg_teams['potentialAction'][0]['targets'][0]['uri'] = f"https://mysubdomain.example.com/{artifact}"

    request = Request(web_hook_url, json.dumps(msg_teams).encode('utf-8'))
    response = urlopen(request)
    response.read()


if __name__ == '__main__':
    email()
    teams()