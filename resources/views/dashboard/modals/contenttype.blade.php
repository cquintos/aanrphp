<!-- Contents -->
    <!-- modal for create content -->
    <div class="modal fade" id="createContentTypeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {{ Form::open(['action' => 'ContentController@addContent', 'method' => 'POST']) }}
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">Create new Content Type</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        {{Form::label('type', 'Content Type', ['class' => 'col-form-label'])}}
                        {{Form::text('type', '', ['class' => 'form-control', 'placeholder' => 'Add a type'])}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    {{Form::submit('Create Content', ['class' => 'btn btn-success'])}}
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>
<!-- end of modal for create content -->

@foreach($contents as $content)
    <!-- edit content -->
        <div class="modal fade" id="editContentTypeModal-{{$content->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    {{ Form::open(['action' => ['ContentController@editContent', $content->id], 'method' => 'POST']) }}
                    <div class="modal-header">
                        <h6 class="modal-title" id="exampleModalLabel">Edit Content Type</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            {{Form::label('type', 'Content Type', ['class' => 'col-form-label'])}}
                            {{Form::text('type', $content->type, ['class' => 'form-control', 'placeholder' => 'Add a type'])}}
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
    <!-- edit content end -->

    <!-- confirm delete content -->
        <div class="modal fade" id="deleteContentTypeModal-{{$content->id}}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('deleteContent', $content->id) }}" id="deleteForm" method="POST">
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
                            <?php $content_with_subtype = $contents->find($content->id); ?>
                            @if($content_with_subtype->content_subtypes->count() > 0)
                                You cannot delete: <b>{{$content->type}}</b></br></br>
                                The following content subtypes needs to be deleted before deleting this content type:
                                <ul>
                                    @foreach($content_with_subtype->content_subtypes as $content_subtype)
                                        <li>{{$content_subtype->name}}</li>
                                    @endforeach
                                </ul>
                            @else
                                Are you sure you want to delete: <b>{{$content->type}}</b>?</br></br>
                            @endif
                        </span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
                        @if($content_with_subtype->content_subtypes->count() == 0)
                        <input class="btn btn-danger" type="submit" value="Yes, Delete">
                        @endif
                    </div>
                    </form>
                </div>
            </div>
        </div>
    <!-- confirm delete content -->
@endforeach
<!-- Contents END -->