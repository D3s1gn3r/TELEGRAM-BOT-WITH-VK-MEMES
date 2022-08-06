<?php

	// db connection
    R::setup('mysql:host=' . DB_HOST . ':' . DB_PORT . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    if(!R::testConnection()){
        die('Unable to connect to database');
    }