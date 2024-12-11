<div class="page-wrapper">
    <div class="page-body">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-lg-6"> <!--add class main-header-->
                    <h2>{{ ucwords($title) }}</h2>
                    </div>
                    <div class="col-lg-6 breadcrumb-right">     
                        <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa fa-home"></i></a></li>
                        <li class="breadcrumb-item active">{{ $currentRoute }}</li>
                        </ol>
                    </div> <!--breadcrumb-->
                </div> <!--row-->
            </div> <!--page header-->
        </div> <!--container-fluid-->
            <div class="card-body">

                @if(session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

            </div>
    </div>
</div>  