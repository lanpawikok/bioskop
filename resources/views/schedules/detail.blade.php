@extends('templates.app')

@section('content')
<div class="container pt-5">
    <div class="w-75 d-block m-auto">
        <div class="d-flex">
            <div style="width: 150px; height: 200px;">
                <img src="{{ asset('storage/' . ($movie->poster ?? '')) }}" alt="{{ $movie->title ?? '' }}" class="w-100 h-100" style="object-fit: cover;">
            </div>
            <div class="ms-5 mt-4">
                <h5>{{ $movie->title ?? '-' }}</h5>
                <table>
                    <tr>
                        <td><b class="text-secondary">Genre</b></td>
                        <td class="px-3">:</td>
                        <td>{{ $movie->genre ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><b class="text-secondary">Durasi</b></td>
                        <td class="px-3">:</td>
                        <td>{{ $movie->duration ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><b class="text-secondary">Sutradara</b></td>
                        <td class="px-3">:</td>
                        <td>{{ $movie->director ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><b class="text-secondary">Rating Usia</b></td>
                        <td class="px-3">:</td>
                        <td><span class="badge badge-danger">{{ $movie['age_rating'] }}+</span></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="w-100 row mt-3">
            <div class="col-6 pe-5">
                <div class="d-flex flex-column justify-content-end align-items-end">
                    <div class="d-flex align-items-center">
                        <h3 class="text-warning me-2">9.2</h3>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                    </div>
                    <small>5.555 Vote</small>
                </div>
            </div>
            <div class="col-6 ps-5" style="border-left: 2px solid #c7c7c7;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-heart text-danger me-2"></i>
                    <b>Masukan watchlist</b>
                </div>
                <small>11.000 Orang</small>
            </div>
        </div>

        <!-- FILTER BIOSKOP & SORTIR -->
        <div class="d-flex w-100 bg-light mt-3 mb-4 px-3 py-3 rounded-3 gap-3">
            <div class="dropdown w-25">
                <button class="btn btn-light w-100 text-start dropdown-toggle" type="button" id="dropdownMenuButton"
                    data-mdb-dropdown-init data-mdb-ripple-init aria-expanded="false">
                    Bioskop
                </button>
                <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton">
                    @foreach ($movie->schedules ?? [] as $schedule)
                        <li><a class="dropdown-item" h  ref="#">{{ $schedule->cinema->name ?? '-' }}</a></li>
                    @endforeach
                </ul>
            </div>
            @php 
            if (request()->get('sort_price') == 'ASC') {
                $sortPrice = 'DESC';
            } elseif (request()->get('sort_price') == 'DESC') {
                $sortPrice = 'ASC';
            } else {
                $sortPrice = 'ASC';
            }

            if (request()->get('sort_alfabet') == 'ASC') {
                $sortAlfabet = 'DESC';
            } elseif (request()->get('sort_alfabet') == 'DESC') {
                $sortAlfabet = 'ASC';
            } else {
                $sortAlfabet = 'ASC';
            }
            @endphp
            <div class="dropdown w-25">
                <button class="btn btn-light w-100 text-start dropdown-toggle" type="button" id="dropdownMenuButton2"
                    data-mdb-dropdown-init data-mdb-ripple-init aria-expanded="false">
                    Sortir
                </button>
                <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton2">
                    <li><a class="dropdown-item" href="?sort_price={{$sortPrice}}">Harga</a></li>
                    <li><a class="dropdown-item" href="?sort_alfabet={{$sortAlfabet}}">Alfabet</a></li>
                </ul>
            </div>
        </div>
        <!-- END FILTER -->

        <div class="mb-5">
            @foreach ($movie->schedules ?? [] as $schedule)
                <div class="w-100 my-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            {{--kanan--}}
                            <i class="fa-solid fa-building"></i><b class="ms-2">{{ $schedule['cinema']['name'] }}</b>
                            <br>
                            <small class="ms-3">{{ $schedule['cinema']['location']}}</small>
                        </div>
                        <div>
                            {{--kiri--}}
                            <b class="">Rp {{ number_format($schedule->price ?? 0, 0, ',', '.') }}</b>
                        </div>
                    </div>

                    <div class="d-flex gap-3 ps-3 my-2">
                            @foreach ($schedule->hours as $index => $hour)
                            <!--argumen pada fungsi selectedHours
                             1.$schedule->id : mengambil detail schedule yang akan di beli
                             2.$index : mengambil index dari array hours untuk mengtahui jam berapa tiket akan di gunakan-->
                                <div class="btn btn-outline-secondary" style="cursor: pointer" onclick="selectedHour
                                ('{{$schedule->id}}','{{$index}}', this)">{{ $hour }}</div>
                            @endforeach

                    </div>
                </div>
                <hr>
            @endforeach
        </div>

        <div class="w-100 p-2 bg-light text-center fixed-bottom" id="wrapBtn">
           {{--javascript:void(0) : nonaktif href--}}
         <a href="javascript:void(0)" id="btnOrder"><i class="fa-solid fa-ticket"></i> BELI TIKET</a>
        </div>
    </div>
</div>
@endsection

@push ('script')
 <script>
    let selectedScheduleId = null;
    let selectedHourIndex = null;
    let lastClicked = null;

    function selectedHour(scheduleId, hourIndex, el) {
        selectedScheduleId = scheduleId;
        selectedHourIndex = hourIndex;

        //ubah kotak yang di klik
        //el diambil dari parameter  fungsi dengan nilai argumen this di html nya
        if(lastClicked) {
            lastClicked.style.backgroundColor = "";
            lastClicked.style.color = "";
            lastClicked.style.borderColor = "";
        }

        el.style.backgroundColor = "#112646";
        el.style.color = "white";
        el.style.borderColor = "#112646";

        lastClicked = el;

        let wrapBtn = document.querySelector("#wrapBtn");
        //hapus class (classlist.rmove)
        wrapBtn.classList.remove("bg-light");
        wrapBtn.style.backgroundColor = '#112646';
        //memanggil route web.php di JS
        //.replace() mengganti./mengisi path dinamis {scheduleId} di web.php
        let url = "{{route('schedules.show_seats',['scheduleId' => ':scheduleId', 'hourId' => ':hourId']) }}".replace(':scheduleId',scheduleId).replace(':hourId',hourIndex);
        let btnOrder = document.querySelector("#btnOrder");
        btnOrder.href = url;
        btnOrder.style.color = 'white';
    }
 </script>
@endpush