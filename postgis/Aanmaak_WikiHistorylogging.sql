---NODES---
--history table
CREATE TABLE nodes_logging (
	hid SERIAL PRIMARY KEY,
	id INTEGER,
	name TEXT NOT NULL, 
	description TEXT, 
	descr tsvector,
	action VARCHAR(1),
	action_time TIMESTAMP,
	action_by VARCHAR(32)
);

--history table trigger function
CREATE OR REPLACE FUNCTION nodes_logger() RETURNS trigger AS
$$
	BEGIN
		IF (TG_OP = 'INSERT') THEN
			INSERT INTO nodes_logging (id, name, description,descr,action, action_time, action_by)
			VALUES (NEW.id, NEW.name, NEW.description, NEW.descr, 'I', current_timestamp, current_user);
			RETURN NEW;
		ELSIF (TG_OP = 'UPDATE') THEN
			INSERT INTO nodes_logging (id, name, description, descr, action, action_time, action_by)
			VALUES (NEW.id, NEW.name, NEW.description, NEW.descr, 'U', current_timestamp, current_user);
			RETURN NEW;
		ELSIF (TG_OP = 'DELETE') THEN
			INSERT INTO nodes_logging (id, name, description, descr, action, action_time, action_by)
			VALUES (OLD.id, OLD.name, OLD.description, OLD.descr, 'D', current_timestamp, current_user);
			RETURN OLD;
		END IF;
	END;
$$
LANGUAGE plpgsql;

---IMPORT CURRENT STATE OF TABLE IN HISTORY TABLE (first insert test data)
INSERT INTO nodes_logging(id, name, description,descr,action, action_time,action_by)
SELECT id, name, description, descr, 'I', now(), current_user
FROM nodes;

--bind trigger to history table
CREATE TRIGGER nodes_logging_trigger
AFTER INSERT OR UPDATE OR DELETE ON nodes
FOR EACH ROW EXECUTE PROCEDURE nodes_logger();

---STATEMENTS---

--history table
CREATE TABLE statements_logging (
	hid SERIAL PRIMARY KEY,
	id INTEGER,
	startID INTEGER, 
	propertyName INTEGER, 
	value TEXT,
	qualifier INTEGER,
	rank RANKS,
	action VARCHAR(1),
	action_time TIMESTAMP,
	action_by VARCHAR(32)
);

--history table trigger function
CREATE OR REPLACE FUNCTION statements_logger() RETURNS trigger AS
$$
	BEGIN
		IF (TG_OP = 'INSERT') THEN
			INSERT INTO statements_logging (id, startID, propertyName, value, qualifier, rank, action, action_time, action_by)
			VALUES (NEW.id, NEW.startID, NEW.propertyName, NEW.value, NEW.qualifier, NEW.rank, 'I', current_timestamp, current_user);
			RETURN NEW;
		ELSIF (TG_OP = 'UPDATE') THEN
			INSERT INTO statements_logging (id, startID, propertyName, value, qualifier, rank, action, action_time, action_by)
			VALUES (NEW.id, NEW.startID, NEW.propertyName, NEW.value, NEW.qualifier, NEW.rank, 'U', current_timestamp, current_user);
			RETURN NEW;
		ELSIF (TG_OP = 'DELETE') THEN
			INSERT INTO statements_logging (id, startID, propertyName, value, qualifier, rank, action, action_time, action_by)
			VALUES (OLD.id, OLD.startID, OLD.propertyName, OLD.value, OLD.qualifier, OLD.rank, 'D', current_timestamp, current_user);
			RETURN OLD;
		END IF;
	END;
$$
LANGUAGE plpgsql;

---IMPORT CURRENT STATE OF TABLE IN HISTORY TABLE (first insert test data)
INSERT INTO statements_logging (id, startID, propertyName, value, qualifier, rank, action, action_time, action_by)
SELECT id, startID, propertyName, value, qualifier, rank, 'I', now(), current_user
FROM statements;

--bind trigger to history table
CREATE TRIGGER statements_logging_trigger
AFTER INSERT OR UPDATE OR DELETE ON statements
FOR EACH ROW EXECUTE PROCEDURE statements_logger();

---PROPERTIES---
--history table
CREATE TABLE properties_logging (
	hid SERIAL PRIMARY KEY,
	id INTEGER,
	name TEXT, 
	description TEXT, 
	datatype TEXT,
	descr tsvector,
	action VARCHAR(1),
	action_time TIMESTAMP,
	action_by VARCHAR(32)
);

--history table trigger function
CREATE OR REPLACE FUNCTION properties_logger() RETURNS trigger AS
$$
	BEGIN
		IF (TG_OP = 'INSERT') THEN
			INSERT INTO properties_logging (id, name, description, datatype, descr, action, action_time, action_by)
			VALUES (NEW.id, NEW.name, NEW.description, NEW.datatype, NEW.descr, 'I', current_timestamp, current_user);
			RETURN NEW;
		ELSIF (TG_OP = 'UPDATE') THEN
			INSERT INTO properties_logging (id, name, description, datatype, descr, action, action_time, action_by)
			VALUES (NEW.id, NEW.name, NEW.description, NEW.datatype, NEW.descr, 'U', current_timestamp, current_user);
			RETURN NEW;
		ELSIF (TG_OP = 'DELETE') THEN
			INSERT INTO properties_logging (id, name, description, datatype, descr, action, action_time, action_by)
			VALUES (OLD.id, OLD.name, OLD.description, OLD.datatype, OLD.descr, 'D', current_timestamp, current_user);
			RETURN OLD;
		END IF;
	END;
$$
LANGUAGE plpgsql;

---IMPORT CURRENT STATE OF TABLE IN HISTORY TABLE (first insert test data)
INSERT INTO properties_logging (id, name, description, datatype, descr, action, action_time, action_by)
SELECT id, name, description, datatype, descr, 'I', now(), current_user
FROM properties;

--bind trigger to history table
CREATE TRIGGER properties_logging_trigger
AFTER INSERT OR UPDATE OR DELETE ON properties
FOR EACH ROW EXECUTE PROCEDURE properties_logger();