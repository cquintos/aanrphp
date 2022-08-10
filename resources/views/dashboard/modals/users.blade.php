
@foreach(App\User::all() as $user)
    <div class="modal fade" id="setConsortiaAdminModal-{{$user->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {{ Form::open(['action' => ['ConsortiaController@setUserAdmin', $user->id], 'method' => 'POST']) }}
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">Change User Role</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id = "lol">
                    <div class="form-group">
                        {{Form::label('consortia_admin_id', 'Set user role', ['class' => 'col-form-label'])}} 
                        <select class="form-control" name="user_role" id="user_role">
                            <option value="" selected disabled hidden>Select Role</option>
                            <option value="1">Regular User</option>
                            <option value="2">Consortia Admin</option>
                            <option value="5">Superadmin</option>
                        </select>
                    </div>
                    <div class="form-group consortia-user-choice" style="{{$user->role == 2 ? '' : 'display:none'}}">
                        {{Form::label('consortia_admin_id', 'Choose consortia', ['class' => 'col-form-label required'])}} 
                        {{Form::select('consortia_admin_id', App\Consortia::where('short_name', '=', $user->organization)->pluck('short_name', 'id')->all(), $user->consortia_admin_id, ['class' => 'form-control', ])}}
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
    
    <div class="modal fade" id="consortiaAdminRequestApproveModal-{{$user->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {{ Form::open(['action' => ['UsersController@consortiaAdminRequestApprove', $user->id], 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">Approve Consortia Admin Request</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group ">
                        Are you sure you want to approve <b>{{$user->first_name}} {{$user->last_name}}'s </b> request?
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    {{Form::submit('Approve', ['class' => 'btn btn-success'])}}
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>

    <!-- confirm delete user -->
    <div class="modal fade" id="deleteUserModal-{{$user->id}}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('deleteUser', $user->id) }}" id="deleteForm" method="POST">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">Confirm Delete</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{ csrf_field() }}
                    <span>
                        Are you sure you want to delete <b>{{$user->first_name}} {{$user->last_name}}'s</b> account?</br></br>
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

<script>
    $(document).ready(function() {
        $('select[name$="user_role"]').click(function() {
            if($(this).val() == '2') {
                $('.consortia-user-choice').show();           
            }
            else {
                $('.consortia-user-choice').hide();   
            }
        });

    });
    $('.modal').on('show.bs.modal', function () {
        $('select').prop('selectedIndex', "");
        $('.consortia-user-choice').hide();   
    });

</script>
