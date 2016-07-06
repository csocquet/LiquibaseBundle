# LiquibaseBundle

## Configuration

Here is the full configuration of this bundle, but only database `type`, `jdbc_dsn` are required

    #app/config/config.yml

    cs_liquibase:
        liquibase_jar_path: "/path/to/liquibase.jar"
        changelog_path:     "/path/to/changelog/file.xml"
        database:
            type:     "mysql"
            jdbc_dsn: "jdbc:mysql:hostname:3306/db_name"
            user:     "db_user"
            password: "db_password"
        drivers:
            - { db_type: "mysql", class: "com.db.jdbc.driver", path: "/path/to/jdbc/connector.jar"}
            # ...

Drivers section can be configured like above as a collection of drivers, or as a key/value collection where the key is `db_type` as below.

    cs_liquibase:
        # ...
        drivers:
            mysql:
                class: "com.db.jdbc.driver"
                path:  "/path/to/jdbc/connector.jar"