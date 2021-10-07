<?php if ($folders) : ?>
    <?php foreach ($folders as $folder) : ?>
        <div class="storage-browser-popup-item storage-browser-info-block" storage-browser-info-block="<?= $folder['relative_path_name']; ?>">
            <form class="storage-browser-action-form storage-browser-info-block-in">
                <input type="hidden" name="action_type" value="update_folder">
                <input type="hidden" name="folder_name" value="<?= $folder['name']; ?>">
                <input type="hidden" name="folder_permissons" value="<?= $folder['permissions']; ?>">

                <div class="form-group">
                    <label><?= _e('Folder name'); ?></label>
                    <input type="text" class="form-control" name="name" value="<?= $folder['name']; ?>" required>
                </div>

                <div class="form-group">
                    <label><?= _e('Folder URL'); ?></label>
                    <div class="storage-browser-info-ro-input">
                        <?= $folder['file_url']; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label><?= _e('Permissions'); ?></label>
                    <input type="number" class="form-control" name="permissions" value="<?= $folder['permissions']; ?>" required>
                </div>

                <div class="row">
                    <div class="col-sm-6 form-group">
                        <label><?= _e('Modified at'); ?></label>
                        <div class="storage-browser-info-ro-input">
                            <?= date('d/m/Y H:i', $folder['modified_time']); ?>
                        </div>
                    </div>

                    <div class="col-sm-6 form-group">
                        <label><?= _e('Access time'); ?></label>
                        <div class="storage-browser-info-ro-input">
                            <?= date('d/m/Y H:i', $folder['access_time']); ?>
                        </div>
                    </div>
                </div>

                <div class="storage-browser-info-block-buttons">
                    <button type="button" class="btn btn-secondary waves-effect waves-light" storage-browser-popup-close>
                        <?= _e('Close'); ?>
                    </button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light storage-browser-action-btn">
                        <i class="ri-refresh-line storage-browser-icon-spin"></i>
                        <span><?= _e('Save'); ?></span>
                    </button>
                </div>
            </form>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if ($files) : ?>
    <?php foreach ($files as $file) : ?>
        <div class="storage-browser-popup-item storage-browser-info-block" storage-browser-info-block="<?= $file['name']; ?>">
            <form class="storage-browser-action-form storage-browser-info-block-in">
                <input type="hidden" name="action_type" value="update_file">
                <input type="hidden" name="file_name" value="<?= $file['name']; ?>">
                <input type="hidden" name="file_permissons" value="<?= $file['permissions']; ?>">

                <div class="form-group">
                    <label><?= _e('File name'); ?></label>
                    <input type="text" class="form-control" name="name" value="<?= $file['name']; ?>" required>
                </div>

                <div class="form-group">
                    <label><?= _e('File URL'); ?></label>
                    <div class="storage-browser-info-ro-input">
                        <?= $file['file_url']; ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6 form-group">
                        <label><?= _e('File size'); ?></label>
                        <div class="storage-browser-info-ro-input">
                            <?= $file['size']; ?>
                        </div>
                    </div>

                    <div class="col-sm-6 form-group">
                        <label><?= _e('Permissions'); ?></label>
                        <input type="number" class="form-control" name="permissions" value="<?= $file['permissions']; ?>" required>
                    </div>

                    <div class="col-sm-6 form-group">
                        <label><?= _e('Modified at'); ?></label>
                        <div class="storage-browser-info-ro-input">
                            <?= date('d/m/Y H:i', $file['modified_time']); ?>
                        </div>
                    </div>

                    <div class="col-sm-6 form-group">
                        <label><?= _e('Access time'); ?></label>
                        <div class="storage-browser-info-ro-input">
                            <?= date('d/m/Y H:i', $file['access_time']); ?>
                        </div>
                    </div>
                </div>

                <div class="storage-browser-info-block-buttons">
                    <button type="button" class="btn btn-secondary waves-effect waves-light" storage-browser-popup-close>
                        <?= _e('Close'); ?>
                    </button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light storage-browser-action-btn">
                        <i class="ri-refresh-line storage-browser-icon-spin"></i>
                        <span><?= _e('Save'); ?></span>
                    </button>
                </div>
            </form>
        </div>
    <?php endforeach; ?>
<?php endif; ?>