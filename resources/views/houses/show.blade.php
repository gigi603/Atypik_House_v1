@extends('layouts.app')
@section('content')
<div class="container list-category">
    <h2>Nos hébergements</h2>
    <div class="row">
        <div class="col-lg-12 col-md-12 mb-4 text-center">
            <div class="card h-100">
                <a href="#"><img class="card-img-top" src="http://placehold.it/700x200" alt=""></a>
                <div class="card-body">
                    <h4 class="card-title">
                        <a href="#">{{$house->title}}</a>
                    </h4>
                    <h5>{{$house->price}}€</h5>
                    <p class="card-text">{{$house->description}}</p>
                </div>
                <div class="card-footer">
                    <small class="text-muted">&#9733; &#9733; &#9733; &#9733; &#9734;</small>
                </div>
            </div>
        </div>
        <!-- <form class="form-horizontal" method="post" action="{{'/houses/'.$house->id}}">
            {{ csrf_field() }}
            {{ method_field("DELETE") }}
            <button>Delete Task</button>
        </form> -->
    </div>   
</div>