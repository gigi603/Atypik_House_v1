@extends('layouts.admin')
@section('content')
<div id="categories">
    <h2>Catégories : </h2>
    @if ($success = Session::get('success'))
        <div class="alert alert-success">
            {{ $success }}
        </div>
    @endif
    @if ($danger = Session::get('danger'))
        <div class="alert alert-danger">
            {{ $danger }}
        </div>
    @endif
    @foreach($categories as $category)
    <div class="table-responsive">
        <table class="table">
            <tbody>
                <tr>
                    <td><a href="{{action('AdminController@proprietescategory', $category['id'])}}">{{$category->category}}</a></td>
                    <td>
                        <a href="{{action('AdminController@proprietescategory', $category['id'])}}" class="btn btn-warning"> propriétés</a>
                        <a href="{{ route('admin.delete_category', $category->id) }}" class="delete btn btn-danger">Supprimer</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @endforeach
    <div class="col-md-10 text-center">
        <a href="{{ route('admin.create_category') }}" class="btn btn-primary">Ajouter une catégorie</a>
    </div>
</div>
@endsection