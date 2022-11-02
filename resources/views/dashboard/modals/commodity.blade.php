<!-- Commodities -->
    <!-- modal for create commodity -->
    <div class="modal fade" id="createCommodityModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {{ Form::open(['action' => 'CommoditiesController@add', 'method' => 'POST']) }}
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">Create new Commodity</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        {{Form::label('isp', 'ISP', ['class' => 'col-form-label'])}}
                        {{Form::select('isp', App\ISP::pluck('name', 'id')->all(), null,['class' => 'form-control', 'placeholder' => 'Select ISP']) }}
                    </div>
                    <div class="form-group">
                        {{Form::label('name', 'Commodity Name', ['class' => 'col-form-label required'])}}
                        {{Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'Add a name'])}}
                    </div>
                    <div class="form-group">
                        {{Form::label('description', 'Description', ['class' => 'col-form-label'])}}
                        {{Form::textarea('description', '', ['class' => 'form-control', 'placeholder' => 'Add a description'])}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    {{Form::submit('Create Commodity', ['class' => 'btn btn-success'])}}
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>
<!-- end of modal for create commodity -->

@foreach(App\Commodity::all() as $commodity)
    <!-- confirm delete commodity -->
        <div class="modal fade" id="deleteCommodityModal-{{$commodity->id}}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('deleteCommodity', $commodity->id) }}" id="deleteForm" method="POST">
                    <div class="modal-header">
                        <h6 class="modal-title" id="exampleModalLabel">Confirm Delete</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <span>
                            Are you sure you want to delete: <b>{{$commodity->name}}</b>?<br><br>
                        </span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
                        <input class="btn btn-danger" type="submit" value="Yes, Delete">
                    </div>
                    </form>
                </div>
            </div>
        </div>
    <!-- confirm delete commodity -->
@endforeach
<!-- Commodities END -->