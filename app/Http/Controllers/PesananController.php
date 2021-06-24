<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Menu;
use Illuminate\Support\Carbon;

class PesananController extends Controller
{
    public function index(){
        $pesanan = Pesanan::where('id_user', auth()->user()->id)->where('status', 0)->first();

        if(empty($pesanan)){
            return response()->json([
                'message' => 'Tidak ada pesanan'
            ], 401);
        }else{
            
            $pesanan_detail = PesananDetail::join('pesanans', 'pesanan_details.id_pesanan', '=', 'pesanans.id')
                                            ->join('menus', 'pesanan_details.id_menu', '=', 'menus.id')
                                            ->where('id_pesanan', $pesanan->id)->get();
            return response()->json([
                'pesanan' => $pesanan,
                'pesanan_detail' => $pesanan_detail
            ], 200);
        }
    }

    public function pesanan($id, Request $request){

        $request->validate([
            'jumlah' => 'required'
        ],[
            'jumlah.required' => 'jumlah tidak boleh kosong'
        ]);

        $tanggal = Carbon::now();
        
        $cek_idMenu = PesananDetail::where('id_menu', $id)->first();

        if(!$cek_idMenu){
            return response()->json([
                'message' => 'id menu '.$id.' tidak ada'
            ], 401);   
        }else{
            $cek_pesanan = Pesanan::where('id_user', auth()->user()->id)->where('status', 0)->first();
            if(empty($cek_pesanan)){
                $pesanan = new Pesanan();
                $pesanan->id_user = auth()->user()->id;
                $pesanan->status = 0;
                $pesanan->total_harga = 0;
                $pesanan->tanggal = $tanggal;
                $pesanan->save();
            }

            $menu = Menu::where('id', $id)->first();
            $pesanan_ready = Pesanan::where('id_user', auth()->user()->id)->where('status', 0)->first();
            $cek_pesanan_detail = PesananDetail::where('id_pesanan', $pesanan_ready->id)->where('id_menu', $id)->first();
            
            if(empty($cek_pesanan_detail)){
                $pesanan_detail = new PesananDetail();
                $pesanan_detail->id_pesanan = $pesanan_ready->id;
                $pesanan_detail->id_menu = $id;
                $pesanan_detail->jumlah = $request->jumlah;
                $pesanan_detail->jumlah_harga = $menu->harga * $request->jumlah;
                $pesanan_detail->save();
            }else{
                $cek_pesanan_detail->jumlah = $cek_pesanan_detail->jumlah + $request->jumlah;
                $cek_pesanan_detail->jumlah_harga = $cek_pesanan_detail->jumlah_harga + ($menu->harga * $request->jumlah);
                $cek_pesanan_detail->save();
            }

            $total_harga = PesananDetail::where('id_pesanan', $pesanan_ready->id)->get()->sum('jumlah_harga');
            $pesanan_ready->total_harga = $total_harga;
            $pesanan_ready->save();

            return response()->json([
                'message' => 'Pesanan berhasil ditambahkan'
            ], 200);
        }
    }
    public function hapusPesanan($id){
        $pesanan = Pesanan::where('id_user', auth()->user()->id)->where('status', 0)->first();
        $pesanan_detail = PesananDetail::where('id_pesanan', $pesanan->id)->where('id_menu', $id)->first();
        if(!$pesanan_detail){
            return response()->json([
                'message' => 'Pesanan dengan id '.$id.' tidak ada'
            ], 401);
        }else{
            $pesanan_detail->delete();
            $total_harga = PesananDetail::where('id_pesanan', $pesanan->id)->get()->sum('jumlah_harga');
            $pesanan->total_harga = $total_harga;
            $pesanan->save();

            $hapus_pesanan = PesananDetail::where('id_pesanan', $pesanan->id)->get();
            if(count($hapus_pesanan) == 0){
                $pesanan->delete();
            }

            return response()->json([
                'message' => 'Pesanan berhasil dihapus'
            ], 200);
        }
    }
    public function buatPesanan(){
        $pesanan = Pesanan::where('id_user', auth()->user()->id)->where('status', 0)->first();
        $pesanan->status = 1;
        $pesanan->save();

        return response()->json([
            'message' => 'Pesanan telah dibuat'
        ], 200);
    }
}
