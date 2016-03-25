<?php
//Changed Port number from 5433 to 5432
$pgConnection = pg_connect('host=localhost port=5432 dbname=familysearch user=familysearch password=familysearch');
pg_query($pgConnection, "INSERT INTO fsplacesiso (fsid, name, lat, lng, iso) VALUES(602108, 'Duluth, Minnesota', 46.786, -92.119, 'US-MN');");

?>