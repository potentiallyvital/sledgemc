<?php
require __DIR__.'/config.php';
?>

==== DATABASE USER =============================================

CREATE USER <?=SLEDGEMC_USER;?>;
ALTER USER <?=SLEDGEMC_USER;?> superuser;
ALTER ROLE <?=SLEDGEMC_USER;?> PASSWORD '<?=SLEDGEMC_PASS;?>';

==== INITIAL DB ================================================

CREATE DATABASE <?=SLEDGEMC_NAME;?>;

\c <?=SLEDGEMC_NAME;?>


==== TABLES ====================================================

CREATE SEQUENCE id_sequence;

CREATE TABLE <?=SLEDGEMC_BASE_TABLE;?> (
	id bigint not null default nextval('id_sequence'),
	created timestamp without time zone not null default now()::timestamp,
	modified timestamp without time zone not null default now()::timestamp
);

CREATE TABLE <?=SLEDGEMC_CHILD_TABLE;?> (
	parent_id bigint,
	parent_table varchar(255),
	parent_class varchar(255),
	child_id bigint,
	child_table varchar(255),
	child_class varchar(255)
) INHERITS (<?=SLEDGEMC_BASE_TABLE;?>);

CREATE TABLE <?=SLEDGEMC_ROLE_TABLE;?> (
	name varchar(64),
	code varchar(64)
) INHERITS (<?=SLEDGEMC_BASE_TABLE;?>);

CREATE TABLE <?=SLEDGEMC_ACCOUNT_TABLE;?> (
	role_id bigint,
	first_name varchar(64),
	last_name varchar(64),
	email varchar(64),
	phone varchar(10),
	street varchar(64),
	unit varchar(64),
	city varchar(64),
	state varchar(2),
	zip varchar(5),
	zip_4 varchar(4)
) INHERITS (<?=SLEDGEMC_BASE_TABLE;?>);

CREATE TABLE <?=SLEDGEMC_LOGIN_TABLE;?> (
	email varchar(64),
	password_hash text
) INHERITS (<?=SLEDGEMC_BASE_TABLE;?>);

=================================================================
