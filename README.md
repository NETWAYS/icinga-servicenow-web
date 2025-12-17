**Note:** This is an early release that is still in development and prone to change

# Icinga ServiceNow Integration Web

An Icinga2 integration with ServiceNow Incident Management.

This module is able to:

* Create ServiceNow incidents for problems detected by Icinga
* Create only one incident per problem
* Show a list of open incidents for Hosts or Services
* Close and acknowledge ServiceNow incidents via Icinga

Icinga ServiceNow Integration consisting of:

* Icinga ServiceNow daemon, which uses the ServiceNow API and a local database to create and manage Incidents
* Icinga ServiceNow Web, which connects to the database for visualizing Incidents and their state
