<!-- Advertisements -->
    <!-- modal for create advertisement -->
    <div class="modal fade" id="createAdvertisementModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {{ Form::open(['action' => 'AdvertisementsController@addAdvertisement', 'method' => 'POST']) }}
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">Create new Advertisement</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        {{Form::label('title', 'Advertisement Title', ['class' => 'col-form-label'])}}
                        {{Form::text('title', '', ['class' => 'form-control', 'placeholder' => 'Add a title'])}}
                    </div>
                    <div class="form-group">
                        {{Form::label('ad_overview', 'Overview', ['class' => 'col-form-label'])}}
                        {{Form::textarea('ad_overview', '', ['class' => 'form-control', 'placeholder' => 'Add overview'])}}
                    </div>
                    <div class="form-group">
                        {{Form::label('link', 'Link', ['class' => 'col-form-label'])}}
                        {{Form::text('link', '', ['class' => 'form-control', 'placeholder' => 'Add url'])}}
                    </div>
                    <div class="form-group">
                        <div class="row mt-3">
                            <div class="col-sm-12">
                                {{Form::label('img_filename', 'Ad Image')}}
                                <div class="card-img-top center-vertically px-3" style="height:250px; background-color: rgb(227, 227, 227);">
                                    <span class="font-weight-bold" style="font-size: 17px;line-height: 1.5em;color: #2b2b2b;">
                                        Upload a 520x250px image for the advertisement.
                                    </span>
                                </div>
                                {{ Form::file('img_filename', ['class' => 'form-control mt-2 mb-3 pt-1'])}}
                                <style>
                                    .center-vertically{
                                        display: flex;
                                        justify-content: center;
                                        align-items: center;
                                    }
                                </style>
                            </div> 
                        </div>
                    </div>
                    <div class="form-group">
                        {{Form::label('feature', 'Feature', ['class' => 'col-form-label'])}}
                        {{Form::checkbox('feature', '1')}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    {{Form::submit('Create Advertisement', ['class' => 'btn btn-success'])}}
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>
<!-- end of modal for create advertisement -->

@foreach(App\Advertisement::all() as $advertisement)
    <!-- edit advertisement -->
        <div class="modal fade" id="editAdvertisementModal-{{$advertisement->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    {{ Form::open(['action' => ['AdvertisementsController@editAdvertisement', $advertisement->id], 'method' => 'POST']) }}
                    <div class="modal-header">
                        <h6 class="modal-title" id="exampleModalLabel">Edit Advertisement</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            {{Form::label('isp', 'ISP')}}
                            {{Form::select('isp', $isps->pluck('name', 'id'), $advertisement->isp_id,['class' => 'form-control', 'placeholder' => 'Select ISP']) }}
                        </div>
                        <div class="form-group">
                            {{Form::label('name', 'Advertisement Name', ['class' => 'col-form-label'])}}
                            {{Form::text('name', $advertisement->name, ['class' => 'form-control', 'placeholder' => 'Add a name'])}}
                        </div>
                        <div class="form-group">
                            {{Form::label('description', 'Description', ['class' => 'col-form-label'])}}
                            {{Form::textarea('description', $advertisement->description, ['class' => 'form-control', 'placeholder' => 'Add a description'])}}
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
    <!-- edit advertisement end -->

    <!-- confirm delete advertisement -->
        <div class="modal fade" id="deleteAdvertisementModal-{{$advertisement->id}}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('deleteAdvertisement', $advertisement->id) }}" id="deleteForm" method="POST">
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
                            Are you sure you want to delete: <b>{{$advertisement->name}}</b>?</br></br>
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
    <!-- confirm delete advertisement -->
@endforeach
<!-- Advertisements END -->