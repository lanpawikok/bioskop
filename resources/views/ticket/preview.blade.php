@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <!-- Kolom Kiri: Preview Tiket -->
        <div class="col-md-8">
            <h4 class="mb-4">Preview Tiket</h4>
            
            @php
                $seatsArray = is_string($ticket->rows_of_seats) 
                    ? explode(',', $ticket->rows_of_seats) 
                    : (is_array($ticket->rows_of_seats) ? $ticket->rows_of_seats : []);
            @endphp

            @foreach ($seatsArray as $seat)
                <div class="ticket-item mb-3">
                    <div class="ticket-header">
                        <div>
                            <b>{{ $ticket->schedule->cinema->name }}</b>
                        </div>
                        <div>
                            <h5 class="studio-title">STUDIO {{ $ticket->schedule->studio ?? '1' }}</h5>
                        </div>
                    </div>

                    <hr class="separator">
                    
                    <div class="ticket-body">
                        <p class="ticket-title">{{ $ticket->schedule->movie->title }}</p>

                        <div class="ticket-details">
                            <small>Tanggal:</small>{{ \Carbon\Carbon::parse($ticket->ticketPayment->booked_date)->format('d F, Y') }}<br>
                            <small>Waktu:</small>{{ \Carbon\Carbon::parse($ticket->schedule->hour)->format('H:i') }}<br>
                            <small>Kursi:</small>{{ trim($seat) }}<br>
                            <small>Harga:</small>Rp. {{ number_format($ticket->schedule->price, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Kolom Kanan: Tombol Download -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Unduh Tiket</h5>
                    <p class="card-text">Klik tombol di bawah untuk mengunduh tiket dalam format PDF</p>
                    <a href="{{ route('tickets.download.pdf', $ticket->id) }}" class="btn btn-primary">
                        UNDUH PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .ticket-item {
        width: 100%;
        max-width: 400px;
        padding: 18px 22px;
        border: 2px solid #333;
        border-radius: 8px;
        background: white;
    }

    .studio-title {
        margin: 0;
    }

    .separator {
        margin: 10px 0;
        height: 1px;
        border: none;
        background: rgba(0, 0, 0, 0.2);
    }

    .ticket-title {
        margin: 0 0 8px 0;
        font-weight: bold;
        font-size: 16px;
    }

    .ticket-details {
        font-size: 14px;
        line-height: 1.6;
    }

    .ticket-details small {
        font-weight: bold;
        display: inline-block;
        width: 80px;
    }
</style>
@endsection