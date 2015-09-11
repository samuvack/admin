--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


--
-- Name: postgis; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS postgis WITH SCHEMA public;


--
-- Name: EXTENSION postgis; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION postgis IS 'PostGIS geometry, geography, and raster spatial types and functions';


SET search_path = public, pg_catalog;

--
-- Name: ranks; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE ranks AS ENUM (
    'normal',
    'preferred',
    'deprecated'
);


ALTER TYPE public.ranks OWNER TO postgres;

--
-- Name: nodes_delete(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION nodes_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
	BEGIN
		UPDATE nodes_history
		SET deleted = current_timestamp, deleted_by = current_user
		WHERE deleted IS NULL and id = OLD.id;
		RETURN NULL;
	END;
$$;


ALTER FUNCTION public.nodes_delete() OWNER TO postgres;

--
-- Name: nodes_insert(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION nodes_insert() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
	BEGIN
		INSERT INTO nodes_history (id, name, description,descr,created,created_by)
		VALUES (NEW.id, NEW.name, NEW.description, NEW.descr, current_timestamp, current_user);
		RETURN NEW;
	END;
$$;


ALTER FUNCTION public.nodes_insert() OWNER TO postgres;

--
-- Name: nodes_logger(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION nodes_logger() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
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
$$;


ALTER FUNCTION public.nodes_logger() OWNER TO postgres;

--
-- Name: nodes_update(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION nodes_update() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
	BEGIN
		UPDATE nodes_history
		SET deleted = current_timestamp, deleted_by = current_user
		WHERE deleted IS NULL and id = OLD.id;
		INSERT INTO nodes_history (id, name, description, descr, created, created_by)
		VALUES (NEW.id, NEW.name, NEW.description, NEW.descr, current_timestamp, current_user);
		RETURN NEW;
	END;
$$;


ALTER FUNCTION public.nodes_update() OWNER TO postgres;

--
-- Name: properties_delete(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION properties_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
	BEGIN
		UPDATE properties_history
		SET deleted = current_timestamp, deleted_by = current_user
		WHERE deleted IS NULL and id = OLD.id;
		RETURN NULL;
	END;
$$;


ALTER FUNCTION public.properties_delete() OWNER TO postgres;

--
-- Name: properties_insert(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION properties_insert() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
	BEGIN
		INSERT INTO properties_history (id, name, description,datatype, descr,created,created_by)
		VALUES (NEW.id, NEW.name, NEW.description, NEW.datatype, NEW.descr, current_timestamp, current_user);
		RETURN NEW;
	END;
$$;


ALTER FUNCTION public.properties_insert() OWNER TO postgres;

--
-- Name: properties_logger(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION properties_logger() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
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
$$;


ALTER FUNCTION public.properties_logger() OWNER TO postgres;

--
-- Name: properties_update(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION properties_update() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
	BEGIN
		UPDATE properties_history
		SET deleted = current_timestamp, deleted_by = current_user
		WHERE deleted IS NULL and id = OLD.id;

		INSERT INTO properties_history (id, name, description,datatype, descr,created,created_by)
		VALUES (NEW.id, NEW.name, NEW.description, NEW.datatype, NEW.descr, current_timestamp, current_user);
		RETURN NEW;
	END;
$$;


ALTER FUNCTION public.properties_update() OWNER TO postgres;

--
-- Name: statements_delete(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION statements_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
	BEGIN
		UPDATE statements_history
		SET deleted = current_timestamp, deleted_by = current_user
		WHERE deleted IS NULL and id = OLD.id;
		RETURN NULL;
	END;
$$;


ALTER FUNCTION public.statements_delete() OWNER TO postgres;

--
-- Name: statements_insert(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION statements_insert() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
	BEGIN
		INSERT INTO statements_history (id, startID, propertyName, value, qualifier, rank,created,created_by)
		VALUES (NEW.id, NEW.startID, NEW.propertyName, NEW.value, NEW.qualifier, NEW.rank, current_timestamp, current_user);
		RETURN NEW;
	END;
$$;


ALTER FUNCTION public.statements_insert() OWNER TO postgres;

--
-- Name: statements_logger(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION statements_logger() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
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
$$;


ALTER FUNCTION public.statements_logger() OWNER TO postgres;

--
-- Name: statements_update(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION statements_update() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
	BEGIN
		UPDATE statements_history
		SET deleted = current_timestamp, deleted_by = current_user
		WHERE deleted IS NULL and id = OLD.id;

		INSERT INTO statements_history (id, startID, propertyName, value, qualifier, rank,created,created_by)
		VALUES (NEW.id, NEW.startID, NEW.propertyName, NEW.value, NEW.qualifier, NEW.rank, current_timestamp, current_user);
		RETURN NEW;
	END;
$$;


ALTER FUNCTION public.statements_update() OWNER TO postgres;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: geometries; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE geometries (
    id integer NOT NULL,
    geom geometry(Geometry,31370)
);


ALTER TABLE public.geometries OWNER TO postgres;

--
-- Name: geometries_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE geometries_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.geometries_id_seq OWNER TO postgres;

--
-- Name: geometries_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE geometries_id_seq OWNED BY geometries.id;


--
-- Name: nodes; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE nodes (
    id integer NOT NULL,
    name text NOT NULL,
    description text,
    descr tsvector
);


ALTER TABLE public.nodes OWNER TO postgres;

--
-- Name: nodes_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE nodes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.nodes_id_seq OWNER TO postgres;

--
-- Name: nodes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE nodes_id_seq OWNED BY nodes.id;


--
-- Name: nodes_log; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE nodes_log (
    id integer NOT NULL,
    node_id integer,
    name text NOT NULL,
    description text,
    descr tsvector,
    action character varying(1),
    action_by integer NOT NULL,
    action_time timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.nodes_log OWNER TO postgres;

--
-- Name: nodes_log_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE nodes_log_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.nodes_log_id_seq OWNER TO postgres;

--
-- Name: nodes_log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE nodes_log_id_seq OWNED BY nodes_log.id;


--
-- Name: phinxlog; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE phinxlog (
    version bigint NOT NULL,
    start_time timestamp without time zone NOT NULL,
    end_time timestamp without time zone NOT NULL
);


ALTER TABLE public.phinxlog OWNER TO postgres;

--
-- Name: properties; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE properties (
    id integer NOT NULL,
    name text NOT NULL,
    description text,
    datatype text,
    descr tsvector
);


ALTER TABLE public.properties OWNER TO postgres;

--
-- Name: properties_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE properties_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.properties_id_seq OWNER TO postgres;

--
-- Name: properties_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE properties_id_seq OWNED BY properties.id;


--
-- Name: properties_log; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE properties_log (
    id integer NOT NULL,
    property_id integer,
    name text,
    description text,
    datatype text,
    descr tsvector,
    action character varying(1),
    action_by integer NOT NULL,
    action_time timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.properties_log OWNER TO postgres;

--
-- Name: properties_log_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE properties_log_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.properties_log_id_seq OWNER TO postgres;

--
-- Name: properties_log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE properties_log_id_seq OWNED BY properties_log.id;


--
-- Name: relations; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE relations (
    id integer NOT NULL,
    property integer,
    value text,
    qualifier integer,
    rank ranks,
    nodevalue integer,
    geometryvalue integer,
    startnode integer NOT NULL
);


ALTER TABLE public.relations OWNER TO postgres;

--
-- Name: relations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE relations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.relations_id_seq OWNER TO postgres;

--
-- Name: relations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE relations_id_seq OWNED BY relations.id;


--
-- Name: relations_log; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE relations_log (
    id integer NOT NULL,
    relation_id integer,
    startid integer,
    property_id integer,
    value text,
    qualifier integer,
    rank ranks,
    action character varying(1),
    action_by integer NOT NULL,
    action_time timestamp without time zone DEFAULT now() NOT NULL,
    nodevalue integer,
    geometryvalue integer
);


ALTER TABLE public.relations_log OWNER TO postgres;

--
-- Name: relations_log_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE relations_log_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.relations_log_id_seq OWNER TO postgres;

--
-- Name: relations_log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE relations_log_id_seq OWNED BY relations_log.id;


--
-- Name: secondary_relations; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE secondary_relations (
    id integer NOT NULL,
    property integer NOT NULL,
    value character varying(255),
    qualifier integer,
    nodevalue integer,
    geometryvalue integer,
    parent_relation integer NOT NULL,
    rank ranks
);


ALTER TABLE public.secondary_relations OWNER TO postgres;

--
-- Name: secondary_relations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE secondary_relations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.secondary_relations_id_seq OWNER TO postgres;

--
-- Name: secondary_relations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE secondary_relations_id_seq OWNED BY secondary_relations.id;


--
-- Name: user_custom_fields; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE user_custom_fields (
    user_id integer NOT NULL,
    attribute character varying(50) NOT NULL,
    value character varying(255)
);


ALTER TABLE public.user_custom_fields OWNER TO postgres;

--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE users (
    id integer NOT NULL,
    email character varying(100) NOT NULL,
    password character varying(255) NOT NULL,
    salt character varying(255) NOT NULL,
    roles character varying(255) NOT NULL,
    time_created integer DEFAULT 0 NOT NULL,
    username character varying(100),
    is_enabled boolean DEFAULT true NOT NULL,
    confirmation_token character varying(100),
    time_password_reset_requested integer,
    name character varying(100) NOT NULL
);


ALTER TABLE public.users OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY geometries ALTER COLUMN id SET DEFAULT nextval('geometries_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY nodes ALTER COLUMN id SET DEFAULT nextval('nodes_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY nodes_log ALTER COLUMN id SET DEFAULT nextval('nodes_log_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY properties ALTER COLUMN id SET DEFAULT nextval('properties_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY properties_log ALTER COLUMN id SET DEFAULT nextval('properties_log_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY relations ALTER COLUMN id SET DEFAULT nextval('relations_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY relations_log ALTER COLUMN id SET DEFAULT nextval('relations_log_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY secondary_relations ALTER COLUMN id SET DEFAULT nextval('secondary_relations_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- Data for Name: geometries; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY geometries (id, geom) FROM stdin;
1	01010000208A7A000000000000887AF740E17A14AE1D8B0341
2	01010000208A7A00009A999999BD7AF740F6285C8F9A8B0341
3	01010000208A7A00009A999999597AF740333333335D8B0341
4	01010000208A7A00008FC2F528407AF7409A9999992D8B0341
\.


--
-- Name: geometries_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('geometries_id_seq', 4, true);


--
-- Data for Name: nodes; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY nodes (id, name, description, descr) FROM stdin;
2	I-A-1--	Spoor met nummer I-A-1-- opgegevraven in zone 1 op grondplan A te Ronse De Stadstuin.	'1':7,11,22 'de':17 'grondplan':13 'i-a':4,19 'met':2 'nummer':3 'op':12 'opgegevraven':8 'rons':16 'spoor':1 'stadstuin':18 'te':15 'zone':10
3	I-A-2--	Spoor met nummer I-A-2-- opgegraven in zone 1 op grondplan A te Ronse De Stadstuin.	'1':11 '2':7,22 'de':17 'grondplan':13 'i-a':4,19 'met':2 'nummer':3 'op':12 'opgegraven':8 'rons':16 'spoor':1 'stadstuin':18 'te':15 'zone':10
4	I-A-3--	Spoor met nummer I-A-3-- opgegraven in zone 1 op grondplan A te Ronse De Stadstuin.	'1':11 '3':7,22 'de':17 'grondplan':13 'i-a':4,19 'met':2 'nummer':3 'op':12 'opgegraven':8 'rons':16 'spoor':1 'stadstuin':18 'te':15 'zone':10
5	I-A-1	Structuur met nummer I-A-1 opgegraven in zone 1 op grondplan A te Ronse De Stadstuin.	'1':7,11,22 'de':17 'grondplan':13 'i-a':4,19 'met':2 'nummer':3 'op':12 'opgegraven':8 'rons':16 'stadstuin':18 'structuur':1 'te':15 'zone':10
6	I-A-1	Context met nummer I-A-1 opgegraven in zone 1 op grondplan A te Ronse De Stadstuin.	'1':7,11,22 'context':1 'de':17 'grondplan':13 'i-a':4,19 'met':2 'nummer':3 'op':12 'opgegraven':8 'rons':16 'stadstuin':18 'te':15 'zone':10
7	I-A-2	Context met nummer I-A-2 opgegraven in zone 1 op grondplan A te Ronse De Stadstuin.	'1':11 '2':7,22 'context':1 'de':17 'grondplan':13 'i-a':4,19 'met':2 'nummer':3 'op':12 'opgegraven':8 'rons':16 'stadstuin':18 'te':15 'zone':10
8	I-A-3	Context met nummer I-A-3 opgegraven in zone 1 op grondplan A te Ronse De Stadstuin.	'1':11 '3':7,22 'context':1 'de':17 'grondplan':13 'i-a':4,19 'met':2 'nummer':3 'op':12 'opgegraven':8 'rons':16 'stadstuin':18 'te':15 'zone':10
9	I-A-13	Spoor met nummer I-A-13 opgegraven in zone 1 op grondplan A te Ronse De Stadstuin.	'1':11 '13':7,22 'de':17 'grondplan':13 'i-a':4,19 'met':2 'nummer':3 'op':12 'opgegraven':8 'rons':16 'spoor':1 'stadstuin':18 'te':15 'zone':10
10	I-A-13	Context met nummer I-A-3 opgegraven in zone 1 op grondplan A te Ronse De Stadstuin.	'1':11 '13':22 '3':7 'context':1 'de':17 'grondplan':13 'i-a':4,19 'met':2 'nummer':3 'op':12 'opgegraven':8 'rons':16 'stadstuin':18 'te':15 'zone':10
11	I-A-13	Structuur met nummer I-A-3 opgegraven in zone 1 op grondplan A te Ronse De Stadstuin.	'1':11 '13':22 '3':7 'de':17 'grondplan':13 'i-a':4,19 'met':2 'nummer':3 'op':12 'opgegraven':8 'rons':16 'stadstuin':18 'structuur':1 'te':15 'zone':10
12	Zone I	Zone I opgegraven te Ronse De Stadstuin.	'de':6 'opgegraven':3 'rons':5 'stadstuin':7 'te':4 'zone':1,8
103	I-A-50--	Spoor met naam I-A-50--	'50':7,11 'i-a':4,8 'met':2 'naam':3 'spoor':1
104	I-A-50-CD-1	Spoor met naam I-A-50-CD-1	'-1':9,15 '50':7,13 'cd':8,14 'i-a':4,10 'met':2 'naam':3 'spoor':1
105	I-A-51--	Spoor met naam I-A-51--	'51':7,11 'i-a':4,8 'met':2 'naam':3 'spoor':1
106	I-A-51-AB-1	Spoor met naam I-A-51-AB-1	'-1':9,15 '51':7,13 'ab':8,14 'i-a':4,10 'met':2 'naam':3 'spoor':1
13	I-A-14--	Spoor met nummer I-A-14-- opgegraven in zone I op site Ronse De Stadstuin	'14':7,20 'de':15 'i-a':4,17 'met':2 'nummer':3 'op':12 'opgegraven':8 'rons':14 'site':13 'spoor':1 'stadstuin':16 'zone':10
107	I-A-52--	Spoor met naam I-A-52--	'52':7,11 'i-a':4,8 'met':2 'naam':3 'spoor':1
108	I-A-53--	Spoor met naam I-A-53--	'53':7,11 'i-a':4,8 'met':2 'naam':3 'spoor':1
109	I-A-53-AB-1	Spoor met naam I-A-53-AB-1	'-1':9,15 '53':7,13 'ab':8,14 'i-a':4,10 'met':2 'naam':3 'spoor':1
110	I-A-54--	Spoor met naam I-A-54--	'54':7,11 'i-a':4,8 'met':2 'naam':3 'spoor':1
111	I-A-54-EF-1	Spoor met naam I-A-54-EF-1	'-1':9,15 '54':7,13 'ef':8,14 'i-a':4,10 'met':2 'naam':3 'spoor':1
112	I-A-55--	Spoor met naam I-A-55--	'55':7,11 'i-a':4,8 'met':2 'naam':3 'spoor':1
113	I-A-55-AB-1	Spoor met naam I-A-55-AB-1	'-1':9,15 '55':7,13 'ab':8,14 'i-a':4,10 'met':2 'naam':3 'spoor':1
15	I-A-13	Structuur met nummer I-A-13 te Ronse De Stadstuin	'13':7,15 'de':10 'i-a':4,12 'met':2 'nummer':3 'rons':9 'stadstuin':11 'structuur':1 'te':8
1	Ronse De Stadstuin	Archeologische site Ronse De Stadstuin, gelegen te Ronse nabij de Grote Markt. De terreinen zijn omloten door Elzelestraat, Bredestraat, O. Delghuststraat, F. Devosstraat en A. Massezstraat	'archeologisch':1 'bredestraat':19 'de':4,10,13,28 'delghuststraat':21 'devosstraat':23 'door':17 'elzelestraat':18 'en':24 'f':22 'gelegen':6 'grote':11 'markt':12 'massezstraat':26 'nabij':9 'o':20 'omloten':16 'rons':3,8,27 'site':2 'stadstuin':5,29 'te':7 'terreinen':14 'zijn':15
14	I-A-14	Context met nummer I-A-14 in Zone 1 te Ronse De Stadstuin	'1':10 '14':7,18 'context':1 'de':13 'i-a':4,15 'met':2 'nummer':3 'rons':12 'stadstuin':14 'te':11 'zone':9
32	I-A-4--	Spoor met nummer I-A-13 opgegraven in zone 1 op grondplan A te Ronse De Stadstuin.	'1':11 '13':7 '4':22 'de':17 'grondplan':13 'i-a':4,19 'met':2 'nummer':3 'op':12 'opgegraven':8 'rons':16 'spoor':1 'stadstuin':18 'te':15 'zone':10
33	I-A-7--	Spoor met nummer I-A-7-- opgegraven in zone I op site Ronse De Stadstuin	'7':7,20 'de':15 'i-a':4,17 'met':2 'nummer':3 'op':12 'opgegraven':8 'rons':14 'site':13 'spoor':1 'stadstuin':16 'zone':10
34	I-A-7	Context met nummer I-A-7 opgegraven in zone I op site Ronse De Stadstuin	'7':7,20 'context':1 'de':15 'i-a':4,17 'met':2 'nummer':3 'op':12 'opgegraven':8 'rons':14 'site':13 'stadstuin':16 'zone':10
35	I-A-5--	Spoor met nummer I-A-5-- opgegraven in zone I op site Ronse De Stadstuin	'5':7,20 'de':15 'i-a':4,17 'met':2 'nummer':3 'op':12 'opgegraven':8 'rons':14 'site':13 'spoor':1 'stadstuin':16 'zone':10
37	I-A-8--	Spoor met nummer I-A-8-- opgegraven in zone I op site Ronse De Stadstuin	'8':7,20 'de':15 'i-a':4,17 'met':2 'nummer':3 'op':12 'opgegraven':8 'rons':14 'site':13 'spoor':1 'stadstuin':16 'zone':10
38	I-A-8--	Spoor met nummer I-A-8-- gevonden in zone I van Ronse De Stadstuin	'8':7,19 'de':14 'gevonden':8 'i-a':4,16 'met':2 'nummer':3 'rons':13 'spoor':1 'stadstuin':15 'van':12 'zone':10
39	I-A-8--	Spoor met nummer I-A-8--	'8':7,11 'i-a':4,8 'met':2 'nummer':3 'spoor':1
40	I-A-8--	Spoor met nummer I-A-8--	'8':7,11 'i-a':4,8 'met':2 'nummer':3 'spoor':1
41	I-A-8--	Spoor met nummer I-A-8--	'8':7,11 'i-a':4,8 'met':2 'nummer':3 'spoor':1
42	I-A-8--	Spoor met nummer I-A-8--	'8':7,11 'i-a':4,8 'met':2 'nummer':3 'spoor':1
43	I-A-8--	Spoor met nummer I-A-8--	'8':7,11 'i-a':4,8 'met':2 'nummer':3 'spoor':1
44	I-A-8--	Spoor met nummer I-A-8--	'8':7,11 'i-a':4,8 'met':2 'nummer':3 'spoor':1
45	I-A-8--	Spoor met nummer I-A-8--	'8':7,11 'i-a':4,8 'met':2 'nummer':3 'spoor':1
46	I-A-8--	Spoor met nummer I-A-8--	'8':7,11 'i-a':4,8 'met':2 'nummer':3 'spoor':1
47	I-A-8--	Spoor met nummer I-A-8--	'8':7,11 'i-a':4,8 'met':2 'nummer':3 'spoor':1
48	I-A-8--	Spoor met nummer I-A-8--	'8':7,11 'i-a':4,8 'met':2 'nummer':3 'spoor':1
49	I-A-8--	Spoor met nummer I-A-8--	'8':7,11 'i-a':4,8 'met':2 'nummer':3 'spoor':1
50	I-A-8--	Spoor met nummer I-A-8--	'8':7,11 'i-a':4,8 'met':2 'nummer':3 'spoor':1
51	I-A-8--	Spoor met nummer I-A-8--	'8':7,11 'i-a':4,8 'met':2 'nummer':3 'spoor':1
52	I-A-8--	Spoor met nummer I-A-8--	'8':7,11 'i-a':4,8 'met':2 'nummer':3 'spoor':1
53	I-A-9--	Spoor met nummer I-A-9-- gevonden in Zone I van Ronse De Stadstuin	'9':7,19 'de':14 'gevonden':8 'i-a':4,16 'met':2 'nummer':3 'rons':13 'spoor':1 'stadstuin':15 'van':12 'zone':10
54	I-A-10--	Spoor met nummer I-A-10--	'10':7,11 'i-a':4,8 'met':2 'nummer':3 'spoor':1
64	I-A-12--	Spoor met nummer I-A-12-- gevonden in zone I van Ronse De Stadstuin	'12':7,19 'de':14 'gevonden':8 'i-a':4,16 'met':2 'nummer':3 'rons':13 'spoor':1 'stadstuin':15 'van':12 'zone':10
69	I-A-12	Context met nummer I-A-12 gevonden in Zone I te Ronse De Stadstuin	'12':7,19 'context':1 'de':14 'gevonden':8 'i-a':4,16 'met':2 'nummer':3 'rons':13 'stadstuin':15 'te':12 'zone':10
55	I-A-11--	Spoor met nummer I-A-11-- gevonden in Zone I te Ronse De Stadstuin	'11':7,19 'de':14 'gevonden':8 'i-a':4,16 'met':2 'nummer':3 'rons':13 'spoor':1 'stadstuin':15 'te':12 'zone':10
114	qsdg	sqdg	'qsdg':2 'sqdg':1
115	sqdg	sdg	'sdg':1 'sqdg':2
116	qsdg	sqdg	'qsdg':2 'sqdg':1
117	sqgd	sdg	'sdg':1 'sqgd':2
118	qsdg	sdg	'qsdg':2 'sdg':1
\.


--
-- Name: nodes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('nodes_id_seq', 118, true);


--
-- Data for Name: nodes_log; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY nodes_log (id, node_id, name, description, descr, action, action_by, action_time) FROM stdin;
191	114	qsdg	sqdg	\N	I	5	2015-09-08 23:19:13
192	115	sqdg	sdg	\N	I	5	2015-09-08 23:21:07
193	116	qsdg	sqdg	\N	I	5	2015-09-08 23:44:08
194	117	sqgd	sdg	\N	I	5	2015-09-08 23:45:05
195	118	qsdg	sdg	\N	I	5	2015-09-09 00:18:17
\.


--
-- Name: nodes_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('nodes_log_id_seq', 195, true);


--
-- Data for Name: phinxlog; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY phinxlog (version, start_time, end_time) FROM stdin;
20150804222242	2015-09-07 16:41:49	2015-09-07 16:41:49
20150804224011	2015-09-07 16:41:49	2015-09-07 16:41:49
20150805142146	2015-09-07 16:42:04	2015-09-07 16:42:05
20150810142508	2015-09-07 16:42:05	2015-09-07 16:42:05
20150814122641	2015-09-07 16:42:05	2015-09-07 16:42:05
20150825084544	2015-09-07 16:42:05	2015-09-07 16:42:05
20150825132203	2015-09-07 16:42:05	2015-09-07 16:42:05
20150825132213	2015-09-07 16:42:05	2015-09-07 16:42:05
20150907144546	2015-09-07 17:31:44	2015-09-07 17:31:44
20150907195651	2015-09-08 09:34:34	2015-09-08 09:34:35
20150908211446	2015-09-08 23:15:36	2015-09-08 23:15:36
\.


--
-- Data for Name: properties; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY properties (id, name, description, datatype, descr) FROM stdin;
1	is of type	\N	text	'type':3
2	has property	\N	text	'properti':2
3	has geometry	\N	geometry	'geometri':2
4	has interpretation	\N	text	'interpret':2
5	is part of	\N	node	'part':2
6	has date	\N	year_period	'date':2
\.


--
-- Name: properties_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('properties_id_seq', 4, true);


--
-- Data for Name: properties_log; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY properties_log (id, property_id, name, description, datatype, descr, action, action_by, action_time) FROM stdin;
\.


--
-- Name: properties_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('properties_log_id_seq', 13, true);


--
-- Data for Name: relations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY relations (id, property, value, qualifier, rank, nodevalue, geometryvalue, startnode) FROM stdin;
12	1	Spoor	\N	normal	\N	\N	2
32	4	greppel	\N	normal	\N	\N	2
222	1	sqdg	\N	normal	\N	\N	114
224	1	qsdg	5	preferred	\N	\N	116
226	1	sqdg	\N	normal	\N	\N	118
10	1	Site	\N	\N	\N	\N	1
11	2	Excavation	\N	\N	\N	\N	1
13	1	Spoor	\N	\N	\N	\N	3
14	1	Spoor	\N	\N	\N	\N	4
15	1	Structuur	\N	\N	\N	\N	5
16	1	Context	\N	\N	\N	\N	6
17	1	Context	\N	\N	\N	\N	7
18	1	Context	\N	\N	\N	\N	8
19	1	Spoor	\N	\N	\N	\N	9
20	1	Context	\N	\N	\N	\N	10
21	1	Structuur	\N	\N	\N	\N	11
33	4	greppel	\N	\N	\N	\N	3
34	4	greppel	\N	\N	\N	\N	4
35	4	weg	\N	\N	\N	\N	5
36	4	greppel	\N	\N	\N	\N	6
37	4	greppel	\N	\N	\N	\N	7
38	4	greppel	\N	\N	\N	\N	8
39	4	paalspoor	\N	\N	\N	\N	9
40	4	paalspoor	\N	\N	\N	\N	10
41	4	gebouw	\N	\N	\N	\N	11
42	6	1501/1940	\N	\N	\N	\N	5
43	6	1501/1800	\N	\N	\N	\N	8
44	6	-0049/0080	\N	\N	\N	\N	10
45	6	-0159/0080	\N	\N	\N	\N	11
51	1	spoor	\N	\N	\N	\N	13
52	1	context	\N	\N	\N	\N	14
53	1	structuur	\N	\N	\N	\N	15
200	1	Kuil-ongedefinieerd	\N	\N	\N	\N	103
201	1	spoor	\N	\N	\N	\N	103
202	1	Kuil-ongedefinieerd	\N	\N	\N	\N	104
57	1	spoor	\N	\N	\N	\N	32
58	1	spoor	\N	normal	\N	\N	52
59	1	spoor	\N	normal	\N	\N	53
60	1	spoor	\N	normal	\N	\N	54
61	1	spoor	\N	normal	\N	\N	55
203	1	spoor	\N	\N	\N	\N	104
204	1	Kuil-ongedefinieerd	\N	\N	\N	\N	105
205	1	spoor	\N	\N	\N	\N	105
206	1	Kuil-ongedefinieerd	\N	\N	\N	\N	106
207	1	spoor	\N	\N	\N	\N	106
208	1	Poel-opgave	\N	\N	\N	\N	107
209	1	spoor	\N	\N	\N	\N	107
210	1	Kuil-ongedefinieerd	\N	\N	\N	\N	108
211	1	spoor	\N	\N	\N	\N	108
212	1	Kuil-ongedefinieerd	\N	\N	\N	\N	109
72	1	spoor	\N	normal	\N	\N	64
74	2	Greppel	\N	normal	\N	\N	64
213	1	spoor	\N	\N	\N	\N	109
214	1	Kuil-ongedefinieerd	\N	\N	\N	\N	110
215	1	spoor	\N	\N	\N	\N	110
216	1	Kuil-ongedefinieerd	\N	\N	\N	\N	111
217	1	spoor	\N	\N	\N	\N	111
218	1	Greppel	\N	\N	\N	\N	112
219	1	spoor	\N	\N	\N	\N	112
220	1	Greppel	\N	\N	\N	\N	113
221	1	spoor	\N	\N	\N	\N	113
87	1	context	\N	normal	\N	\N	69
89	2	Greppel	\N	normal	\N	\N	69
23	5	7	\N	\N	7	\N	3
24	5	8	\N	\N	8	\N	4
25	5	12	\N	\N	12	\N	5
26	5	5	\N	\N	5	\N	6
27	5	5	\N	\N	5	\N	7
28	5	5	\N	\N	5	\N	8
29	5	10	\N	\N	10	\N	9
30	5	11	\N	\N	11	\N	10
31	5	12	\N	\N	12	\N	11
50	5	1	\N	\N	1	\N	12
73	5	1	\N	normal	1	\N	64
88	5	1	\N	normal	1	\N	69
47	3	2	\N	\N	\N	2	3
48	3	3	\N	\N	\N	3	4
49	3	4	\N	\N	\N	4	9
22	5	6	\N	normal	6	\N	2
46	3	1	\N	normal	\N	1	2
223	1	sqdg	\N	normal	\N	\N	115
225	1	alpha	5	normal	\N	\N	117
\.


--
-- Name: relations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('relations_id_seq', 226, true);


--
-- Data for Name: relations_log; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY relations_log (id, relation_id, startid, property_id, value, qualifier, rank, action, action_by, action_time, nodevalue, geometryvalue) FROM stdin;
360	222	114	\N	sqdg	\N	normal	I	5	2015-09-08 23:19:13	\N	\N
361	223	115	\N	sqdg	\N	normal	I	5	2015-09-08 23:21:07	\N	\N
362	224	116	\N	qsdg	5	preferred	I	5	2015-09-08 23:44:08	\N	\N
363	225	117	\N	alpha	5	normal	I	5	2015-09-08 23:45:05	\N	\N
364	226	118	\N	sqdg	\N	normal	I	5	2015-09-09 00:18:17	\N	\N
\.


--
-- Name: relations_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('relations_log_id_seq', 364, true);


--
-- Data for Name: secondary_relations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY secondary_relations (id, property, value, qualifier, nodevalue, geometryvalue, parent_relation, rank) FROM stdin;
19	1	sdqg	\N	\N	\N	12	normal
20	1	sqdg	\N	\N	\N	226	normal
\.


--
-- Name: secondary_relations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('secondary_relations_id_seq', 20, true);


--
-- Data for Name: spatial_ref_sys; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY spatial_ref_sys (srid, auth_name, auth_srid, srtext, proj4text) FROM stdin;
\.


--
-- Data for Name: user_custom_fields; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY user_custom_fields (user_id, attribute, value) FROM stdin;
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY users (id, email, password, salt, roles, time_created, username, is_enabled, confirmation_token, time_password_reset_requested, name) FROM stdin;
5	david.vandorpe@admin.be	Yfcn5bX51V2mZpuusz6oh90/fg4beP3T2qvwYqWoxTsXn3Pq7D4cCBfIOVGtxGOlmPOmyj6C2QCV7cbcX4mZfw==	ig75p3ubp5c8oc0cso48so04oo4o8o8	ROLE_ADMIN	1441638049	\N	t	\N	\N	David
\.


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('users_id_seq', 5, true);


--
-- Name: geometries_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY geometries
    ADD CONSTRAINT geometries_pkey PRIMARY KEY (id);


--
-- Name: nodes_logging_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY nodes_log
    ADD CONSTRAINT nodes_logging_pkey PRIMARY KEY (id);


--
-- Name: nodes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY nodes
    ADD CONSTRAINT nodes_pkey PRIMARY KEY (id);


--
-- Name: properties_logging_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY properties_log
    ADD CONSTRAINT properties_logging_pkey PRIMARY KEY (id);


--
-- Name: properties_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY properties
    ADD CONSTRAINT properties_pkey PRIMARY KEY (id);


--
-- Name: secondary_relations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY secondary_relations
    ADD CONSTRAINT secondary_relations_pkey PRIMARY KEY (id);


--
-- Name: statements_logging_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY relations_log
    ADD CONSTRAINT statements_logging_pkey PRIMARY KEY (id);


--
-- Name: statements_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY relations
    ADD CONSTRAINT statements_pkey PRIMARY KEY (id);


--
-- Name: user_custom_fields_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY user_custom_fields
    ADD CONSTRAINT user_custom_fields_pkey PRIMARY KEY (user_id, attribute);


--
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: users_email; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX users_email ON users USING btree (email);


--
-- Name: users_username; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX users_username ON users USING btree (username);


--
-- Name: tsvectorupdatenode; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER tsvectorupdatenode BEFORE INSERT OR UPDATE ON nodes FOR EACH ROW EXECUTE PROCEDURE tsvector_update_trigger('descr', 'pg_catalog.english', 'description', 'name');


--
-- Name: tsvectorupdateprop; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER tsvectorupdateprop BEFORE INSERT OR UPDATE ON properties FOR EACH ROW EXECUTE PROCEDURE tsvector_update_trigger('descr', 'pg_catalog.english', 'description', 'name');


--
-- Name: geometryvalue_fk_constraint; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY relations
    ADD CONSTRAINT geometryvalue_fk_constraint FOREIGN KEY (geometryvalue) REFERENCES geometries(id) ON DELETE CASCADE;


--
-- Name: nodes_log_action_by; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY nodes_log
    ADD CONSTRAINT nodes_log_action_by FOREIGN KEY (action_by) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: nodevalue_fk_constraint; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY relations
    ADD CONSTRAINT nodevalue_fk_constraint FOREIGN KEY (nodevalue) REFERENCES nodes(id) ON DELETE CASCADE;


--
-- Name: properties_log_action_by; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY properties_log
    ADD CONSTRAINT properties_log_action_by FOREIGN KEY (action_by) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: property_fk_constraint; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY relations
    ADD CONSTRAINT property_fk_constraint FOREIGN KEY (property) REFERENCES properties(id) ON DELETE CASCADE;


--
-- Name: relations_log_action_by; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY relations_log
    ADD CONSTRAINT relations_log_action_by FOREIGN KEY (action_by) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: relations_log_geometryvalue; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY relations_log
    ADD CONSTRAINT relations_log_geometryvalue FOREIGN KEY (geometryvalue) REFERENCES geometries(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: relations_log_nodevalue; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY relations_log
    ADD CONSTRAINT relations_log_nodevalue FOREIGN KEY (nodevalue) REFERENCES nodes(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: relations_log_property_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY relations_log
    ADD CONSTRAINT relations_log_property_id FOREIGN KEY (property_id) REFERENCES properties(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: relations_startnode; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY relations
    ADD CONSTRAINT relations_startnode FOREIGN KEY (startnode) REFERENCES nodes(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: secondary_relations_geometryvalue; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY secondary_relations
    ADD CONSTRAINT secondary_relations_geometryvalue FOREIGN KEY (geometryvalue) REFERENCES geometries(id);


--
-- Name: secondary_relations_nodevalue; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY secondary_relations
    ADD CONSTRAINT secondary_relations_nodevalue FOREIGN KEY (nodevalue) REFERENCES nodes(id);


--
-- Name: secondary_relations_parent_relation; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY secondary_relations
    ADD CONSTRAINT secondary_relations_parent_relation FOREIGN KEY (parent_relation) REFERENCES relations(id);


--
-- Name: secondary_relations_property; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY secondary_relations
    ADD CONSTRAINT secondary_relations_property FOREIGN KEY (property) REFERENCES properties(id);


--
-- Name: user_custom_fields_user_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY user_custom_fields
    ADD CONSTRAINT user_custom_fields_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

