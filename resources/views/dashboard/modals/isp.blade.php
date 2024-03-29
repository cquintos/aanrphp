<!-- ISP -->
    <!-- modal for create isp -->
    <div class="modal fade" id="createISPModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {{ Form::open(['action' => 'ISPController@addISP', 'method' => 'POST']) }}
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">Create new ISP</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        {{Form::label('sector', 'Sector', ['class' => 'col-form-label required'])}}
                        {{Form::select('sector', $sectors->pluck('name', 'id'), null,['class' => 'form-control', 'placeholder' => 'Select Sector']) }}
                    </div>
                    <div class="form-group">
                        {{Form::label('name', 'ISP Name', ['class' => 'col-form-label required'])}}
                        {{Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'Add a name'])}}
                    </div>
                    <div class="form-group">
                        {{Form::label('description', 'Description', ['class' => 'col-form-label'])}}
                        {{Form::textarea('description', '', ['class' => 'form-control', 'placeholder' => 'Add a description'])}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    {{Form::submit('Create ISP', ['class' => 'btn btn-success'])}}
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>
<!-- end of modal for create isp -->

@foreach($isps as $isp)
    <!-- edit isp -->
        <div class="modal fade" id="editISPModal-{{$isp->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    {{ Form::open(['action' => ['ISPController@editISP', $isp->id], 'method' => 'POST']) }}
                    <div class="modal-header">
                        <h6 class="modal-title" id="exampleModalLabel">Edit ISP</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            {{Form::label('sector', 'Sector', ['class' => 'col-form-label required'])}}
                            {{Form::select('sector', $sectors->pluck('name', 'id'), $isp->sector_id,['class' => 'form-control', 'placeholder' => 'Select Sector']) }}
                        </div>
                        <div class="form-group">
                            {{Form::label('name', 'ISP Name', ['class' => 'col-form-label required'])}}
                            {{Form::text('name', $isp->name, ['class' => 'form-control', 'placeholder' => 'Add a name'])}}
                        </div>
                        <div class="form-group">
                            {{Form::label('description', 'Description', ['class' => 'col-form-label'])}}
                            {{Form::textarea('description', $isp->description, ['class' => 'form-control', 'placeholder' => 'Add a description'])}}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        {{Form::submit('Save Changes', ['class' => 'btn btn-success'])}}
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    <!-- edit isp end -->

    <!-- confirm delete isp -->
        <div class="modal fade" id="deleteISPModal-{{$isp->id}}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('deleteISP', $isp->id) }}" id="deleteForm" method="POST">
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
                                Are you sure you want to delete: <b>{{$isp->name}}</b>?</br></br>
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
    <!-- confirm delete isp -->
@endforeach
<!-- ISP END -->