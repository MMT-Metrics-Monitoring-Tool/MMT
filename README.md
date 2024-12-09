# What is MMT?

Metrics Monitoring Tool is part of Pekka Mäkiaho's PhD work. MMT is in use at Tampere University's courses: Project work and Software Project management.
It is used for logging working hours in a project, for managing a project and reporting its state, and for observing one project or the whole portfolio visually. With MMT you can observe data at portfolio level, at project level or even see the statistics of an individual project member.

### Previews

Project members view:
![Screenshot_20241128_173932](https://github.com/user-attachments/assets/4598833f-9ac2-4d75-83c3-38c53473e3fd)

Overview of weekly reports for coaches and admins:
![image](https://github.com/user-attachments/assets/3355a77c-1d3a-44fc-8d9f-c9f0138e74c0)

# To start with Metrics Monitoring Tool (MMT) 

**It is important to note** that the latest MMT updates have been done with:  
- PHP 8.3 (in project 10 - Fall 2024)  
- CakePHP 4.5  
- MariaDB 10.3.39 is the server program used in V10.0, it is found to be compatible with the database

In case of unexpected errors, you should first check your versions.

# **How to set up**

## Prerequisites

To get started with MMT development, you need Docker and Make. The development environment was containerized in 2023 to make it easier to set up and also to prevent any obscure bugs that configuration differences might cause.

### Windows and Linux

**If you're on Windows using WSL 2 is highly recommended:** run `wsl --install` in PowerShell or Command Prompt and reboot your computer. The Linux distribution installed by WSL should be Ubuntu by default. For more instructions see https://learn.microsoft.com/en-us/windows/wsl/install.

To install Docker Engine on Ubuntu/Debian, follow thsese instructions: https://docs.docker.com/engine/install/ubuntu/#install-docker-engine. TL; DR:

```shell
sudo apt update
sudo apt install docker-ce docker-compose-plugin make
```

**NOTE**: You don't need to add yourself to the `docker` group. Use `sudo` instead.

### Mac

An alternative for Windows and Mac is to install Docker Desktop, but keep in mind that these instructions were written for (Debian-based) Linux. See https://docs.docker.com/compose/install/ for Docker Desktop installation instructions.

## Running locally

To run MMT locally, do the following:

1. `git clone git@github.com:MMT-Metrics-Monitoring-Tool/MMT.git`
2. `cd metrics-monitoring-tool`
3. `cp config/app_local.example.php config/app_local.php`. Use this file to make local changes to CakePHP.
4. Run `make run` to run local environment
5. You can now access MMT on [`http://localhost`](http://localhost), phpMyAdmin on [`http://localhost:8080`](http://localhost:8080) and MailCatcher on [`http://localhost:8008`](http://localhost:8008)
6. Run `make clean` to clean-up
7. **(Recommended)** Run `make enable-git-hooks` to automatically test code quality on commit.

### Testing locally

To run tests which are found in the tests dir, do the following:

1. `make clean`
2. `make run-test`
3. After `run-test` has started, in a new terminal, run `make test` to run tests.
4. Run `make clean` to clean up

# **Configurations**

Default database template `MMT Database.sql` found in `sql` folder is ready to use. You can also find the database creation and insertion phrases in the MMT_Database.sql file. The `sql`folder is also mounted in the Docker container to initialize database during **first startup**.

The database comes with metric types, work types, and 20 anonymous users predefined. There also is one admin user with email `admin@admin.com` and password `adminadmin`. This password should be changed.

Server and environment-related settings should be configured in `config/app_local.php`. Settings, such as metric type names and project’s default length, are usually configured in the model or controller files related to the specific setting.

---

**Auto logout timeout**\
To configure session timeout, add the following to your `app_local.php`:

```php
return [
    ...other settings...
    'Session' => [
        'timeout' => 123 // in minutes
    ],
    ...more settings...
];
```

---

**Database connection settings**\
See `'Datasources'` section in `config/app.php` and override settings in `config/app_local.php`.

---

**Debug mode**\
See `'debug'`section in `config/app.php` and override settings in `config/app_local.php`.

---

**Email settings**\
See `'EmailTransport'` section in `config/app.php` and override settings in `config/app_local.php`.

---

**Error level settings**\
See `'Error'`section in `config/app.php` and override settings in `config/app_local.php`.

---

**Landing page & routes settings**\
In `config/routes.php`. Set default controller action for the landing page (address ‘/’) and default action for the rest of the URLs.

---

**Layout & theme & colors & images**\
Most user-defined settings are done in `webroot/css/cake.css` with some individual stylings (for example table column width) done also directly in view template files. All the images such as the main logo are in folder `webroot/img`.

---

**Logging settings**\
See `'Log'`section in `config/app.php` and override settings in `config/app_local.php`.

---

**Metric types & metric type names**\
Metric types are configured in the database table ‘metrictypes’. Admin can edit these in MMT from page /metrictypes. These names/descriptions must match the ones in src\\Controller\\MetricsController.php and in src\\Template\\Metrics\\addmultiple.ctp

Metric type names can be found in src\\Controller\\MetricsController.php function getMetricNames(). Changing the metric type name in getMetricNames() changes metric type names in the program globally, except few charts where names are hardcoded. These can be changed in src\\Controller\\ChartsController.php.

---

**Pagination settings**\
Some pagination settings (such as order and number of entries per page) can be defined in the controller files. For example, project listings settings are configured in src\\Controller\\ProjectsController.php in index() function.

---

**Predictive working hours charts**\
The model can be found in src\\Model\\Table\\MembersTable.php, and controller in src\\Controller\\MembersController. In a case where the project's ending date is not defined, the default length of the chart is set to be 20 weeks from the project’s start date ($endingDate). Line colors and data point symbols are also configured in the model. Chart texts are configured in the controller.

---

**Statistics page & metrics and working hours tables**\
The threshold values are configured in `src/Model/Table/ProjectsTable.php` function `getStatusColors()`. They are based on actual averages of data collected from earlier MMT use. In case one column has both yellow and red status limits active, red naturally overrides the yellow.

The projects are split into 4 phases with the threshold values varying by phase.\
`phase 1 = weeks 1-5`\
`phase 2 = weeks 6-10`\
`phase 3 = weeks 11-15`\
`phase 4 = weeks 16+`

The original definitions of these values are the following:

`Phase 1:`

- Commits: no requirements
- Test cases (passed / total): no requirements
- Backlog (product / sprint): no requirements
- Done: no requirements
- Risks (high / total): no requirements
- CPI / SPI: no requirements
- Minimum working hours of an active member: yellow if 0
- Earliest last seen date of an active member: yellow if \> 2 weeks from today, red if \> 3 weeks from today

`Phase 2:`

- Commits: yellow if \< number of weeks from project start, red if 0
- Test cases (passed / total): yellow if total = 0
- Backlog (product / sprint): yellow if product \< 5 or sprint = 0, red if product = 0
- Done: no requirements
- Risks (high / total): yellow if total \< 5, red if total = 0
- CPI / SPI: yellow if either \< 0,5
- Minimum working hours of an active member: yellow if \< 2,5 times number of weeks from project start, red if \< 1 times number of weeks from project start
- Earliest last seen date of an active member: yellow if \> 2 weeks from today, red if \> 3 weeks from today

`Phase 3:`

- Commits: yellow if \< number of weeks from project start, red if \< 5
- Test cases (passed / total): yellow if total \< 5, red if total = 0
- Backlog (product / sprint): yellow if sprint = 0, red if product \< 5
- Done: yellow if = 0
- Risks (high / total): yellow if total \< 5 or high \> 2, red if total = 0 or high = total
- Minimum working hours of an active member: yellow if \< 5 times number of weeks from project start, red if \< 3 times number of weeks from project start
- Earliest last seen date of an active member: yellow if \> 2 weeks from today, red if \> 3 weeks from today

`Phase 4:`

- Commits: yellow if \< number of weeks from project start, red if \< 5
- Test cases (passed / total): yellow if passed = 0, red if total \< 5
- Backlog (product / sprint): yellow if sprint = 0, red if product \< 5
- Done: yellow if product backlog \* 0,5 \> done
- Risks (high / total): yellow if total \< 5 or high \> 2, red if total = 0 or high = total
- Minimum working hours of an active member: yellow if \< 5 times number of weeks from project start, red if \< 3 times number of weeks from project start
- Earliest last seen date of an active member: yellow if \> 2 weeks from today, red if \> 3 weeks from today

---

**Working hour & work type descriptions**\
Working hours and work type descriptions are configured in the database table ‘worktypes’. Admin can edit these in MMT from page /worktypes. Changing the description value of a work type changes the working hour description in the program globally, except in few charts where they are hardcoded. These can be changed in src\\Controller\\ChartsController.php. Note that adding new work types requires additional changes to code.

# **Structure of MMT**

MMT uses the [MVC model](https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller), in which files are separated into Model, View, and Controller components. MMT is a combination of PHP, HTML, and JavaScript languages.

**Model** (or the data structure and logic) files are contained in the src\\Model folder. For example, ChartsTable.php in src\\Model\\Table creates the queries to fetch the chart-related data for each chart.

**View** (representation of data, such as charts or tables) files are contained in multiple folders. In the case of charts, the chart creation is handled in webroot\\js\\chartview.js.

**Controller** takes input and coordinates the data handling between the Model and View components. In src\\Controller\\ChartsController.php, the controller takes input from the MMT site, gathers the data from Model (ChartsTable.php), and tells View (chartview.js) to create the charts using the highcharts.js library.

The user interface (or the website) is contained in files under the templates\\ folder. When a project is selected, the "Charts" page on MMT is created in the file templates\\Charts\\index.php. The **main page** of MMT is located in templates\\layout\\default.php.
