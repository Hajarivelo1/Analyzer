<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SEOAnalyzer Pro - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" >
    <!-- Chargement avec Vite -->
@vite(['resources/css/admin_master.css', 'resources/js/admin_master.js'])
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<body class="min-vh-100 d-flex flex-column min-vh-100">
    <div class="container-fluid h-100 flex-grow-1 d-flex flex-column">
        <div class="row">
            <!-- Sidebar Navigation -->
            @include('admin.body.sidebar')
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 glass-background d-flex flex-column">
                <!-- Header -->
                @include('admin.body.header')
                
                <!-- Dashboard Content -->
               @yield('admin')

                <!-- Footer -->
                @include('admin.body.footer')
            </main>
        </div>
    </div>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>// Initialize tooltips
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script> 
<script src="{{ asset('backend/assets/js/code.js') }}"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
 @if(Session::has('message'))
 var type = "{{ Session::get('alert-type','info') }}"
 switch(type){
    case 'info':
    toastr.info(" {{ Session::get('message') }} ");
    break;

    case 'success':
    toastr.success(" {{ Session::get('message') }} ");
    break;

    case 'warning':
    toastr.warning(" {{ Session::get('message') }} ");
    break;

    case 'error':
    toastr.error(" {{ Session::get('message') }} ");
    break; 
 }
 @endif 
</script>





</body>