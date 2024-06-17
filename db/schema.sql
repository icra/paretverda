/*
  inicialitzar base de dades des del terminal:
  cat schema.sql | sqlite3 db.sqlite
*/
CREATE TABLE IF NOT EXISTS 'sistemes' (
  'id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  'json' TEXT DEFAULT '{}'
);

INSERT INTO sistemes (id) VALUES (0);
