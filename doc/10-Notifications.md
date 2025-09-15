# Sending Notifications

This module provides a CLI command to send notifications to the Icinga ServiceNow daemon.

Sending Problem notifications:

```bash
icingacli servicenow --state Critical --type Problem \
--output "This host is on fire!" \
--host Node2 --name "generic" --template "mytemplate"

icingacli servicenow --state Critical --type Acknowledgement \
--output "Get some water." \
--host Node2 --name "generic" --template "mytemplate"

icingacli servicenow --state OK --type Recovery \
--output "Splash!" \
--host Node2 --name "generic" --template "mytemplate"
```

Sending Downtime notifications:

```bash
icingacli servicenow --state OK --type DowntimeStart \
--output "Maintenance started" \
--host Node1 --name "generic" --template "mytemplate"

icingacli servicenow --state OK --type DowntimeEnd \
--output "Maintenance ended" \
--host Node1 --name "generic" --template "mytemplate"
```

Sending Flapping notifications:

```bash
icingacli servicenow --state OK --type FlappingStart \
--output "Flapping started" \
--host Node1 --name "generic" --template "mytemplate"

icingacli servicenow --state OK --type DowntimeEnd \
--output "Flapping ended" \
--host Node1 --name "generic" --template "mytemplate"
```

## Icinga 2 NotificationCommand

In Icinga 2, a related `NotificationCommand` definition could look like this:

```
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
      value = "$notification_serviceoutput$"
    }
    "--state" = {
      required = true
      value = "$notification_servicestate$"
    }
    "--type" = {
      required = true
      value = "$notification_type$"
    }
    "--template" = {
       value = "$snow_template$"
    }
    "--name" = {
       value = "$snow_name$"
    }
  }

  vars += {
    notification_type = "$notification.type$"
    notification_hostname = "$host.name$"
    notification_servicename = "$service.name$"
    notification_serviceoutput = "$service.output$"
    notification_servicestate = "$service.state$"
    notification_name= "$snow.name$"
    notification_template = "$snow.template$"
  }
}
```
