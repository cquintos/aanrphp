
<div class="modal fade" id="editPCAARRDPageModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php echo e(Form::open(['action' => ['PCAARRDPageController@editPCAARRDPage', $pcaarrdPage->id], 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">Edit Consortia Member</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <?php echo e(Form::label('full_name', 'Consortia Full Name', ['class' => 'col-form-label required'])); ?>

                    <?php echo e(Form::text('full_name', $pcaarrdPage->full_name, ['class' => 'form-control', 'placeholder' => 'Add full name'])); ?>

                </div>
                <div class="form-group">
                    <?php echo e(Form::label('short_name', 'Consortia Short Name', ['class' => 'col-form-label required'])); ?>

                    <?php echo e(Form::text('short_name', $pcaarrdPage->short_name, ['class' => 'form-control', 'placeholder' => 'Add acronym/short name'])); ?>

                </div>
                <div class="form-group">
                    <div class="mt-3">
                        <?php echo e(Form::label('image', 'Consortia Logo', ['class' => 'required'])); ?>

                        <br>
                        <?php if($pcaarrdPage->thumbnail!=null): ?>
                        <img src="/storage/page_images/<?php echo e($pcaarrdPage->thumbnail); ?>" class="card-img-top" style="object-fit: cover;overflow:hidden;height:250px;width:250px;border:1px solid rgba(100,100,100,0.25)" >
                        <?php else: ?>
                        <div class="card-img-top center-vertically px-3" style="height:250px; width:250px; background-color: rgb(227, 227, 227);">
                            <span class="font-weight-bold" style="font-size: 17px;line-height: 1.5em;color: #2b2b2b;">
                                Upload a 250x250px logo for the consortia.
                            </span>
                        </div>
                        <?php endif; ?> 
                        <?php echo e(Form::file('image', ['class' => 'form-control mt-2 mb-3 pt-1'])); ?>

                        <style>
                            .center-vertically{
                                display: flex;
                                justify-content: center;
                                align-items: center;
                            }
                        </style>
                    </div> 
                </div>
                <div class="form-group">
                    <?php echo e(Form::label('profile', 'Profile', ['class' => 'col-form-label'])); ?>

                    <?php echo e(Form::textarea('profile', $pcaarrdPage->profile, ['class' => 'form-control', 'placeholder' => 'Add a profile', 'rows' => '4'])); ?>

                </div>
                <div class="form-group">
                    <?php echo e(Form::label('contact_name', 'Contact Name', ['class' => 'col-form-label'])); ?>

                    <?php echo e(Form::text('contact_name', $pcaarrdPage->contact_name, ['class' => 'form-control', 'placeholder' => 'Add contact name'])); ?>

                </div>
                <div class="form-group">
                    <?php echo e(Form::label('contact_details', 'Contact Details', ['class' => 'col-form-label'])); ?>

                    <?php echo e(Form::text('contact_details', $pcaarrdPage->contact_details, ['class' => 'form-control', 'placeholder' => 'Add contact details'])); ?>

                </div>
                <div class="form-group">
                    <?php echo e(Form::label('link', 'Link to website', ['class' => 'col-form-label'])); ?>

                    <?php echo e(Form::text('link', $pcaarrdPage->link, ['class' => 'form-control', 'placeholder' => 'Add a link'])); ?>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <?php echo e(Form::submit('Save Changes', ['class' => 'btn btn-success'])); ?>

            </div>
            <?php echo e(Form::close()); ?>

        </div>
    </div>
</div><?php /**PATH /var/www/aanrphp/resources/views/dashboard/modals/pcaarrdpage.blade.php ENDPATH**/ ?>