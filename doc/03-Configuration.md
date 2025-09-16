# Configuration

## Manual Configuration

To manually create a related configuration file:

```bash
install -d -m 2770 -o www-data -g icingaweb2 /etc/icingaweb2/modules/servicenow
```

The `config.ini` in this directory:

```
[servicenow]
api_url = "https://icinga-servicenow-daemon:5910"
api_timeout = "10"
api_tls_insecure = "0"
instance_url = "https://my.service-now.com"

[db]
resource = "servicenow_db"
```

## Available Settings and defaults

| Option | Description | Default |
|---|---|---|
| api_url | The URL to the daemon including the scheme | "http://localhost:5910" |
| api_timeout | HTTP timeout for the daemon in seconds | 10 |
| api_tls_insecure | Skip the TLS verification for the daemon | false |
| instance_url | URL for the ServiceNow instance | "" |
| resource | Name of the database resource to use | "" |

