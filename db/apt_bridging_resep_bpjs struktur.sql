/*
 Navicat Premium Data Transfer

 Source Server         : localhost_Pos10
 Source Server Type    : PostgreSQL
 Source Server Version : 100023 (100023)
 Source Host           : localhost:5432
 Source Catalog        : localsimrs
 Source Schema         : public

 Target Server Type    : PostgreSQL
 Target Server Version : 100023 (100023)
 File Encoding         : 65001

 Date: 30/03/2026 02:08:22
*/


-- ----------------------------
-- Table structure for apt_bridging_resep_bpjs
-- ----------------------------
DROP TABLE IF EXISTS "public"."apt_bridging_resep_bpjs";
CREATE TABLE "public"."apt_bridging_resep_bpjs" (
  "id" int4 NOT NULL DEFAULT nextval('bridging_resep_bpjs_id_seq'::regclass),
  "noresep_simrs" varchar(30) COLLATE "pg_catalog"."default" NOT NULL,
  "noresep_bpjs" varchar(5) COLLATE "pg_catalog"."default" NOT NULL,
  "created_at" timestamp(6) NOT NULL DEFAULT now(),
  "status_kirim" bool NOT NULL DEFAULT false,
  "no_out" numeric(5,0) NOT NULL,
  "tgl_out" timestamp(6) NOT NULL,
  "noApotik" varchar(255) COLLATE "pg_catalog"."default",
  "response_bpjs" jsonb,
  "response_message" varchar(255) COLLATE "pg_catalog"."default",
  "sts_batal" bool NOT NULL DEFAULT false,
  "alasan_batal" text COLLATE "pg_catalog"."default"
)
;
COMMENT ON COLUMN "public"."apt_bridging_resep_bpjs"."response_message" IS 'Ok = Resep berhasil dikirim ke BPJS';
COMMENT ON COLUMN "public"."apt_bridging_resep_bpjs"."sts_batal" IS 'jika true maka dihapus, pastikan status_kirim false';

-- ----------------------------
-- Table structure for apt_bridging_resep_detail
-- ----------------------------
DROP TABLE IF EXISTS "public"."apt_bridging_resep_detail";
CREATE TABLE "public"."apt_bridging_resep_detail" (
  "id" int4 NOT NULL DEFAULT nextval('apt_bridging_resep_detail_id_seq'::regclass),
  "noresep" varchar(50) COLLATE "pg_catalog"."default" NOT NULL,
  "no_out" varchar(30) COLLATE "pg_catalog"."default" NOT NULL,
  "noresep_bpjs" varchar(50) COLLATE "pg_catalog"."default",
  "no_apotik" varchar(50) COLLATE "pg_catalog"."default",
  "nm_obat" varchar(255) COLLATE "pg_catalog"."default",
  "signa1" varchar(10) COLLATE "pg_catalog"."default",
  "signa2" varchar(10) COLLATE "pg_catalog"."default",
  "jml_obat" int4 DEFAULT 0,
  "jho" varchar(10) COLLATE "pg_catalog"."default",
  "cat_khusus" varchar(50) COLLATE "pg_catalog"."default",
  "status_kirim" bool DEFAULT false,
  "response_bpjs" text COLLATE "pg_catalog"."default",
  "created_at" timestamp(6) DEFAULT CURRENT_TIMESTAMP,
  "kd_obat_simrs" varchar(50) COLLATE "pg_catalog"."default",
  "kd_obat_bpjs" varchar(50) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Uniques structure for table apt_bridging_resep_bpjs
-- ----------------------------
ALTER TABLE "public"."apt_bridging_resep_bpjs" ADD CONSTRAINT "bridging_resep_bpjs_noresep_bpjs_tgl_resep_key" UNIQUE ("noresep_bpjs", "no_out", "tgl_out");

-- ----------------------------
-- Primary Key structure for table apt_bridging_resep_bpjs
-- ----------------------------
ALTER TABLE "public"."apt_bridging_resep_bpjs" ADD CONSTRAINT "bridging_resep_bpjs_pkey" PRIMARY KEY ("id");

-- ----------------------------
-- Indexes structure for table apt_bridging_resep_detail
-- ----------------------------
CREATE INDEX "idx_apt_bridging_resep_detail" ON "public"."apt_bridging_resep_detail" USING btree (
  "noresep" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table apt_bridging_resep_detail
-- ----------------------------
ALTER TABLE "public"."apt_bridging_resep_detail" ADD CONSTRAINT "apt_bridging_resep_detail_pkey" PRIMARY KEY ("id");
