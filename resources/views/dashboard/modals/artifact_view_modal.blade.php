    <div class="modal-dialog {{$artifact->embed_link ? 'modal-xl' : 'modal-lg'}}">
        <div class="modal-content pl-0 pr-0 pl-0">
            <div class="inner-modal pl-3 pr-3"> 
                <div class="modal-header" style="padding-bottom:8px">
                    <span style="width:100%" class="mt-2">
                        <h4>{!!$artifact->title!!} </h4>
                    <span>
                    <small class="text-muted">
                        <i class="far fa-sticky-note"></i> {{$artifact->content->type}} | 
                        <i class="fas fa-calendar"></i> {{date('d-m-Y', strtotime($artifact->date_published))}}
                    </small>
                </div>
                <div class="modal-body">
                    @if($artifact->description)
                    <b>Description</b><br>
                    <span>{!!$artifact->description!!}</span>
                    @endif

                    @if($artifact->imglink != null)
                    <div class="dropdown-divider mt-3"></div>
                    <b>Image</b><br>
                    <span style=''>
                        <img src="{{$artifact->imglink}}" style="object-fit: contain; width:100%; height:300px">
                    </span>
                    @endif
                        

                    @if($artifact->consortia)
                    <div class="dropdown-divider mt-3"></div>
                    <b>Consortia Resource</b><br>
                    <span>{{$artifact->consortia->short_name}}</span>
                    @endif
                    
                    @if($artifact->author)
                    <div class="dropdown-divider mt-3"></div>
                    <b>Author</b><br>
                    <span>{{$artifact->author}}</span>
                    @endif

                    @if($artifact->author_institution)
                    <div class="dropdown-divider mt-3"></div>
                    <b>Author Institution</b><br>
                    <span>{{$artifact->author_institution}}</span>
                    @endif

                    @if($artifact->embed_link)
                    <div class="dropdown-divider mt-3"></div>
                    <b>Content Website</b><br>
                    <iframe allowfullscreen src="{{$artifact->embed_link}}" width="100%" height="500"></iframe>
                    <button id="fullscreeniframe" title="view in full screen" class="button btn btn-light"><i class="fas fa-expand"></i> View in fullscreen</button>
                    @endif
                    
                    
                    @if($artifact->file)
                    <div class="dropdown-divider mt-3"></div>
                    <b>PDF Preview</b><br>
                    <iframe 
                        class="mt-2"
                        src="{{$artifact->file_type == 'pdf_link' ? $artifact->file : asset('/storage/files/' . $artifact->file)}}" 
                        style="width:100%; height:500px;" 
                        frameborder="0">
                    </iframe>
                    @endif

                    <div class="dropdown-divider mt-3"></div>
                    <b>Search Keywords</b><br>
                    <span>{{$artifact->keywords}}</span>
                </div>
                <div class="modal-footer">
                    @if($artifact->link != null)
                    <a target="_blank" href="{{$artifact->link}}"><button type="button" class="btn btn-primary">Go to link</button></a>
                    @endif
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>