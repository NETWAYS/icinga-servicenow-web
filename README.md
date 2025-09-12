# Icinga ServiceNow Web

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

