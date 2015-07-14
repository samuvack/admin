CREATE EXTENSION postgis;

--tables
DROP TABLE IF EXISTS nodes;
CREATE TABLE nodes (id serial primary key, name text NOT NULL, description text, descr tsvector);
CREATE TRIGGER tsvectorupdatenode BEFORE INSERT OR UPDATE
	ON nodes FOR EACH ROW EXECUTE PROCEDURE
	tsvector_update_trigger(descr, 'pg_catalog.english', description, name);

DROP TABLE IF EXISTS statements;
CREATE TYPE ranks AS ENUM ('normal', 'preferred', 'deprecated');
CREATE TABLE statements (id serial primary key, startID integer, propertyName integer, value text, qualifier integer, rank ranks);

DROP TABLE IF EXISTS properties;
CREATE TABLE properties (id serial primary key, name text NOT NULL, description text, datatype text, descr tsvector);
CREATE TRIGGER tsvectorupdateprop BEFORE INSERT OR UPDATE
	ON properties FOR EACH ROW EXECUTE PROCEDURE
	tsvector_update_trigger(descr, 'pg_catalog.english', description, name);

DROP TABLE IF EXISTS geometries_point;
CREATE TABLE geometries_point (id serial primary key);
ALTER TABLE geometries_point ADD COLUMN geom geometry(Point,4326);