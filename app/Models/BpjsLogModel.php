<?php

namespace App\Models;

use CodeIgniter\Model;

class BpjsLogModel extends Model
{
    protected $table      = 'bpjs_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'endpoint', 'method', 'request_header', 'request_body', 'response_code', 'response_body', 'response_message', 'iduser'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // Tidak ada update field
    protected $deletedField  = ''; // Tidak ada soft delete


    public function getDashboardTodayLama()
    {
        return $this->db->query("
            WITH base AS (
                SELECT 
                    ID,
                    created_at,
                    CASE
                        WHEN endpoint ILIKE'%obatnonracikan%' THEN 'OBAT NON RACIKAN' 
                        WHEN endpoint ILIKE'%obatracikan%' THEN 'OBAT RACIKAN' ELSE'LAINNYA' 
                    END AS jenis_endpoint,
                    response_code,
                    response_message,
                    request_body :: JSON ->> 'NOSJP' AS nosjp,
                    request_body :: JSON ->> 'NORESEP' AS noresep 
                FROM
                    bpjs_logs 
                WHERE
                    created_at :: DATE = CURRENT_DATE 
                    AND ( endpoint ILIKE'%obatnonracikan%' OR endpoint ILIKE'%obatracikan%' ) 
                ),
                final_status AS (
                SELECT
                    jenis_endpoint,
                    noresep,
                    CASE-- minimal ada success
                        
                        WHEN BOOL_OR( response_code = 200 ) THEN
                        'SUCCESS' -- warning BPJS
                        
                        WHEN BOOL_OR( response_code IN ( 201, 204 ) ) THEN
                        'WARNING' -- semua gagal
                        
                        WHEN BOOL_AND( response_code IN ( 404, 405, 500 ) ) THEN
                        'FAILED' ELSE'PENDING' 
                    END AS status 
                FROM
                    base 
                GROUP BY
                    jenis_endpoint,
                    noresep 
                ) SELECT
                jenis_endpoint,
                COUNT ( * ) AS total_resep,
                COUNT ( * ) FILTER ( WHERE status = 'SUCCESS' ) AS success,
                COUNT ( * ) FILTER ( WHERE status = 'WARNING' ) AS warning,
                COUNT ( * ) FILTER ( WHERE status = 'FAILED' ) AS failed,
                COUNT ( * ) FILTER ( WHERE status = 'PENDING' ) AS pending,
                ROUND(
                    ( COUNT ( * ) FILTER ( WHERE status = 'SUCCESS' ) :: NUMERIC / NULLIF ( COUNT ( * ), 0 ) ) * 100,
                    2 
                ) AS success_rate 
            FROM
                final_status 
            GROUP BY
                jenis_endpoint 
            ORDER BY
                jenis_endpoint
        ")->getResultArray();
    }

    public function getDashboardToday()
    {
        return $this->db->query("
            WITH base AS (
                SELECT
                    id,
                    created_at,
                    CASE
                        WHEN endpoint LIKE 'https://apijkn.bpjs-kesehatan.go.id/apotek-rest/obatnonracikan%' THEN 'OBAT NON RACIKAN'
                        WHEN endpoint LIKE 'https://apijkn.bpjs-kesehatan.go.id/apotek-rest/obatracikan%' THEN 'OBAT RACIKAN'
                        ELSE 'LAINNYA'
                    END AS jenis_endpoint,
                    response_code,
                    request_body,
                    response_message
                FROM bpjs_logs
                WHERE created_at >= CURRENT_DATE
                  AND created_at < CURRENT_DATE + INTERVAL '1 day'
                  AND (
                        endpoint LIKE 'https://apijkn.bpjs-kesehatan.go.id/apotek-rest/obatnonracikan%'
                     OR endpoint LIKE 'https://apijkn.bpjs-kesehatan.go.id/apotek-rest/obatracikan%'
                  )
            ),

            parsed AS (
                SELECT
                    jenis_endpoint,
                    response_code,
                    response_message,
                    request_body::json->>'NORESEP' AS noresep
                FROM base
            ),

            final_status AS (
                SELECT
                    jenis_endpoint,
                    noresep,

                    CASE
                        WHEN BOOL_OR(response_code = 200) THEN 'SUCCESS'
                        WHEN BOOL_OR(response_code IN (201, 204)) THEN 'WARNING'
                        WHEN BOOL_AND(response_code IN (404, 405, 500)) THEN 'FAILED'
                        ELSE 'PENDING'
                    END AS status
                FROM parsed
                GROUP BY jenis_endpoint, noresep
            )

            SELECT
                jenis_endpoint,
                COUNT(*) AS total_resep,
                COUNT(*) FILTER (WHERE status = 'SUCCESS') AS success,
                COUNT(*) FILTER (WHERE status = 'WARNING') AS warning,
                COUNT(*) FILTER (WHERE status = 'FAILED') AS failed,
                COUNT(*) FILTER (WHERE status = 'PENDING') AS pending,
                ROUND(
                    ( COUNT ( * ) FILTER ( WHERE status = 'SUCCESS' ) :: NUMERIC / NULLIF ( COUNT ( * ), 0 ) ) * 100,
                    2 
                ) AS success_rate 
            FROM final_status
            GROUP BY jenis_endpoint
            ORDER BY jenis_endpoint;
        ")->getResultArray();
    }
}