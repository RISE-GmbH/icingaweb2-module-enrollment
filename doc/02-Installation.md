# Installation <a id="module-enrollment-installation"></a>

## Requirements <a id="module-enrollment-installation-requirements"></a>

* Icinga Web 2 (&gt;= 2.12.1)
* PHP (&gt;= 7.3)


## Installation from .tar.gz <a id="module-enrollment-installation-manual"></a>

Download the latest version and extract it to a folder named `enrollment`
in one of your Icinga Web 2 module path directories.

## Enable the newly installed module <a id="module-enrollment-installation-enable"></a>

Enable the `enrollment` module either on the CLI by running

```sh
icingacli module enable enrollment
```

Or go to your Icinga Web 2 frontend, choose `Configuration` -&gt; `Modules`, chose the `enrollment` module and `enable` it.

It might afterward be necessary to refresh your web browser to be sure that
newly provided styling is loaded.

## Setting up the Database

### Setting up a MySQL or MariaDB Database

The module needs a MySQL/MariaDB database with the schema that's provided in the `/usr/share/icingaweb2/modules/enrollment/schema/mysql.schema.sql` file.

You can use the following sample command for creating the MySQL/MariaDB database. Please change the password:

```
CREATE DATABASE enrollment;
GRANT CREATE, SELECT, INSERT, UPDATE, DELETE, DROP, ALTER, CREATE VIEW, INDEX, EXECUTE ON enrollment.* TO enrollment@localhost IDENTIFIED BY 'secret';
```

After, you can import the schema using the following command:

```
mysql -p -u root enrollment < /usr/share/icingaweb2/modules/enrollment/schema/mysql.schema.sql
```

Please create a database resource as usual and choose this resource in the enrollment module backend settings.

## Config via CLI

### Select a database resource

You can run the following command choose a resource:

```
sudo -u www-data icingacli enrollment set resource --name NAMEOFYOURDATABASERESOURCE
```