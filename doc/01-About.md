# Icinga ServiceNow Integration

This module provides an Icinga integration with ServiceNow Incident Management.
It allows for Icinga notifications to be sent to ServiceNow in order to open, update and close incidents.

This module is able to:

* Create ServiceNow incidents for problems detected by Icinga
* Create only one incident per problem and update the incident on further notifications
* Show a list of open incidents for Host or Service objects
* Close and acknowledge ServiceNow incidents via Icinga notifications

## How it works

The Icinga ServiceNow Integration has two components: a daemon, which receives notifications and sends them to ServiceNow and an Icinga ServiceNow Web module, to provide a graphical interface and a CLI to send notifications to the daemon.
The daemon receives Icinga notification via HTTP and transforms them into a format that ServiceNow understands.

This transformation is using two components: First, "templates" these are key-value objects that are attached to the notification information. For example:

```json
{
"display_name": "example-template",
"fields": {
  "ci_item": "example.localhost",
  "custom_field": "Icinga",
  "caller_id": "6816f79cc0a8016401c5a33be04be441"
  }
}
```

Second, multiple additional fields that be sent to the daemon.

```
additional_fields": {
 "ci_item": "node01.internal",
 "team": "awesome"
}
```

These mechanisms exist because the ServiceNow data model is flexible, and each user may have different requirements.
They allow for flexible mapping of Icinga data to ServiceNow tables.

We are aware that many ServiceNow instances use custom endpoints to manage data. Due to this flexibility, we have decided that the daemon will use the ServiceNow Table API to manage incidents. Users who must use custom endpoints can create a dedicated Icinga2 incident table and retrieve the data from there (e.g. via Service Now Business Rules).

The daemon also stores the created incident in its own SQL database to keep track of incidents.
This allows us to edit existing ServiceNow incidents rather than creating new ones for each notification.

This database is synchronized with ServiceNow at regular intervals (120 seconds by default).
Note that this means some changes are not immediately visible in the web interface.
Once an incident is resolved in the daemon's database through this synchronization, it will not be reopened, even if the state in ServiceNow changes. This is to avoid synchronizing the entire database.
By default, the daemon will regularly remove incidents that are more than 30 days old from its database.
This will not affect the incidents in ServiceNow; it is simply to prevent the database from growing endlessly.

Since ServiceNow is very flexible the daemon also provides many configurations to map Icinga data to ServiceNow.
For example:

```yaml
# Which table to use for incidents
incident_table_endpoint: "/api/now/v2/table/incident"

# Which field to use for "work_notes" data
incident_work_notes_field: "work_notes"

# Which field to use for "close_notes" data
incident_close_notes_field: "close_notes"

# Which field to use for "close_code" data
incident_close_code_field: "close_code"

# Which code to use when closing incidents
incident_close_code: "Resolved by caller"

# Which field to use for "state" data
incident_state_field: "state"

# A mapping of Icinga notification types and ServiceNow states
incident_state_mapping:
  Problem: "1"
  Acknowledgement: "2"
```

## Sending Notifications

The web module provides a CLI command to send notifications to the Icinga ServiceNow daemon.

To send a Problem notification from Icinga we can use it like this:

```bash
icingacli servicenow --state Critical --type Problem \
--output "This node is down" \
--host node01.internal --name "default-notification" --template "example-template"
```

The CLI command supports sending additional custom fields.
This is intended to extend a basic generic template with more data.

```bash
icingacli servicenow --state Critical --type Problem \
--output "This node is down" \
--host node02.internal --name "default-notification"
--extra=appOwner=jdoe --extra=pageOnCall=true
```

```bash
icingacli servicenow --state Critical --type Problem \
--output "This node is down" \
--host node02.internal --name "default-notification" --template "example-template"
--extra=appOwner=jdoe --extra=pageOnCall=true
```

Note that these additional fields override fields that are specified in a template

## Notifications Types

The Icinga2 notification types are handled by the daemon like this:

* Problem, if no ServiceNow incident exists it will create a new incident, otherwise it will update the existing incident
* Recovery, will update the ServiceNow state of an existing "Problem" type incident (using the `incident_state_mapping`)
* Acknowledgement, will update the ServiceNow state of an existing "Problem" type incident (using the `incident_state_mapping`)
* DowntimeStart, if no ServiceNow incident exists it will create a new incident, otherwise it will update the existing incident
* DowntimeEnd, will update the ServiceNow state of an existing "DowntimeStart" type incident (using the `incident_state_mapping`)
* FlappingStart, if no ServiceNow incident exists it will create a new incident, otherwise it will update the existing incident
* FlappingEnd, will update the ServiceNow state of an existing "FlappingStart" type incident (using the `incident_state_mapping`)
