import json
import logging
import datetime as datetime_iso
from urllib.request import Request, urlopen

channels = {
    'channel_1': 'https://webhook.office.com/webhookb2/36374362-6fb7-4875-...',
    'channel_2': 'https://webhook.office.com/webhookb2/c544faf1-5ab8-4c77-...'
}

logger = logging.getLogger()
logger.setLevel(logging.INFO)


def formatted_object(raw_object):
    validate = raw_object
    for search_slash in validate:
        if (search_slash == '/'):
            break
        else:
            validate = raw_object + '///'

    control = validate.split('/')[0]
    if control == 'env%3A':
        workspace = True
    else:
        workspace = False
    environment = validate.split('/')[1]
    if environment == None:
        environment = ''
    level = validate.split('/')[2]
    if level == None:
        level = ''
    service = validate.split('/')[3]
    if service == None or workspace == False:
        service = raw_object
    result = [workspace, environment, level, service]
    return result


def lambda_handler(event, context):

    message = dict(event['Records'][0])
    bucket_s3 = message['s3']['bucket']['name']
    region = message['awsRegion']
    type_event = message['eventName']
    user = message['userIdentity']['principalId']
    source = message['requestParameters']['sourceIPAddress']
    if source == '70.86.221.140':
        source = source + ' (mywebsite.com)'
    raw_object = message['s3']['object']['key']
    output_object = formatted_object(raw_object)
    datetime_iso_transform = datetime_iso.datetime.fromisoformat(
        message['eventTime'][:-1])
    dt_formatted = datetime_iso_transform.strftime(
        '%Y-%m-%d %H:%M:%S').split(' ')

    teams_message = {
        "@context": "https://schema.org/extensions",
        "@type": "MessageCard",
        "summary": "AWS Infrastructure Event",
        "themeColor": "f8991d",
        "title": f"Status update: {output_object[3]}",
        "text": f"Change recorded at **{dt_formatted[1]}** **{dt_formatted[0]}**",
        "sections": [
            {
                "facts": [
                    {
                        "name": "Bucket affected: ",
                        "value": f"{bucket_s3}"
                    },
                    {
                        "name": "Region: ",
                        "value": f"{region}"
                    },
                    {
                        "name": "Type of event: ",
                        "value": f"{type_event}"
                    },
                    {
                        "name": "User: ",
                        "value": f"{user}"
                    },
                    {
                        "name": "Source IP: ",
                        "value": f"{source}"
                    },
                    {
                        "name": "Use of Workspace: ",
                        "value": f"{output_object[0]}"
                    },
                    {
                        "name": "Environment: ",
                        "value": f"{output_object[1]}"
                    },
                    {
                        "name": "Level: ",
                        "value": f"{output_object[2]}"
                    },
                    {
                        "name": "Service: ",
                        "value": f"{output_object[3]}"
                    }
                ]
            }
        ]
    }

    for web_hook_url in channels.values():
        request = Request(web_hook_url, json.dumps(
            teams_message).encode('utf-8'))
        response = urlopen(request)
        response.read()
        logger.info("Message posted")


if __name__ == '__main__':
    lambda_handler(event, context)
