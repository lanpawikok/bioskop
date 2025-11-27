<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Promo;
use App\Models\TicketPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //tiket aktif :sudah di bayar dan berlaku di hari ini atau besok
        //tiket sesuai dengan milik akun yang login
        $ticketActive = Ticket::whereHas('ticketPayment', function($q){
            $date =  now()->format('Y-m-d');
            $q->whereDate('paid_date', '>=', $date);
        })->where('user_id', Auth::user()->id)->get();
        //ticket tidak aktif : sudah di bayar dan berlaku di hari kemarin (terlewat)
        //tiket sesuai dengan milik akun yang login
        $ticketNonActive = Ticket::whereHas('ticketPayment', function($q){
            $date =  now()->format('Y-m-d');
            $q->whereDate('paid_date', '<', $date);
        })->where('user_id', Auth::user()->id)->get();
        return view('ticket.index',compact('ticketActive', 'ticketNonActive'));
    }

    public function chart() {
        //ambil data tanggal unutuk sumbu x dan jumlah tiket untuk sumbu y
        $tickets = Ticket::whereHas('ticketPayment',function($q) {
            // ambil yang paid_date nya uda bukan (<>) null (uda dibayar)
            $q->where('paid_date','<>',NULL);
        })->get()->groupBy(function($ticket) {
            //groupBy : mengelompokan data tiket berdasarkan tgj byran, untuk dihitung jumkah tiket di tiap tgl nya
            return \Carbon\Carbon::parse($ticket->ticketPayment->paid_date)->format('Y-m-d');
        })->toArray();//toArray() : data di sajikan dalam bentuk array agar bisa menggunakan fungsi' array
        $labels =  array_keys($tickets);//array_keys() : ambil index array
        $data = [];
        //sumbu y mengambil jumkah value bukan isi value,di gunakan count() untuk ambil jumlah valuenya
        foreach ($tickets as $value) {
            //simpan jumlah value ke array diatas
            array_push($data, count($value));
        }
        // dd($tickets);
        // diproses lewat js, jd gunakan response()->json()
        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
public function createQrcode(Request $request) {
    $request->validate([
        'ticket_id' => 'required',
        'promo_id' => 'nullable'
    ]);

    $ticket = Ticket::findOrFail($request->ticket_id);
    $kodeQr = 'TICKET-' . $ticket->id;

    // Generate QR Code
    $qrcode = QrCode::format('svg')->size(200)->margin(2)->generate($kodeQr);

    // Simpan di storage/public/qrcode
    $filename = $kodeQr . '-' . time() . '.svg'; // nama file unik
    $folder = 'qrcode/' . $filename;
    Storage::disk('public')->put($folder, $qrcode);

    // Buat atau update TicketPayment
    $ticketPayment = TicketPayment::updateOrCreate(
        ['ticket_id' => $ticket->id],
        [
            'qrcode' => $folder,
            'booked_date' => now(),
            'status' => 'process'
        ]
    );

    // Update promo jika ada
    if ($request->promo_id) {
        $promo = Promo::find($request->promo_id);
        if ($promo) {
            $discount = $promo->type === 'percent' 
                ? $ticket->total_price * $promo->discount / 100 
                : $promo->discount;

            $ticket->update([
                'total_price' => $ticket->total_price - $discount,
                'promo_id' => $promo->id
            ]);
        }
    }

    // Return path QR Code agar frontend bisa langsung menampilkan
    return response()->json([
        'message' => 'Berhasil membuat QR Code dan update tiket!',
        'ticket' => $ticket,
        'qrcode_url' => asset('storage/' . $folder)
    ]);
}


    /**
     * Store a newly created resource in storage.
     */
        public function store(Request $request)
        {
            $request->validate([
                'user_id' => 'required',
                'schedule_id' => 'required',
                'rows_of_seats' => 'required|array',
                'quantity' => 'required',
                'total_price' => 'required',
                'tax' => 'required',
                'hour' => 'required',
            ]);

            $createData = Ticket::create([
                'user_id' => $request->user_id,
                'schedule_id' => $request->schedule_id,
                'rows_of_seats' => json_encode($request->rows_of_seats), // <-- FIX
                'quantity' => $request->quantity,
                'total_price' => $request->total_price,
                'tax' => $request->tax,
                'hour' => $request->hour,
                'date' => now(),
                'actived' => 0,
            ]);

            return response()->json([
                'message' => 'Berhasil Membuat Data Ticket',
                'data' => $createData
            ]);
        }


    public function orderPage($ticketId) {
        $ticket = Ticket::where('id', $ticketId)->with(['schedule','schedule.cinema', 'schedule.movie'])->first();
        $promos = Promo::where('actived',1)->get();
        return view('schedules.order',compact('ticket','promos'));
    }

    public function paymentPage($ticketId){
        $ticket = ticket::where('id',$ticketId)->with('ticketPayment','promo')->first();
        // dd($ticket);
        return view('schedules.payment',compact('ticket'));
    }

    public function updateStatusPayment(Request $request, $ticketId) {
        $updateData = TicketPayment::where('ticket_id', $ticketId)->update([
            'status' => 'paid-of',
            'paid_date' => now()
        ]);

        if($updateData) {
            Ticket::where('id',$ticketId)->update(['actived'=> 1]);
        }
        return redirect()->route('tickets.payment.proof',$ticketId);
    }

public function proofPayment($ticketId)
{
    $ticket = Ticket::with(['schedule.cinema', 'schedule.movie', 'ticketPayment'])
                    ->findOrFail($ticketId);
    
    return view('ticket.proof-payment', compact('ticket'));
}

public function exportPdf($ticketId)
{
    $ticket = Ticket::with(['schedule.cinema', 'schedule.movie', 'ticketPayment'])
                    ->findOrFail($ticketId);
    
    // PENTING: Ganti 'ticket.export-pdf' sesuai lokasi file
    $pdf = PDF::loadView('ticket.export-pdf', compact('ticket'));
    
    return $pdf->download('ticket-' . $ticket->id . '.pdf');
}

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        //
    }

}
