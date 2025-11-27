{{--import templates--}}
@extends('templates.app')

@section('content')
@if(session('message'))
<div class="alert alert-success">{{ session('success') }}<b> Selamat datang, {{ auth()->user()->name }}</b></div>
@endif
@if(session('logout'))
<div class="alert alert-warning">{{ session('logout') }}</div>
@endif

<div class="dropdown">
  <button
    class="btn btn-light w-100 text-start dropdown-toggle"
    type="button"
    id="dropdownMenuButton"
    data-mdb-dropdown-init
    data-mdb-ripple-init
    aria-expanded="false">
  <i class="fa-solid fa-location-dot"></i> Bogor

  </button>
  <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton">
    <li><a class="dropdown-item" href="#">Jakarta timur</a></li>
    <li><a class="dropdown-item" href="#">Jakarta selatan</a></li>
    <li><a class="dropdown-item" href="#">Depok</a></li>
  </ul>
</div>

{{--slide--}}
<!-- Carousel wrapper -->
<div id="carouselBasicExample" class="carousel slide carousel-fade" data-mdb-ride="carousel" data-mdb-carousel-init>
    <!-- Indicators -->
    <div class="carousel-indicators">
      <button
        type="button"
        data-mdb-target="#carouselBasicExample"
        data-mdb-slide-to="0"
        class="active"
        aria-current="true"
        aria-label="Slide 1"
      ></button>
      <button
        type="button"
        data-mdb-target="#carouselBasicExample"
        data-mdb-slide-to="1"
        aria-label="Slide 2"
      ></button>
      <button
        type="button"
        data-mdb-target="#carouselBasicExample"
        data-mdb-slide-to="2"
        aria-label="Slide 3"
      ></button>
    </div>
  
    <!-- Inner -->
    <div class="carousel-inner">
      <!-- Single item -->
      <div class="carousel-item active">
        <img style="height: 550px;" src="https://www.whatsoninourbackyard.com.au/wp-content/uploads/2025/09/Superman-LookUp-Banner-1.png" class="d-block w-100" alt="Sunset Over the City"/>
        <div class="carousel-caption d-none d-md-block">
          <h5>SUPERMAN</h5>
          <p></p>
        </div>
      </div>
  
      <!-- Single item -->
      <div class="carousel-item">
        <img style="height: 550px;" src="https://m.media-amazon.com/images/I/71ZhDVz5NcL._AC_UF894,1000_QL80_.jpg" class="d-block w-100" alt="Canyon at Nigh"/>
        <div class="carousel-caption d-none d-md-block">
          <h5>AGAK LAEN</h5>
          <p></p>
        </div>
      </div>
  
      <!-- Single item -->
      <div class="carousel-item">
        <img style="height: 550px;" src="https://preview.redd.it/uq7ap0i4vh241.jpg?width=640&crop=smart&auto=webp&s=c4d23827cae2f08c5a1f3c0fa14648770ec59e67" class="d-block w-100" alt="Cliff Above a Stormy Sea"/>
        <div class="carousel-caption d-none d-md-block">
          <h5>STAR WARS</h5>
          <p></p>
        </div>
      </div>
    </div>
    <!-- Inner -->
  
    <!-- Controls -->
    <button class="carousel-control-prev" type="button" data-mdb-target="#carouselBasicExample" data-mdb-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-mdb-target="#carouselBasicExample" data-mdb-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>
  <!-- Carousel wrapper -->
  <div class="container my-4">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-clapperboard"></i>
            <h5 class="ms-2 mt-2">Sedang tayang</h5>
        </div>
        <div>
            <a href="{{ route('home.movies.all') }}" class="btn btn-warning rounded-pill">Semua</a>
        </div>
    </div>
</div>
 
<div class="container d-flex gap-4  mt-4">
    {{--gap-2 : jarak antar komponen--}}
    <a href="{{ route('home.movies.all') }}" class="btn btn-outline-primary rounded-pill">Semua Film</a>
    <button class="btn btn-outline-secondary rounded-pill">XXI</button>
    <button class="btn btn-outline-secondary rounded-pill">Cinopolis</button>
    <button class="btn btn-outline-secondary rounded-pill">IMAX</button>
  </div>
  
  
    <!-- Card 3 -->
    <div class="d-flex justify-content-center gap-4 mt-4">
    @foreach ($movies as $item)
    <div class="card" style="width: 18rem;">
      <img src="{{ asset('storage/'. $item['poster']) }}" 
           class="card-img-top" 
           alt="Ocean View" 
           style="height:350px; object-fit:cover;" />
      <div class="card-body bg-primary text-warning text-center" style="padding: 0 !important;">
     <p class="card-text mb-2">
          <a href="{{ route('schedules.detail', $item['id']) }}" class="text-warning">Beli Tiket</a>
  </p>
      </div>
    </div>
    @endforeach
  </div>

<footer class="bg-body-tertiary text-center text-lg-start mt-5">
    <!-- Copyright -->
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.05);">
      © 2025 Copyright:
      <a class="text-body" href="https://mdbootstrap.com/">TIXID</a>
    </div>
    <!-- Copyright -->
  </footer>
@endsection