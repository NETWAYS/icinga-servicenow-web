# Sending Notifications

This module provides a CLI command to send notifications to the Icinga ServiceNow daemon.

Sending Problem notifications:

```bash
icingacli servicenow send notification --state Critical --type Problem \
--output "This host is on fire!" \
--host Node2 --name "generic" --template "mytemplate"

icingacli servicenow send notification --state Critical --type Acknowledgement \
--output "Get some water." \
--host Node2 --name "generic" --template "mytemplate"

icingacli servicenow send notification --state OK --type Recovery \
--output "Splash!" \
--host Node2 --name "generic" --template "mytemplate"
```

Sending Downtime notifications:

```bash
icingacli servicenow send notification --state OK --type DowntimeStart \
--output "Maintenance started" \
--host Node1 --name "generic" --template "mytemplate"

icingacli servicenow send notification --state OK --type DowntimeEnd \
--output "Maintenance ended" \
--host Node1 --name "generic" --template "mytemplate"
```

Sending Flapping notifications:

```bash
icingacli servicenow send notification --state OK --type FlappingStart \
--output "Flapping started" \
--host Node1 --name "generic" --template "mytemplate"

icingacli servicenow send notification --state OK --type DowntimeEnd \
--output "Flapping ended" \
--host Node1 --name "generic" --template "mytemplate"
```

Sending Problem notification with additional fields:

```bash
icingacli servicenow send notification --state Critical --type Problem \
--output "An error occured" \
--host Node1 --name "generic" \
--extra "short_description=my short description" \
--extra "description=my description"
```

## Icinga 2 NotificationCommand

In Icinga 2, a related `NotificationCommand` definition could look like this:

```bash
object NotificationCommand "servicenow-host-notification" {
  command = [ "/usr/bin/icingacli", "servicenow", "send", "notification" ]

  arguments += {
    "--host" = {
      required = true
      value = "$notification_hostname$"
    }
    "--output" = {
      required = true
      value = "$notification_output$"
    }
    "--state" = {
      required = true
      value = "$notification_state$"
    }
    "--type" = {
      required = true
      value = "$notification_type$"
    }
    "--name" = {
      required = true
      value = "$snow_name$"
    }
    "--template" = {
      value = "$snow_template$"
    }
    "--extra" = {
      value = "$snow_additional_fields$"
    }
  }

  vars += {
    notification_type = "$notification.type$"
    notification_hostname = "$host.name$"
    notification_output = "$host.output$"
    notification_state = "$host.state$"
    notification_name = "$snow.name$"
    notification_template = "$snow.template$"
    notification_extra = "$snow_additional_fields$"
  }
}

object NotificationCommand "servicenow-service-notification" {
  command = [ "/usr/bin/icingacli", "servicenow", "send", "notification" ]

  arguments += {
    "--service" = {
      required = true
      value = "$notification_servicename$"
    }
    "--host" = {
      required = true
      value = "$notification_hostname$"
    }
    "--output" = {
      required = true
      value = "$notification_output$"
    }
    "--state" = {
      required = true
      value = "$notification_state$"
    }
    "--type" = {
      required = true
      value = "$notification_type$"
    }
    "--name" = {
      required = true
      value = "$snow_name$"
    }
    "--template" = {
      value = "$snow_template$"
    }
    "--extra" = {
      value = "$snow_additional_fields$"
    }
  }

  vars += {
    notification_type = "$notification.type$"
    notification_hostname = "$host.name$"
    notification_servicename = "$service.name$"
    notification_output = "$service.output$"
    notification_state = "$service.state$"
    notification_name = "$snow.name$"
    notification_template = "$snow.template$"
    notification_extra = "$snow_additional_fields$"
  }
}
```

```bash
template Notification "snow-host-notification" {
  command = "servicenow-host-notification"

  types = [ Problem, Acknowledgement, Recovery, Custom,
            FlappingStart, FlappingEnd,
            DowntimeStart, DowntimeEnd, DowntimeRemoved ]
}

template Notification "snow-svc-notification" {
  command = "servicenow-service-notification"

  states = [ OK, Warning, Critical, Unknown ]
  types = [ Problem, Acknowledgement, Recovery, FlappingStart, FlappingEnd,
            DowntimeStart, DowntimeEnd, DowntimeRemoved ]
}
```
