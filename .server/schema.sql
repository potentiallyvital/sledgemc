--
-- PostgreSQL database dump
--

-- Dumped from database version 16.2 (Ubuntu 16.2-1ubuntu4)
-- Dumped by pg_dump version 16.2 (Ubuntu 16.2-1ubuntu4)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: id_sequence; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.id_sequence
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.id_sequence OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: id; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.id (
    id bigint DEFAULT nextval('public.id_sequence'::regclass) NOT NULL,
    created timestamp without time zone DEFAULT (now())::timestamp without time zone NOT NULL,
    modified timestamp without time zone DEFAULT (now())::timestamp without time zone NOT NULL
);


ALTER TABLE public.id OWNER TO postgres;

--
-- Name: account; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.account (
    role_id bigint,
    first_name character varying(64),
    last_name character varying(64),
    email character varying(64),
    phone character varying(10),
    street character varying(64),
    unit character varying(64),
    city character varying(64),
    state character varying(2),
    zip character varying(5),
    zip_4 character varying(4)
)
INHERITS (public.id);


ALTER TABLE public.account OWNER TO postgres;

--
-- Name: children; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.children (
    parent_id bigint,
    parent_table character varying(255),
    parent_class character varying(255),
    child_id bigint,
    child_table character varying(255),
    child_class character varying(255)
)
INHERITS (public.id);


ALTER TABLE public.children OWNER TO postgres;

--
-- Name: message; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.message (
    subject character varying(255),
    body text
)
INHERITS (public.id);


ALTER TABLE public.message OWNER TO postgres;

--
-- Name: flash; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.flash (
)
INHERITS (public.message);


ALTER TABLE public.flash OWNER TO postgres;

--
-- Name: login; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.login (
    email character varying(64),
    password text,
    invalid_logins bigint,
    temporary_pass character varying(64),
    temporary_expires timestamp without time zone
)
INHERITS (public.id);


ALTER TABLE public.login OWNER TO postgres;

--
-- Name: role; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.role (
    name character varying(64),
    code character varying(64)
)
INHERITS (public.id);


ALTER TABLE public.role OWNER TO postgres;

--
-- Name: account id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.account ALTER COLUMN id SET DEFAULT nextval('public.id_sequence'::regclass);


--
-- Name: account created; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.account ALTER COLUMN created SET DEFAULT (now())::timestamp without time zone;


--
-- Name: account modified; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.account ALTER COLUMN modified SET DEFAULT (now())::timestamp without time zone;


--
-- Name: children id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.children ALTER COLUMN id SET DEFAULT nextval('public.id_sequence'::regclass);


--
-- Name: children created; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.children ALTER COLUMN created SET DEFAULT (now())::timestamp without time zone;


--
-- Name: children modified; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.children ALTER COLUMN modified SET DEFAULT (now())::timestamp without time zone;


--
-- Name: flash id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.flash ALTER COLUMN id SET DEFAULT nextval('public.id_sequence'::regclass);


--
-- Name: flash created; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.flash ALTER COLUMN created SET DEFAULT (now())::timestamp without time zone;


--
-- Name: flash modified; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.flash ALTER COLUMN modified SET DEFAULT (now())::timestamp without time zone;


--
-- Name: login id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.login ALTER COLUMN id SET DEFAULT nextval('public.id_sequence'::regclass);


--
-- Name: login created; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.login ALTER COLUMN created SET DEFAULT (now())::timestamp without time zone;


--
-- Name: login modified; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.login ALTER COLUMN modified SET DEFAULT (now())::timestamp without time zone;


--
-- Name: message id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.message ALTER COLUMN id SET DEFAULT nextval('public.id_sequence'::regclass);


--
-- Name: message created; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.message ALTER COLUMN created SET DEFAULT (now())::timestamp without time zone;


--
-- Name: message modified; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.message ALTER COLUMN modified SET DEFAULT (now())::timestamp without time zone;


--
-- Name: role id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role ALTER COLUMN id SET DEFAULT nextval('public.id_sequence'::regclass);


--
-- Name: role created; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role ALTER COLUMN created SET DEFAULT (now())::timestamp without time zone;


--
-- Name: role modified; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role ALTER COLUMN modified SET DEFAULT (now())::timestamp without time zone;


--
-- Name: idx_0c9d6257d06181d4c95ce7dff581c416; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_0c9d6257d06181d4c95ce7dff581c416 ON public.id USING btree (id);


--
-- Name: idx_3105c847f0074b5cd7b525f4fb770dfb; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_3105c847f0074b5cd7b525f4fb770dfb ON public.children USING btree (id);


--
-- Name: idx_53eb18e51b16fcf37dc9ef717365fd1e; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_53eb18e51b16fcf37dc9ef717365fd1e ON public.role USING btree (name);


--
-- Name: idx_5737e797c58db6d71a3234b6b4ebf009; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_5737e797c58db6d71a3234b6b4ebf009 ON public.message USING btree (id);


--
-- Name: idx_60df765091890744b520142c59bdb5a0; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_60df765091890744b520142c59bdb5a0 ON public.children USING btree (child_id);


--
-- Name: idx_6faa2b2180855983c8d5194493729627; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_6faa2b2180855983c8d5194493729627 ON public.account USING btree (id);


--
-- Name: idx_7e7de8c9a5cf18c7eb4cc7957d236321; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_7e7de8c9a5cf18c7eb4cc7957d236321 ON public.login USING btree (id);


--
-- Name: idx_8dac1b149bca9510a4b5d688423a0d99; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_8dac1b149bca9510a4b5d688423a0d99 ON public.flash USING btree (id);


--
-- Name: idx_927162bf0a75908936e1bfc43a5b641e; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_927162bf0a75908936e1bfc43a5b641e ON public.login USING btree (email);


--
-- Name: idx_a7a27b45943ba812cd02724b583ee5f0; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_a7a27b45943ba812cd02724b583ee5f0 ON public.role USING btree (id);


--
-- Name: idx_abe80928cd5fbbde7e23929bbed5bb33; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_abe80928cd5fbbde7e23929bbed5bb33 ON public.role USING btree (code);


--
-- Name: idx_c86187591bfbc732689f350c819d4198; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_c86187591bfbc732689f350c819d4198 ON public.children USING btree (parent_id, child_id);


--
-- Name: idx_ca248cfeb97c6473c68cb1d6daa61afa; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_ca248cfeb97c6473c68cb1d6daa61afa ON public.children USING btree (parent_id, child_class);


--
-- Name: idx_ceb00f82ba9355ef9b9d636b33f7806d; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_ceb00f82ba9355ef9b9d636b33f7806d ON public.children USING btree (parent_id);


--
-- PostgreSQL database dump complete
--

