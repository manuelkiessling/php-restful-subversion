# PHPMergeHelper

** Eine Sammlung von PHP Klassen die das Management von SVN Merges vereinfachen. **

## Wozu sind die PHPMergeHelper gut?

Die PHPMergeHelper sind eine Sammlung von PHP Klassen die es erleichtern, maßgeschneiderte Werkzeuge
zum Merge- und Releasemanagement innerhalb eines SVN Repositories zu bauen.

Diese maßgeschneiderten Werkzeuge wiederum sind meiner Meinung nach notwendig, aus zwei Gründen:

Erstens: Die verfügbaren Subversion Client-Tools sind zwar sehr hilfreich beim Mergen von ganzen
Branches oder einzelnen Commits, bieten jedoch keinerlei Unterstützung bei den übergeordneten
Aufgaben des Releasens, wie zum Beispiel beim Zusammensuchen aller Änderungen die zu einem Projekt
gehören, oder dem Auseinandersortieren von den Commits eines Projekts, die bereits released wurden
und denen, die in den nächsten Release aufgenommen werden sollen.

Zweitens: Jedes Team von Entwicklern betreibt Releasemanagement anders. Daher wird es auch schwierig
sein, eine one-size-fits-all Lösung zu bauen, was bedeutet dass jedes Team sein eigenes
maßgeschneidertes Tool bauen können sollte.

Da es sich lediglich um eine Klassensammlung handelt, bieten die PHPMergeHelper keine Sofortlösung!

Sie ermöglichen aber mit vergleichsweise geringem Aufwand, ein eigenes Tool zu bauen, mit dessen
Hilfe der Releasemanager eines Softwareentwicklerteams viele aufwendige und manuelle SVN Handgriffe
stark vereinfachen und ggf. automatisieren kann.

Die PHPMergeHelper implementieren unter anderen folgende Use Cases:

- "Liefere mir alle Revisionsnummern, deren Commit Message eine bestimmte Zeichenkette enthält"

- "Liefere mir auf Basis eines vollständigen Pfads das Wurzelverzeichnis des Branches, zu dem dieser
   Pfad gehört"

- "Liefere mir das Wurzelverzeichnis des Branches, auf dem ein bestimmer Commit stattgefunden hat"

- "Sage mir, ob eine Liste von Commits alle im gleichen Branch stattgefunden haben"

- "Liefere mir eine Liste aller Datei- und Verzeichnispfade, die durch einen gegebenen Commit ver-
   ändert wurden"

Verknüpft man diese Use Cases miteinander, kann man bspw. ein Tool bauen, das dem Releasemanager
nach Eingabe einer Liste von Ticketnummern (aus dem vom Team verwendeten Bugtracker, und natürlich
nur, wenn alle Entwickler brav die Tickenummern in die Commit Message schreiben) alle Commits zu
diesen Tickets in chronologischer Reihenfolge ausgibt, die notwendigen SVN Merge Kommandozeilen an-
zeigt, und darauf hinweist, wenn zu einem Ticket bereits Merges in einem alten Release gefunden
wurden.
