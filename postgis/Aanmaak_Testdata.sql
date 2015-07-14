---Tabellen leegmaken---
TRUNCATE nodes, nodes_logging, properties, properties_logging, statements, statements_logging, geometries_point;

---TEST DATA TOEVOEGEN (concept)---
INSERT INTO nodes (id, name, description) VALUES (default, 'node1', 'Description number 1. This is a test.');
INSERT INTO nodes (name, description) VALUES ('node2', 'Description 2 on node 1');
INSERT INTO nodes (name, description) VALUES ('node3', 'Description on 3');
INSERT INTO statements (startID, propertyName, value) VALUES ('2', '1', '3');
INSERT INTO statements (startID, propertyName, value) VALUES ('2', '3', 'geen');
INSERT INTO statements (startID, propertyName, value) VALUES ('3', '2', '2');
INSERT INTO statements (startID, propertyName, value) VALUES ('1', '2', '2');
INSERT INTO statements (startID, propertyName, value) VALUES ('1', '4', '1');
INSERT INTO properties (name, datatype) values ('in','node');
INSERT INTO properties (name, datatype) values ('contains', 'node');
INSERT INTO properties (name, datatype) values ('has property', 'text');
INSERT INTO properties (name, datatype) VALUES ('has geometry', 'geometry');
INSERT INTO geometries_point (geom) VALUES (ST_GeomFromText('POINT(2 10)', 4326));

---TEST DATA TOEVOEGEN (echt ~ Project Ronse De Stadstuin-Solva)---
--NODES
INSERT INTO nodes (id, name, description) VALUES (1,'Ronse De Stadstuin', 'Archeologische site Ronse De Stadstuin, gelegen te Ronse nabij de Grote Markt. De terreinen zijn omloten door Elzelestraat, Bredestraat, O. Delghuststraat, F. Devosstraat en A. Massezstraat');
INSERT INTO nodes (id, name, description) VALUES (2, 'I-A-1--', 'Spoor met nummer I-A-1-- opgegevraven in zone 1 op grondplan A te Ronse De Stadstuin.');
INSERT INTO nodes (id, name, description) VALUES (3, 'I-A-2--', 'Spoor met nummer I-A-2-- opgegraven in zone 1 op grondplan A te Ronse De Stadstuin.');
INSERT INTO nodes (id, name, description) VALUES (4, 'I-A-3--', 'Spoor met nummer I-A-3-- opgegraven in zone 1 op grondplan A te Ronse De Stadstuin.');
INSERT INTO nodes (id, name, description) VALUES (5, 'I-A-1', 'Structuur met nummer I-A-1 opgegraven in zone 1 op grondplan A te Ronse De Stadstuin.');
INSERT INTO nodes (id, name, description) VALUES (6, 'I-A-1', 'Context met nummer I-A-1 opgegraven in zone 1 op grondplan A te Ronse De Stadstuin.');
INSERT INTO nodes (id, name, description) VALUES (7, 'I-A-2', 'Context met nummer I-A-2 opgegraven in zone 1 op grondplan A te Ronse De Stadstuin.');
INSERT INTO nodes (id, name, description) VALUES (8, 'I-A-3', 'Context met nummer I-A-3 opgegraven in zone 1 op grondplan A te Ronse De Stadstuin.');
INSERT INTO nodes (id, name, description) VALUES (9, 'I-A-13', 'Spoor met nummer I-A-13 opgegraven in zone 1 op grondplan A te Ronse De Stadstuin.');
INSERT INTO nodes (id, name, description) VALUES (10, 'I-A-13', 'Context met nummer I-A-3 opgegraven in zone 1 op grondplan A te Ronse De Stadstuin.');
INSERT INTO nodes (id, name, description) VALUES (11, 'I-A-13', 'Structuur met nummer I-A-3 opgegraven in zone 1 op grondplan A te Ronse De Stadstuin.');
INSERT INTO nodes (id, name, description) VALUES (12, 'Zone I', 'Zone I opgegraven te Ronse De Stadstuin.');

--STATEMENTS
INSERT INTO statements (startID, propertyName, value) VALUES ('1','1','Site');
INSERT INTO statements (startID, propertyName, value) VALUES ('1','2','Excavation');

INSERT INTO statements (startID, propertyName, value) VALUES ('2','1','Spoor');
INSERT INTO statements (startID, propertyName, value) VALUES ('3','1','Spoor');
INSERT INTO statements (startID, propertyName, value) VALUES ('4','1','Spoor');
INSERT INTO statements (startID, propertyName, value) VALUES ('5','1','Structuur');
INSERT INTO statements (startID, propertyName, value) VALUES ('6','1','Context');
INSERT INTO statements (startID, propertyName, value) VALUES ('7','1','Context');
INSERT INTO statements (startID, propertyName, value) VALUES ('8','1','Context');
INSERT INTO statements (startID, propertyName, value) VALUES ('9','1','Spoor');
INSERT INTO statements (startID, propertyName, value) VALUES ('10','1','Context');
INSERT INTO statements (startID, propertyName, value) VALUES ('11','1','Structuur');

INSERT INTO statements (startID, propertyName, value) VALUES ('2','5','6');
INSERT INTO statements (startID, propertyName, value) VALUES ('3','5','7');
INSERT INTO statements (startID, propertyName, value) VALUES ('4','5','8');
INSERT INTO statements (startID, propertyName, value) VALUES ('5','5','12');
INSERT INTO statements (startID, propertyName, value) VALUES ('6','5','5');
INSERT INTO statements (startID, propertyName, value) VALUES ('7','5','5');
INSERT INTO statements (startID, propertyName, value) VALUES ('8','5','5');
INSERT INTO statements (startID, propertyName, value) VALUES ('9','5','10');
INSERT INTO statements (startID, propertyName, value) VALUES ('10','5','11');
INSERT INTO statements (startID, propertyName, value) VALUES ('11','5','12');
INSERT INTO statements (startID, propertyName, value) VALUES ('12','5','1');

INSERT INTO statements (startID, propertyName, value) VALUES ('2','4','greppel');
INSERT INTO statements (startID, propertyName, value) VALUES ('3','4','greppel');
INSERT INTO statements (startID, propertyName, value) VALUES ('4','4','greppel');
INSERT INTO statements (startID, propertyName, value) VALUES ('5','4','weg');
INSERT INTO statements (startID, propertyName, value) VALUES ('6','4','greppel');
INSERT INTO statements (startID, propertyName, value) VALUES ('7','4','greppel');
INSERT INTO statements (startID, propertyName, value) VALUES ('8','4','greppel');
INSERT INTO statements (startID, propertyName, value) VALUES ('9','4','paalspoor');
INSERT INTO statements (startID, propertyName, value) VALUES ('10','4','paalspoor');
INSERT INTO statements (startID, propertyName, value) VALUES ('11','4','gebouw');


INSERT INTO statements (startID, propertyName, value) VALUES ('5','6','1501/1940');
INSERT INTO statements (startID, propertyName, value) VALUES ('8','6','1501/1800');
INSERT INTO statements (startID, propertyName, value) VALUES ('10','6','-0049/0080');
INSERT INTO statements (startID, propertyName, value) VALUES ('11','6','-0159/0080');

INSERT INTO statements (startID, propertyName, value) VALUES ('2','3','1');
INSERT INTO statements (startID, propertyName, value) VALUES ('3','3','2');
INSERT INTO statements (startID, propertyName, value) VALUES ('4','3','3');
INSERT INTO statements (startID, propertyName, value) VALUES ('9','3','4');

--PROPERTIES
INSERT INTO properties (id, name, datatype) VALUES ('1','is of type', 'text');
INSERT INTO properties (id, name, datatype) VALUES ('2','has property', 'text');
INSERT INTO properties (id, name, datatype) VALUES ('3', 'has geometry', 'geometry');
INSERT INTO properties (id, name, datatype) VALUES ('4', 'has interpretation', 'text');
INSERT INTO properties (id, name, datatype) VALUES ('5', 'is part of', 'node');
INSERT INTO properties (id, name, datatype) VALUES ('6', 'has dating', 'interval');

--GEOMETRIES
SELECT UpdateGeometrySRID('geometries_point', 'geom', 31370);
INSERT INTO geometries_point (id,geom) VALUES ('1', ST_GeomFromText('POINT(96168.50 160099.71)', 31370));
INSERT INTO geometries_point (id,geom) VALUES ('2', ST_GeomFromText('POINT(96171.85 160115.32)', 31370));
INSERT INTO geometries_point (id,geom) VALUES ('3', ST_GeomFromText('POINT(96165.60 160107.65)', 31370));
INSERT INTO geometries_point (id,geom) VALUES ('4', ST_GeomFromText('POINT(96164.01 160101.70)', 31370));