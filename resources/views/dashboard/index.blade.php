@extends('layouts.admin')

@section('content')
    <!-- Título e Trilha de Navegação -->
    <div class="content-wrapper">
        <div class="content-header">
            <h2 class="content-title">Dashboard</h2>
            <nav class="breadcrumb">
                <span>Dashboard</span>
            </nav>
        </div>
    </div>

    <div class="content-box">
        <div class="content-box-header">
            <h3 class="content-box-title">Página Inicial</h3>
            <div class="content-box-btn"></div>
        </div>

        <x-alert />
        
    </div>
    
@endsection
