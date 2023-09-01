# Commands

## Init partner countries

The `enabel:partner-countries:init` command loads the base data for the partner countries table from Symfony's Intl\Country component.

### Usage

```bash
  bin/console enabel:partner-countries:init
```

### Details

This command fetches country data from the Symfony Intl\Country component and inserts it into the partner countries table. It ensures that your application has a consistent, up-to-date list of countries based on international standards.

### Notes
Ensure your database connection is correctly configured before running this command.
Make sure the bundle is appropriately configured.
If countries already exist in the table, this command does nothing.

## Update partner countries

The `enabel:partner-countries:update` command update the data of the partner countries table from Symfony's Intl\Country component.

### Usage

```bash
  bin/console enabel:partner-countries:update
```

### Details

This command fetches country data from the Symfony Intl\Country component and updated it into the partner countries table. It ensures that your application has a consistent, up-to-date list of countries based on international standards.

### Notes
Ensure your database connection is correctly configured before running this command.
Make sure the bundle is appropriately configured.
If the table is empty, this command does nothing.
