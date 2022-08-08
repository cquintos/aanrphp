@if(count($errors) > 0)
    @foreach($errors->all() as $error)
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {{$error}}
        </div>
    @endforeach
@endif
@if(session('success'))
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{session('success')}}</strong>
    </div>
@endif

@if(session('error'))
    <div class="alert" style="background-color: white; text-align:center; font-size: 16pt">
    <i class="fas fa-exclamation-triangle" style="color: red"></i><button type="button" class="close" data-dismiss="alert" style="text-align:center; color:red">×</button>
        <strong>{{session('error')}}</strong>
    </div>
@endif
