@extends('client.layouts.app')
@section('title','Emploi du temps')
@section('content')
<div class="mb-6 flex items-center justify-between gap-3 flex-wrap"><div><h2 class="font-headline-lg text-headline-lg text-primary">Emploi du temps</h2><p class="text-sm text-on-surface-variant">Consultation en lecture seule</p></div><div class="flex gap-2 flex-wrap"><a class="px-4 py-2 border rounded-lg" href="{{ route('client.enseignant') }}">Retour</a><a class="px-4 py-2 border rounded-lg" target="_blank" href="{{ route('client.emploi-temps.teacher.print',$enseignant) }}">Imprimer</a><a class="px-4 py-2 border rounded-lg" href="{{ route('client.emploi-temps.teacher.pdf',$enseignant) }}">Télécharger PDF</a><a class="px-4 py-2 bg-primary text-white rounded-lg" href="{{ route('client.emploi-temps.edit',$enseignant) }}">Modifier</a></div></div>
@include('client.partials.emploi-temps-grid',['readOnly'=>true])
@endsection
