/*
 Navicat Premium Data Transfer

 Source Server         : localhost_Pos10
 Source Server Type    : PostgreSQL
 Source Server Version : 100023 (100023)
 Source Host           : localhost:5432
 Source Catalog        : 2025_logbridgefar
 Source Schema         : public

 Target Server Type    : PostgreSQL
 Target Server Version : 100023 (100023)
 File Encoding         : 65001

 Date: 15/01/2026 01:00:50
*/


-- ----------------------------
-- Table structure for bpjs_logs
-- ----------------------------
DROP TABLE IF EXISTS "public"."bpjs_logs";
CREATE TABLE "public"."bpjs_logs" (
  "id" int4 NOT NULL GENERATED ALWAYS AS IDENTITY (
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1
),
  "endpoint" varchar(255) COLLATE "pg_catalog"."default" NOT NULL,
  "method" varchar(10) COLLATE "pg_catalog"."default" NOT NULL,
  "request_header" text COLLATE "pg_catalog"."default",
  "request_body" text COLLATE "pg_catalog"."default",
  "response_code" int4 NOT NULL,
  "response_body" text COLLATE "pg_catalog"."default",
  "created_at" timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP
)
;

-- ----------------------------
-- Table structure for ci_sessions
-- ----------------------------
DROP TABLE IF EXISTS "public"."ci_sessions";
CREATE TABLE "public"."ci_sessions" (
  "id" varchar(128) COLLATE "pg_catalog"."default" NOT NULL,
  "ip_address" varchar(45) COLLATE "pg_catalog"."default" NOT NULL,
  "timestamp" timestamptz(6) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  "data" bytea DEFAULT '\x'::bytea
)
;

-- ----------------------------
-- Table structure for rules
-- ----------------------------
DROP TABLE IF EXISTS "public"."rules";
CREATE TABLE "public"."rules" (
  "id_rules" int2 NOT NULL,
  "role" varchar(255) COLLATE "pg_catalog"."default" NOT NULL,
  "access_sidebar" text COLLATE "pg_catalog"."default" NOT NULL
)
;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS "public"."users";
CREATE TABLE "public"."users" (
  "id" int4 NOT NULL DEFAULT nextval('users_id_seq'::regclass),
  "username" varchar(100) COLLATE "pg_catalog"."default" NOT NULL,
  "password_hash" varchar(255) COLLATE "pg_catalog"."default" NOT NULL,
  "full_name" varchar(100) COLLATE "pg_catalog"."default",
  "created_at" timestamp(6) DEFAULT CURRENT_TIMESTAMP,
  "genre" bool DEFAULT true,
  "role" varchar(255) COLLATE "pg_catalog"."default" DEFAULT ''::character varying
)
;

-- ----------------------------
-- Primary Key structure for table bpjs_logs
-- ----------------------------
ALTER TABLE "public"."bpjs_logs" ADD CONSTRAINT "bpjs_logs_pkey" PRIMARY KEY ("id");

-- ----------------------------
-- Indexes structure for table ci_sessions
-- ----------------------------
CREATE INDEX "ci_sessions_timestamp" ON "public"."ci_sessions" USING btree (
  "timestamp" "pg_catalog"."timestamptz_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table ci_sessions
-- ----------------------------
ALTER TABLE "public"."ci_sessions" ADD CONSTRAINT "ci_sessions_pkey" PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table rules
-- ----------------------------
ALTER TABLE "public"."rules" ADD CONSTRAINT "rulses_pkey" PRIMARY KEY ("id_rules");

-- ----------------------------
-- Uniques structure for table users
-- ----------------------------
ALTER TABLE "public"."users" ADD CONSTRAINT "users_username_key" UNIQUE ("username");

-- ----------------------------
-- Primary Key structure for table users
-- ----------------------------
ALTER TABLE "public"."users" ADD CONSTRAINT "users_pkey" PRIMARY KEY ("id");
