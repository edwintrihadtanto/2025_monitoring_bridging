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

 Date: 10/11/2025 02:19:44
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
-- Primary Key structure for table bpjs_logs
-- ----------------------------
ALTER TABLE "public"."bpjs_logs" ADD CONSTRAINT "bpjs_logs_pkey" PRIMARY KEY ("id");
