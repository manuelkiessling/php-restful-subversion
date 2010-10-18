<?php

$asSql = array();

$asSql[] = 'DROP TABLE IF EXISTS revisions;';
$asSql[] = 'CREATE TABLE revisions(revision INTEGER PRIMARY KEY NOT NULL, author TEXT(64), date TEXT(10), time TEXT(8));';

$asSql[] = 'CREATE INDEX author ON revisions(author);';
$asSql[] = 'CREATE INDEX datetime ON revisions(date, time);';

$asSql[] = 'DROP TABLE IF EXISTS paths;';
$asSql[] = 'CREATE TABLE paths (id INTEGER PRIMARY KEY, revision INTEGER NOT NULL, type TEXT(2), path TEXT(512), revertedpath TEXT(512), FOREIGN KEY(revision) REFERENCES revisions(revision));';

$asSql[] = 'CREATE INDEX path ON paths(path);';
$asSql[] = 'CREATE INDEX revertedpath ON paths(revertedpath);';
