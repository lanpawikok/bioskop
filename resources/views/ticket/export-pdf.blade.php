<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Tiket</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .tickets-wrapper {
            width: 100%;
            max-width: 400px;
        }

        /* style kartu */
        .ticket-item {
            width: 340px;
            padding: 18px 22px;
            border: 2px solid #333;
            border-radius: 8px;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .studio-title {
            margin: 0;
        }

        .separator {
            margin: 10px 0;
            height: 1px;
            border: none;
            background: rgb(0, 0, 0, 0.2);
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
            width: 60px;
        }
    </style>
</head>

<body>
    <div class="tickets-wrapper">
        @php
            // Konversi string seats menjadi array
            $seatsArray = is_string($ticket->rows_of_seats) 
                ? explode(',', $ticket->rows_of_seats) 
                : (is_array($ticket->rows_of_seats) ? $ticket->rows_of_seats : []);
        @endphp

        @foreach ($seatsArray as $seat) 
            <div class="ticket-item">
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
</body>

</html>