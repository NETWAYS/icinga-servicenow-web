# Icinga Web ServiceNow Integration Web

An Icinga2 integration with ServiceNow Incident Management.

This module is able to:

* Create ServiceNow incidents for problems detected by Icinga
* Create only one incident per problem
* Show a list of open incidents for Hosts or Services
* Close and acknowledge ServiceNow incidents via Icinga

## How it works

The Icinga ServiceNow Integration uses a daemon, which receives notifications and sends them to ServiceNow.
The Icinga ServiceNow Web module, provides a graphical interface and a CLI to send notifications to the daemon.

Once the daemon receives a notification it will transform it into a format ServiceNow understands,
this is done using templates.

The daemon also stores the incident in its own database to keep track of incidents.
This database is synchronized with ServiceNow on a regular interval (120 seconds, currently not configurable)
(Note: this means some changes are not visible in the interface immediately).

Once an incident is resolved in the database it will not be opened again, even if the state in ServiceNow changes.

The daemon will regularly remove resolved incidents from the database (older than 30 days, currently not configurable)
