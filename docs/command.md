# Commands

## Sync partner countries

The `enabel:partner-countries:sync` command sync the data of the partner countries table from Symfony's Intl\Country component.

### Usage

```bash
  bin/console enabel:partner-countries:sync
```

### Details

This command fetches country data from the Symfony Intl\Country component and updated it into the partner countries table. It ensures that your application has a consistent, up-to-date list of countries based on international standards.

### Notes
Ensure your database connection is correctly configured before running this command.
Make sure the bundle is appropriately configured.
