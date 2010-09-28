DROP TABLE revisions;
CREATE TABLE revisions(revision INTEGER PRIMARY KEY NOT NULL, author TEXT(64), date TEXT(10), time TEXT(8));

CREATE INDEX author ON revisions(author);
CREATE INDEX datetime ON revisions(date, time);

DROP TABLE paths;
CREATE TABLE paths (id INTEGER PRIMARY KEY, revision INTEGER NOT NULL, type TEXT(2), path TEXT(512), revertedpath TEXT(512), FOREIGN KEY(revision) REFERENCES revisions(revision));

CREATE INDEX path ON paths(path);
CREATE INDEX revertedpath ON paths(revertedpath);
