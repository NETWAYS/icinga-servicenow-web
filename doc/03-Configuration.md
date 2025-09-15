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

[db]
resource = "servicenow_db"
```
