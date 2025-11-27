<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pembayaran Tiket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }

        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
            color: white;
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .page-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .content-wrapper {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            align-items: start;
        }

        @media (max-width: 992px) {
            .content-wrapper {
                grid-template-columns: 1fr;
            }
        }

        /* Ticket Cards Section */
        .tickets-section {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .ticket-item {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .ticket-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }

        .ticket-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }

        .ticket-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px dashed #e0e0e0;
        }

        .cinema-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cinema-name i {
            color: #667eea;
        }

        .studio-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
        }

        .ticket-body {
            padding: 20px 0;
        }

        .movie-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .movie-title i {
            color: #ffc107;
        }

        .ticket-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 10px;
            transition: background 0.3s ease;
        }

        .detail-item:hover {
            background: #e9ecef;
        }

        .detail-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .detail-content {
            flex: 1;
        }

        .detail-label {
            font-size: 0.75rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .detail-value {
            font-size: 1rem;
            color: #333;
            font-weight: 600;
        }

        /* Download Card */
        .download-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            position: sticky;
            top: 20px;
            text-align: center;
        }

        .download-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .download-icon i {
            font-size: 3rem;
            color: white;
        }

        .download-card h5 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }

        .download-card p {
            color: #6c757d;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .btn-download {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-download:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.5);
            color: white;
        }

        .btn-download i {
            font-size: 1.2rem;
        }

        .features-list {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px dashed #e0e0e0;
            text-align: left;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            color: #6c757d;
        }

        .feature-item i {
            color: #667eea;
            font-size: 1.1rem;
        }

        /* Success Badge */
        .success-badge {
            background: #d4edda;
            color: #155724;
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
        }

        .success-badge i {
            font-size: 1.2rem;
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .ticket-item {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .ticket-item:nth-child(2) {
            animation-delay: 0.1s;
        }

        .ticket-item:nth-child(3) {
            animation-delay: 0.2s;
        }

        .download-card {
            animation: fadeInUp 0.6s ease-out 0.3s forwards;
            opacity: 0;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-ticket-alt"></i> Bukti Pembayaran</h1>
            <p>Tiket Anda telah berhasil dibeli dan siap digunakan</p>
        </div>

        <!-- Success Badge -->
        <div class="text-center mb-4">
            <div class="success-badge">
                <i class="fas fa-check-circle"></i>
                <span>Pembayaran Berhasil!</span>
            </div>
        </div>

        <div class="content-wrapper">
            <!-- Tickets Section -->
            <div class="tickets-section">
                @php
                    $seatsArray = is_string($ticket->rows_of_seats) 
                        ? explode(',', $ticket->rows_of_seats) 
                        : (is_array($ticket->rows_of_seats) ? $ticket->rows_of_seats : []);
                @endphp

                @foreach ($seatsArray as $seat)
                    <div class="ticket-item">
                        <div class="ticket-header">
                            <div class="cinema-name">
                                <i class="fas fa-film"></i>
                                {{ $ticket->schedule->cinema->name }}
                            </div>
                            <div class="studio-badge">
                                STUDIO {{ $ticket->schedule->studio ?? '1' }}
                            </div>
                        </div>

                        <div class="ticket-body">
                            <div class="movie-title">
                                <i class="fas fa-star"></i>
                                {{ $ticket->schedule->movie->title }}
                            </div>

                            <div class="ticket-details">
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                    <div class="detail-content">
                                        <div class="detail-label">Tanggal</div>
                                        <div class="detail-value">{{ \Carbon\Carbon::parse($ticket->ticketPayment->booked_date)->format('d F, Y') }}</div>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="detail-content">
                                        <div class="detail-label">Waktu</div>
                                        <div class="detail-value">{{ \Carbon\Carbon::parse($ticket->schedule->hour)->format('H:i') }}</div>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-couch"></i>
                                    </div>
                                    <div class="detail-content">
                                        <div class="detail-label">Kursi</div>
                                        <div class="detail-value">{{ trim($seat) }}</div>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div class="detail-content">
                                        <div class="detail-label">Harga</div>
                                        <div class="detail-value">Rp. {{ number_format($ticket->schedule->price, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Download Card -->
            <div class="download-card">
                <div class="download-icon">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <h5>Unduh Tiket PDF</h5>
                <p>Simpan tiket Anda dalam format PDF untuk kemudahan akses dan cetak</p>
                
                <a href="{{ route('tickets.export_pdf', $ticket->id) }}" class="btn-download">
                    <i class="fas fa-download"></i>
                    <span>UNDUH PDF</span>
                </a>

                <div class="features-list">
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Dapat disimpan secara offline</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>