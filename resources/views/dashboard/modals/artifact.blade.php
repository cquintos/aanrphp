<div class="modal fade" id="multiple_artifact_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{ Form::open(['action' => ['ArtifactAANRController@uploadArtifactCSV'], 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            <div class="modal-header">
                <h6 class="modal-title" style="color:black">Upload AANR Content</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="tab-content mb-3" id="addArtifactTabContent">
                    <div class="">
                        <div class="form-group">
                            {{Form::label('csv_file', 'Upload CSV File', ['class' => 'col-form-label required'])}}
                            {{ Form::file('csv_file', ['class' => 'form-control mb-3 pt-1'])}}
                        </div>
                        <br>
                        <div>
                            <h3 style="color:black"> IMPORTANT REMINDERS: </h3>
                            <br>
                            <h6 style="color:black">Entries with missing TITLE, CONSORTIA, and CONTENT_TYPE fields will be skipped.</h6>
                            <br>
                            <h6 style="color:black">Entries with the same title, date published, author, description, consortia, content type, and GAD are considered duplicate and will also be skipped.</h6>
                            <br>
                            <h6 style="color:black">Entries with invalid CONSORTIA, CMI, CONTENT TYPE, and SUBCONTENT TYPE will also be skipped.</h6>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                
                {{Form::submit('Upload CSV', ['class' => 'btn btn-success'])}}
            </div>
            {{Form::close()}}
        </div>
    </div>
</div>