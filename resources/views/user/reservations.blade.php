@extends('layouts.app')
@section('link')
<link href="{{ asset('css/jquery-ui.min.css') }}" rel="stylesheet">
@section('content')
<div class="container list-category">
    <h2>Mes réservations</h2>
    <div class="row">
    @foreach ($reservations as $reservation)
        <div class="col-lg-4 col-md-4">
            <div class="thumbnail">
                <div class="card h-100">
                    <a href="{{action('UsersController@showHouse', $reservation->house['id'])}}"><img class="img-responsive img_house" src="{{ asset('img/houses/'.$reservation->house->photo) }}"></a>
                    <div>
                        <h4 class="title card-title">
                            <a href="{{route('user.showHouse', $reservation->house['id']) }}">{{$reservation->house->title}}</a>
                        </h4>
                        <p>Type de bien : Logement</p>
                        <p><i class="fas fa-bed"></i> : 2 lits - <i class="fas fa-users"></i> : pour 2 Personnes</p>
                        <p><i class="fas fa-calendar"></i> Début: <?php \Date::setLocale('fr'); $startdate = Date::parse($reservation->start_date)->format('l j F Y'); echo($startdate);?> </p>
                        <p><i class="fas fa-calendar"></i> Fin:  <?php \Date::setLocale('fr'); $enddate = Date::parse($reservation->end_date)->format('l j F Y'); echo($enddate);?></p>
                        <h3 class="price">{{$reservation->house->price}}€</h3>
                        <p class="card-text"><?php echo(substr($reservation->house->description, 0, 40));?></p>
                        @if($reservation->house->statut == "En attente de validation")
                            <p>Statut: <span style="color:red;"><?php echo($reservation->house->statut);?></span></p>
                        @else
                            <p>Statut: <span style="color:green;"><?php echo($reservation->house->statut);?></span></p>
                        @endif
                    </div>
                </div> 
            </div>
        </div>
    @endforeach
    </div>
</div>
@section('script')
<script src="{{ asset('js/jquery.js') }}"></script>
<script src="{{ asset('js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('js/calendar.js') }}"></script>
@endsection