<?php
// Database configuration extracted to a single file so the connection
// can be pointed at a centralized server without editing every script.
// Update these values to match the credentials/hostname of your
// central database server.

return [
    // hostname or IP address of the central database
    'host' => '127.0.0.1',    // e.g. 'central-db.yourorg.local'

    // database user and password with appropriate privileges
    'user' => 'root',
    'pass' => '',

    // the shared database name used by this application
    'db'   => 'class',
];
