<?php

use Liberta\Sql\DB;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '../../tests/print_result/resultJsonDebug.php';
require_once __DIR__ . '../../tests/print_result/result.php';

$db = new DB([
    'driver'   => 'mysql',
    'host'     => 'localhost',
    'database' => 'iproms_properti',
    'username' => 'root',
    'password' => 'root'
]);

$penjualan = $db->table('ar_penjualan')
            ->join('properti', 'ar_penjualan.properti_id', '=', 'properti.properti_id')
            ->join('properti_kawasan', 'ar_penjualan.kawasan_id', '=', 'properti_kawasan.kawasan_id')
            ->join('properti_jalan', 'properti.jalan_id', '=', 'properti_jalan.jalan_id') 
            ->leftJoin('ar_jadwal_detail', 'ar_penjualan.properti_id', '=', 'ar_jadwal_detail.properti_id')     
            ->leftJoin('ar_marketing', 'ar_penjualan.marketing_id', '=', 'ar_marketing.marketing_id')       
            ->leftJoin('properti_pelanggan', 'ar_penjualan.pelanggan_id', '=', 'properti_pelanggan.pelanggan_id')      
            ->select(
                'ar_penjualan.properti_id as properti_id',
                'ar_penjualan.project_id as project_id',
                'ar_penjualan.rekening as rekening',
                'ar_penjualan.tgl_penjualan as tgl_penjualan',
                'ar_penjualan.faktur_id as faktur_id',
                'ar_penjualan.tipe_surat as tipe_surat',
                'ar_penjualan.no_surat as no_surat',
                'ar_penjualan.kawasan_id as kawasan_id',
                'ar_penjualan.pelanggan_id as pelanggan_id',
                'ar_penjualan.kode_kavling as kode_kavling',
                'ar_penjualan.skema_pembayaran as skema_pembayaran',
                'ar_penjualan.nominal as nominal',
                'ar_penjualan.booking_fee as booking_fee',
                'ar_penjualan.tgl_booking_fee as tgl_booking_fee', 
                'ar_penjualan.lama_bfe as lama_bfe', 

                'ar_penjualan.dp as dp',
                'ar_penjualan.tgl_dp as tgl_dp', 
                'ar_penjualan.lama_dp as lama_dp', 

                'ar_penjualan.kode_leasing as kode_leasing',        
                'ar_penjualan.leasing as leasing',
                'ar_penjualan.lama_leasing as lama_leasing',
                'ar_penjualan.tgl_awal_leasing as tgl_awal_leasing',
                'ar_penjualan.tgl_akhir_leasing as tgl_akhir_leasing',

                'ar_penjualan.saldo as saldo',
                'ar_penjualan.tgl_saldo as tgl_saldo',        
                'ar_penjualan.lama_ags as lama_ags',    
                'ar_penjualan.lama as lama',    
                'ar_penjualan.tgl_serah_terima as tgl_serah_terima',   
                'ar_penjualan.posting as posting',

                'properti.no_kavling as no_kavling',
                'properti.harga as harga',  
                'properti.luas_tanah as luas_tanah',
                'properti.luas_bangunan as luas_bangunan',

                'properti_kawasan.kode_surat as kode_surat',     
                'properti_kawasan.kode_kota as kode_kota',         
                'properti_jalan.nama_jalan as nama_jalan',

                'ar_jadwal_detail.faktur_id as jadwal_faktur_id',
                'ar_marketing.nama as nama_marketing',

                'properti_pelanggan.pelanggan_id as pelanggan_id',
                'properti_pelanggan.nama_pelanggan as nama_pelanggan',                
                'properti_pelanggan.alamat as alamat',
            )
            ->where('ar_penjualan.properti_id', 'LIKE', '203010042')
            ->first();

$eol = (php_sapi_name() === 'cli') ? PHP_EOL : '<br>';

printResult($penjualan);
echo ($eol);
echo "Json{$eol}";
printJsonVerticalDebug($penjualan);