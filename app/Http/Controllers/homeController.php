<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class homeController extends Controller
{
    function index(Request $request)
    {
        if ($request->has('tahun') and $request->tahun != null) {
            $getMenu = Http::get('http://tes-web.landa.id/intermediate/menu');
            $menu = json_decode($getMenu->getBody(), true);

            $tahun = $request->get('tahun');

            $data = Http::get('http://tes-web.landa.id/intermediate/transaksi?tahun=' . $request->get('tahun'));
            $rekap = json_decode($data->getBody(), true);




            $makanan = array();
            $minuman = array();

            foreach ($menu as $m) {
                if ($m['kategori'] == 'makanan') {
                    $bulan = [null, null, null, null, null, null, null, null, null, null, null, null];
                    foreach ($rekap as $r) {
                        if ($r['menu'] == $m['menu']) {
                            $month = (int) date("m", strtotime($r['tanggal']));
                            if (!isset($makanan[$r['menu']])) {
                                $makanan[$r['menu']] = ['menu' => $r['menu'], 'total' => $r['total']];
                            } else {
                                $makanan[$r['menu']]['total'] += $r['total'];
                            }

                            $bulan[$month - 1] += $r['total'];
                        }
                    }
                    $makanan[$m['menu']]['bulan'] = $bulan;
                }
                if ($m['kategori'] == 'minuman') {
                    $bulan = [null, null, null, null, null, null, null, null, null, null, null, null];
                    foreach ($rekap as $r) {
                        if ($r['menu'] == $m['menu']) {
                            $month = (int) date("m", strtotime($r['tanggal']));
                            if (!isset($minuman[$r['menu']])) {
                                $minuman[$r['menu']] = ['menu' => $r['menu'], 'total' => $r['total']];
                            } else {
                                $minuman[$r['menu']]['total'] += $r['total'];
                            }

                            $bulan[$month - 1] += $r['total'];
                        }
                    }
                    $minuman[$m['menu']]['bulan'] = $bulan;
                }
            }

            $allmakanan = array();
            $allminuman = array();

            foreach ($makanan as $key => $m) {
                if (!isset($m['menu'])) {
                    array_push($allmakanan, array('menu' => $key, 'bulan' => $m['bulan'], 'total' => 0));
                } else {
                    array_push($allmakanan, $m);
                }
            }
            foreach ($minuman as $key => $m) {
                if (!isset($m['menu'])) {
                    array_push($allminuman, array('menu' => $key, 'bulan' => $m['bulan'], 'total' => 0));
                } else {
                    array_push($allminuman, $m);
                }
            }

            $total_per_bulan = [null, null, null, null, null, null, null, null, null, null, null, null];

            foreach ($allmakanan as $makanan) {
                foreach ($makanan['bulan'] as $key => $jml) {
                    $total_per_bulan[$key] += $jml;
                }
            }
            foreach ($allminuman as $minuman) {
                foreach ($minuman['bulan'] as $key => $jml) {
                    $total_per_bulan[$key] += $jml;
                }
            }

            dd($total_per_bulan);
            $total = array_sum($total_per_bulan);


            return view('welcome', compact('menu', 'rekap', 'tahun', 'allmakanan', 'allminuman', 'total', 'total_per_bulan'));
        }

        return view('welcome');

    }

}