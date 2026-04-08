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

 Date: 09/04/2026 02:33:56
*/


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
  "kd_obat_bpjs" varchar(50) COLLATE "pg_catalog"."default",
  "tgl_out" timestamp(6),
  "permintaan" int2,
  "jenisracikan" varchar(255) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Records of apt_bridging_resep_detail
-- ----------------------------
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (44, 'AP2-050684', '4001', '00001', '0216A01604260000002', 'ADALAT OROS 30MG TAB', '1', '1', 5, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Kode Obat tidak sesuai dengan Jenis Resep."}', '2026-04-07 00:37:21', '00002492', '14250805113', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (45, 'AP2-050684', '4001', '00001', '0216A01604260000002', 'DOPAMET 250MG TAB', '1', '1', 15, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Kode Obat tidak sesuai dengan Jenis Resep."}', '2026-04-07 00:37:21', '00001462', '14250804881', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (36, 'AP3-036802', '3001', '00018', '0216A01603260000023', 'ZOLEDRONIC ACID 4MG/5ML INFUS', '1', '1', 1, '1', 'Single', 't', '{"status_code":"200","data":[],"message":"Obat Berhasil Simpan.."}', '2026-03-31 01:30:32', '00008733', '14250804944', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (20, '001155742', '3004', '00020', '0216A01603260000020', 'ZOLEDRONIC ACID 4MG/5ML INFUS', '1', '1', 1, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Kode Obat tidak sesuai dengan Jenis Resep."}', '2026-03-30 02:19:35', '00008733', '14250804944', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (21, '001155742', '3004', '00020', '0216A01603260000020', 'KALSIUM FOLINAT 50MG 5ML INJEKSI', '1', '1', 1, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Kode Obat tidak sesuai dengan Jenis Resep."}', '2026-03-30 02:19:36', '00001351', '14250805045', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (22, '001155742', '3004', '00020', '0216A01603260000020', 'XELODA 500MG TAB', '1', '1', 1, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Kode Obat tidak sesuai dengan Jenis Resep."}', '2026-03-30 02:19:36', '00002590', '14250804318', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (23, '001155742', '3004', '00021', '0216A01603260000020', 'ZOLEDRONIC ACID 4MG/5ML INFUS', '1', '1', 1, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Kode Obat tidak sesuai dengan Jenis Resep."}', '2026-03-30 02:21:53', '00008733', '14250804944', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (24, '001155742', '3004', '00021', '0216A01603260000020', 'KALSIUM FOLINAT 50MG 5ML INJEKSI', '1', '1', 1, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Kode Obat tidak sesuai dengan Jenis Resep."}', '2026-03-30 02:21:53', '00001351', '14250805045', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (25, '001155742', '3004', '00021', '0216A01603260000020', 'XELODA 500MG TAB', '1', '1', 1, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Kode Obat tidak sesuai dengan Jenis Resep."}', '2026-03-30 02:21:53', '00002590', '14250804318', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (26, '001155742', '3004', '00021', '0216A01603260000020', 'amiTRIPTYline 25MG TAB', '1', '1', 1, '1', 'Single', 't', '{"status_code":"200","data":[],"message":"Obat Berhasil Simpan.."}', '2026-03-30 02:24:21', '00000016', '14250804692', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (27, '001155742', '3004', '00021', '0216A01603260000020', 'ASAM FOLAT 1MG TAB', '1', '1', 1, '1', 'Single', 't', '{"status_code":"200","data":[],"message":"Obat Berhasil Simpan.."}', '2026-03-30 02:24:22', '00000044', '14250805226', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (28, '001155742', '3004', '00021', '0216A01603260000020', 'amiTRIPTYline 25MG TAB', '1', '1', 1, '1', 'Single', 'f', '{"status":"gagal","code":"201","message":"300 - Obat Beirisan dengan pemakaian obat Tgl Resep 21-03-2026"}', '2026-03-30 02:24:22', '00000016', '14250804692', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (29, '001155742', '3004', '00021', '0216A01603260000020', 'ALPRAZOLAM 1MG TAB', '1', '1', 1, '1', 'Single', 't', '{"status_code":"200","data":[],"message":"Obat Berhasil Simpan.."}', '2026-03-30 02:24:22', '00000007', '14250803809', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (37, 'AP3-036802', '3001', '00018', '0216A01603260000023', 'KALSIUM FOLINAT 50MG 5ML INJEKSI', '1', '1', 1, '1', 'Single', 't', '{"status_code":"200","data":[],"message":"Obat Berhasil Simpan.."}', '2026-03-31 01:39:22', '00001351', '14250805045', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (41, '001155746', '3001', '00001', '0216A01604260000001', 'ALPRAZOLAM 1MG TAB', '1', '1', 1, '1', 'Single', 'f', '{"status":"gagal","code":"201","message":"300 - Obat Beirisan dengan pemakaian obat Tgl Resep 04-04-2026"}', '2026-04-05 01:29:54', '00000007', '14250803809', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (38, '001155746', '3001', '00001', '0216A01604260000001', 'amiTRIPTYline 25MG TAB', '1', '1', 1, '1', 'Single', 't', '{"status_code":"200","data":[],"message":"Obat Berhasil Simpan.."}', '2026-04-04 23:57:46', '00000016', '14250804692', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (39, '001155746', '3001', '00001', '0216A01604260000001', 'ALPRAZOLAM 1MG TAB', '1', '1', 1, '1', 'Single', 't', '{"status_code":"200","data":[],"message":"Obat Berhasil Simpan.."}', '2026-04-05 00:00:36', '00000007', '14250803809', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (40, '001155746', '3001', '00001', '0216A01604260000001', 'ASAM FOLAT 1MG TAB', '1', '1', 1, '1', 'Single', 't', '{"status_code":"200","data":[],"message":"Obat Berhasil Simpan.."}', '2026-04-05 00:00:41', '00000044', '14250805226', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (42, 'AP2-050684', '4001', '00001', '0216A01604260000002', 'SODIUM BICARBONATE 500MG TABLET', '1', '1', 15, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Kode Obat tidak sesuai dengan Jenis Resep."}', '2026-04-07 00:37:20', '00000294', '14250804267', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (43, 'AP2-050684', '4001', '00001', '0216A01604260000002', 'ASAM FOLAT 1MG TAB', '1', '1', 15, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Kode Obat tidak sesuai dengan Jenis Resep."}', '2026-04-07 00:37:20', '00000044', '14250805226', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (46, 'AP2-050684', '4001', '00001', '0216A01604260000002', 'SODIUM BICARBONATE 500MG TABLET', '1', '1', 15, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Kode Obat tidak sesuai dengan Jenis Resep."}', '2026-04-07 00:38:43', '00000294', '14250804267', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (47, 'AP2-050684', '4001', '00001', '0216A01604260000002', 'ASAM FOLAT 1MG TAB', '1', '1', 15, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Kode Obat tidak sesuai dengan Jenis Resep."}', '2026-04-07 00:38:43', '00000044', '14250805226', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (48, 'AP2-050684', '4001', '00001', '0216A01604260000002', 'ADALAT OROS 30MG TAB', '1', '1', 5, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Kode Obat tidak sesuai dengan Jenis Resep."}', '2026-04-07 00:38:44', '00002492', '14250805113', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (49, 'AP2-050684', '4001', '00001', '0216A01604260000002', 'DOPAMET 250MG TAB', '1', '1', 15, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Kode Obat tidak sesuai dengan Jenis Resep."}', '2026-04-07 00:38:45', '00001462', '14250804881', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (50, 'AP2-050684', '4001', '00001', '0216A01604260000002', 'DOPAMET 250MG TAB', '1', '1', 15, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Kode Obat tidak sesuai dengan Jenis Resep."}', '2026-04-07 00:57:07', '00001462', '14250804881', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (51, 'AP2-050684', '4001', '00001', '0216A01604260000002', 'ALPRAZOLAM 0.5MG TAB', '1', '1', 15, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Kode Obat tidak sesuai dengan Jenis Resep."}', '2026-04-07 00:57:08', '00000006', '14250804055', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (52, 'AP2-050684', '4001', '00001', '0216A01604260000002', 'amiTRIPTYline 25MG TAB', '1', '1', 15, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Kode Obat tidak sesuai dengan Jenis Resep."}', '2026-04-07 00:57:08', '00000016', '14250804692', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (53, 'AP2-050684', '4001', '00001', '0216A01604260000002', 'ASAM FOLAT 1MG TAB', '1', '1', 5, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Kode Obat tidak sesuai dengan Jenis Resep."}', '2026-04-07 00:57:08', '00000044', '14250805226', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (54, 'AP2-050684', '4001', '00001', '0216A01604260000002', 'asam MEFENamat 500MG TAB', '1', '1', 30, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Kode Obat tidak sesuai dengan Jenis Resep."}', '2026-04-07 00:57:09', '00002263', '14250804444', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (55, 'AP2-050684', '4001', '00001', '0216A01604260000002', 'amiTRIPTYline 25MG TAB', '1', '1', 15, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Kode Obat tidak sesuai dengan Jenis Resep."}', '2026-04-07 00:57:58', '00000016', '14250804692', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (56, 'AP2-050684', '4001', '00001', '0216A01604260000002', 'CALCIUM POLYSTYRENE SULFONAT 5GR SACHET', '1', '1', 1, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Kode Obat tidak sesuai dengan Jenis Resep."}', '2026-04-07 01:02:06', '00007482', '14250805048', NULL, NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (60, 'AP2-050684', '4001', '00001', '0216A01604260000002', 'TAMOFEN 10MG TAB', '1', '1', 30, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Signa atau Jumlah Hari tidak sesuai dengan Jumlah Obat."}', '2026-04-07 01:13:38', '00002065', '14250804718', '2026-04-07 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (58, 'AP2-050684', '4001', '00001', '0216A01604260000002', 'TAMOFEN 10MG TAB', '1', '1', 30, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Signa atau Jumlah Hari tidak sesuai dengan Jumlah Obat."}', '2026-04-07 01:11:51', '00002065', '14250804718', '2026-04-07 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (59, 'AP2-050684', '4001', '00001', '0216A01604260000002', 'TAMOFEN 10MG TAB', '1', '1', 30, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Signa atau Jumlah Hari tidak sesuai dengan Jumlah Obat."}', '2026-04-07 01:12:56', '00002065', '14250804718', '2026-04-07 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (61, 'AP2-050684', '4001', '00001', '0216A01604260000002', 'TAMOFEN 10MG TAB', '1', '1', 1, '1', 'Single', 't', '{"status_code":"200","data":[],"message":"Obat Berhasil Simpan.."}', '2026-04-07 01:14:08', '00002065', '14250804718', '2026-04-07 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (62, '001155747', '6001', '00001', '0216A01604260000003', 'ASAM FOLAT 1MG TAB', '1', '1', 30, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Signa atau Jumlah Hari tidak sesuai dengan Jumlah Obat."}', '2026-04-08 22:35:34', '00000044', '14250805226', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (63, '001155747', '6001', '00001', '0216A01604260000003', 'ASAM FOLAT 1MG TAB', '1', '1', 30, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Signa atau Jumlah Hari tidak sesuai dengan Jumlah Obat."}', '2026-04-08 22:35:45', '00000044', '14250805226', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (64, '001155747', '6001', '00004', '0216A01604260000003', 'ASAM FOLAT 1MG TAB', '0', '0', 30, '0', 'Single', 'f', '{"status":"gagal","code":"404","message":"Signa atau Jumlah Hari tidak sesuai dengan Jumlah Obat."}', '2026-04-08 23:26:24', '00000044', '14250805226', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (65, '001155747', '6001', '00004', '0216A01604260000003', 'ASAM FOLAT 1MG TAB', '0', '0', 30, '0', 'Single', 'f', '{"status":"gagal","code":"404","message":"Signa atau Jumlah Hari tidak sesuai dengan Jumlah Obat."}', '2026-04-08 23:29:43', '00000044', '14250805226', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (66, '001155747', '6001', '00004', '0216A01604260000003', 'ACETYLCYSTEIN 200MG KAPS', '8', '6', 8, '66', 'Single', 'f', '{"status":"gagal","code":"404","message":"Signa atau Jumlah Hari tidak sesuai dengan Jumlah Obat."}', '2026-04-08 23:29:45', '00005365', '14250805110', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (67, '001155747', '6001', '00004', '0216A01604260000003', 'ASAM FOLAT 1MG TAB', '0', '0', 30, '0', 'Single', 'f', '{"status":"gagal","code":"404","message":"Signa atau Jumlah Hari tidak sesuai dengan Jumlah Obat."}', '2026-04-08 23:38:54', '00000044', '14250805226', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (68, '001155747', '6001', '00004', '0216A01604260000003', 'ACETYLCYSTEIN 200MG KAPS', '8', '6', 8, '66', 'Single', 'f', '{"status":"gagal","code":"404","message":"Signa atau Jumlah Hari tidak sesuai dengan Jumlah Obat."}', '2026-04-08 23:38:54', '00005365', '14250805110', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (69, '001155747', '6001', '00004', '0216A01604260000003', 'ASAM FOLAT 1MG TAB', '0', '0', 30, '0', 'Single', 'f', '{"status":"gagal","code":"404","message":"Signa atau Jumlah Hari tidak sesuai dengan Jumlah Obat."}', '2026-04-08 23:40:06', '00000044', '14250805226', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (70, '001155747', '6001', '00004', '0216A01604260000003', 'ACETYLCYSTEIN 200MG KAPS', '8', '6', 8, '66', 'Single', 'f', '{"status":"gagal","code":"404","message":"Signa atau Jumlah Hari tidak sesuai dengan Jumlah Obat."}', '2026-04-08 23:40:06', '00005365', '14250805110', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (71, '001155747', '6001', '00004', '0216A01604260000003', 'ASAM FOLAT 1MG TAB', '9', '9', 30, '90', 'Single', 'f', '{"status":"gagal","code":"404","message":"Signa atau Jumlah Hari tidak sesuai dengan Jumlah Obat."}', '2026-04-09 00:03:40', '00000044', '14250805226', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (72, '001155747', '6001', '00004', '0216A01604260000003', 'ACETYLCYSTEIN 200MG KAPS', '1', '1', 10, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Signa atau Jumlah Hari tidak sesuai dengan Jumlah Obat."}', '2026-04-09 00:03:41', '00005365', '14250805110', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (73, '001155747', '6001', '00004', '0216A01604260000003', 'ASAM FOLAT 1MG TAB', '0', '0', 30, '0', 'Single', 'f', '{"status":"gagal","code":"404","message":"Signa atau Jumlah Hari tidak sesuai dengan Jumlah Obat."}', '2026-04-09 01:08:42', '00000044', '14250805226', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (74, '001155747', '6001', '00004', '0216A01604260000003', 'ASAM FOLAT 1MG TAB', '0', '0', 30, '0', 'Single', 'f', '{"status":"gagal","code":"404","message":"Signa atau Jumlah Hari tidak sesuai dengan Jumlah Obat."}', '2026-04-09 01:09:08', '00000044', '14250805226', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (75, '001155747', '6001', '00004', '0216A01604260000003', 'ASAM FOLAT 1MG TAB', '0', '0', 1, '0', 'Single', 'f', '{"status":"gagal","code":"404","message":"Signa atau Jumlah Hari tidak sesuai dengan Jumlah Obat."}', '2026-04-09 01:10:37', '00000044', '14250805226', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (76, '001155747', '6001', '00004', '0216A01604260000003', 'ASAM FOLAT 1MG TAB', '2', '1', 1, '20', 'Single', 'f', '{"status":"gagal","code":"404","message":"Signa atau Jumlah Hari tidak sesuai dengan Jumlah Obat."}', '2026-04-09 01:13:53', '00000044', '14250805226', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (77, '001155747', '6001', '00004', '0216A01604260000003', 'ACETYLCYSTEIN 200MG KAPS', '1', '2', 8, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Signa atau Jumlah Hari tidak sesuai dengan Jumlah Obat."}', '2026-04-09 01:13:54', '00005365', '14250805110', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (78, '001155747', '6001', '00004', '0216A01604260000003', 'ASAM FOLAT 1MG TAB', '1', '1', 12, '1', 'Single', 'f', '{"status":"gagal","code":"404","message":"Signa atau Jumlah Hari tidak sesuai dengan Jumlah Obat."}', '2026-04-09 01:50:08', '00000044', '14250805226', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (88, '001155747', '6001', '00004', '0216A01604260000003', 'ACETYLCYSTEIN 200MG KAPS', '1', '21', 8, '1', 'Puyer 2', 'f', '{"status":"gagal","code":"201","message":"300 - Obat Beirisan dengan pemakaian obat Tgl Resep 08-04-2026"}', '2026-04-09 02:29:06', '00005365', '14250805110', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (80, '001155747', '6001', '00004', '0216A01604260000003', 'ACETYLCYSTEIN 200MG KAPS', '2', '1', 8, '1', 'Puyer 2', 'f', '{"status_code":"500","message":"22 : The requested URL returned error: 404"}', '2026-04-09 01:56:15', '00005365', '14250805110', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (81, '001155747', '6001', '00004', '0216A01604260000003', 'ACETYLCYSTEIN 200MG KAPS', '2', '1', 8, '1', 'Puyer 2', 'f', '{"status_code":"500","message":"22 : The requested URL returned error: 404"}', '2026-04-09 01:59:11', '00005365', '14250805110', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (82, '001155747', '6001', '00004', '0216A01604260000003', 'ACETYLCYSTEIN 200MG KAPS', '2', '1', 1, '1', 'Puyer 2', 'f', '{"status_code":"200","data":[],"message":"Obat Berhasil Simpan.."}', '2026-04-09 02:01:45', '00005365', '14250805110', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (83, '001155747', '6001', '00004', '0216A01604260000003', 'ACETYLCYSTEIN 200MG KAPS', '2', '2', 10, '1', 'Puyer 2', 'f', '{"status":"gagal","code":"201","message":"300 - Obat Beirisan dengan pemakaian obat Tgl Resep 08-04-2026"}', '2026-04-09 02:20:56', '00005365', '14250805110', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (84, '001155747', '6001', '00004', '0216A01604260000003', 'ACETYLCYSTEIN 200MG KAPS', '1', '1', 8, '1', 'Puyer 2', 'f', '{"status":"gagal","code":"201","message":"300 - Obat Beirisan dengan pemakaian obat Tgl Resep 08-04-2026"}', '2026-04-09 02:22:31', '00005365', '14250805110', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (85, '001155747', '6001', '00004', '0216A01604260000003', 'ACETYLCYSTEIN 200MG KAPS', '1', '21', 8, '1', 'Puyer 2', 'f', '{"status":"gagal","code":"201","message":"300 - Obat Beirisan dengan pemakaian obat Tgl Resep 08-04-2026"}', '2026-04-09 02:26:53', '00005365', '14250805110', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (89, '001155747', '6001', '00004', '0216A01604260000003', 'ASAM FOLAT 1MG TAB', '1', '1', 30, '20', 'Single', 'f', '{"status":"gagal","code":"404","message":"Signa atau Jumlah Hari tidak sesuai dengan Jumlah Obat."}', '2026-04-09 02:29:33', '00000044', '14250805226', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (86, '001155747', '6001', '00004', '0216A01604260000003', 'ACETYLCYSTEIN 200MG KAPS', '1', '21', 8, '1', 'Puyer 2', 'f', '{"status":"gagal","code":"201","message":"300 - Obat Beirisan dengan pemakaian obat Tgl Resep 08-04-2026"}', '2026-04-09 02:27:48', '00005365', '14250805110', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (79, '001155747', '6001', '00004', '0216A01604260000003', 'ASAM FOLAT 1MG TAB', '1', '1', 1, '1', 'Single', 'f', '{"status_code":"200","data":[],"message":"Obat Berhasil Simpan.."}', '2026-04-09 01:50:39', '00000044', '14250805226', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (87, '001155747', '6001', '00004', '0216A01604260000003', 'ASAM FOLAT 1MG TAB', '1', '1', 30, '20', 'Single', 'f', '{"status":"gagal","code":"404","message":"Signa atau Jumlah Hari tidak sesuai dengan Jumlah Obat."}', '2026-04-09 02:29:06', '00000044', '14250805226', '2026-04-08 00:00:00', NULL, NULL);
INSERT INTO "public"."apt_bridging_resep_detail" VALUES (90, '001155747', '6001', '00004', '0216A01604260000003', 'ACETYLCYSTEIN 200MG KAPS', '1', '21', 8, '1', 'Puyer 2', 'f', '{"status":"gagal","code":"201","message":"300 - Obat Beirisan dengan pemakaian obat Tgl Resep 08-04-2026"}', '2026-04-09 02:29:33', '00005365', '14250805110', '2026-04-08 00:00:00', NULL, NULL);

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
