## Introduction
This a base for Debricked's backend home task.

## How to use

Start the environment and upload files via endpoint `POST http://localhost:8888/api/uploads` ([Docs](https://github.com/dmytro-dymarchuk/backend-home-task/blob/master/src/Controller/UploadController.php)). Check your email or Slack notifications. 

### Starting the environment
`docker-compose up`

### Stopping the environment
`docker-compose down`

### Configuring services

To configure the service use the following environment variables:

* `DEBRICKED_TOKEN` - valid JWT to authorize in Debricked API.
* `ALLOWED_VULNERABILITIES_COUNT` - limit of found vulnerabilities exceeding that notifications are triggered.
* `EMAIL_TRIGGERS` - list of triggers sending email notifications. One or more values are separated by `,`. Possible values see [here](https://github.com/dmytro-dymarchuk/backend-home-task/blob/master/src/Component/Enum/TriggerEnum.php).
* `SLACK_TRIGGERS` - list of triggers sending Slack notifications. One or more values are separated by `,`. Possible values see [here](https://github.com/dmytro-dymarchuk/backend-home-task/blob/master/src/Component/Enum/TriggerEnum.php).
* `EMAIL_TO` - email receiving notification about triggers. You can find your email in [mailhog](http://localhost:8025/).
* `SLACK_DSN` - DSN to configure the recipient of Slack's notifications.
  E.g.:
```
SLACK_DSN=slack://TOKEN@default?channel=CHANNEL
```
where:
- `TOKEN` is your Bot User OAuth Access Token (they begin with `xoxb-`)
- `CHANNEL` is a channel, private group, or IM channel to send messages to, it can be an encoded ID or a name.
