<div class="storage-browser-block">
    <div class="storage-browser-block-in">
        <div class="storage-browser-top">
            <div class="storage-browser-top-in storage-browser-top-left">
                <div class="h3 storage-browser-top-border" storage-browser-text="title"><?= _e('{count} files', ['count' => '0']); ?></div>

                <div class="storage-browser-top-icon">
                    <i class="ri-function-line storage-browser-view-btn" storage-browser-view="grid" data-toggle="tooltip" data-placement="top" title="<?= _e('Grid view'); ?>"></i>
                </div>

                <div class="storage-browser-top-icon storage-browser-top-border">
                    <i class="ri-list-unordered storage-browser-view-btn" storage-browser-view="list" data-toggle="tooltip" data-placement="top" title="<?= _e('List view'); ?>"></i>
                </div>

                <div class="storage-browser-top-icon">
                    <i class="ri-delete-bin-line" storage-browser-quick-action="delete" data-toggle="tooltip" data-placement="top" title="<?= _e('Delete'); ?>"></i>
                </div>
            </div>

            <div class="storage-browser-top-in storage-browser-top-right">
                <div class="storage-browser-top-btn">
                    <button type="button" class="btn btn-outline-secondary waves-effect" storage-browser-toggle="folder">
                        <i class="ri-folder-add-line mr-2"></i>
                        <?= _e('Add folder'); ?>
                    </button>
                </div>
                <div class="storage-browser-top-btn storage-browser-top-right" storage-browser-toggle="upload">
                    <button type="button" class="btn btn-info waves-effect waves-light">
                        <i class="ri-upload-2-line mr-2"></i>
                        <?= _e('Upload'); ?>
                    </button>
                </div>
            </div>
        </div>

        <div class="storage-browser-actions">
            <div class="storage-browser-add-folder">
                <form class="storage-browser-action-form input-group">
                    <input type="hidden" name="action_type" value="create_folder">
                    <input type="text" class="form-control" name="folder_name" placeholder="<?= _e('Enter name of folder'); ?>" required>

                    <div class="input-group-append">
                        <button type="submit" class="btn btn-secondary storage-browser-action-btn">
                            <i class="ri-refresh-line storage-browser-icon-spin"></i>
                            <span><?= _e('Create folder'); ?></span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="storage-browser-upload">
                <form class="storage-browser-upload-form input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="storage-browser-upload-area" name="files[]" multiple required>
                        <label class="custom-file-label" id="storage-browser-upload-label" for="storage-browser-upload-area" data-label="<?= _e('Choose file'); ?>"><?= _e('Choose file'); ?></label>
                    </div>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-success storage-browser-action-btn">
                            <i class="ri-refresh-line storage-browser-icon-spin"></i>
                            <span><?= _e('Upload'); ?></span>
                        </button>
                    </div>

                    <div id="progress-wrp">
                        <div class="progress-bar-in"></div>
                        <div class="progress-bar-status" data-text="<?= _e('Compressing...'); ?>">0%</div>
                    </div>

                    <div class="storage-browser-upload-form-msg"></div>
                </form>
            </div>
        </div>

        <div class="storage-browser-link">
            <ul class="nav">
                <li storage-browser-open-dir="/">
                    <i class="ri-home-3-line"></i>
                    <?= $path_name ? $path_name : _e('Home'); ?>
                </li>
            </ul>
        </div>

        <div class="storage-browser-list" storage-browser-block>
            <div class="storage-browser-list-preloader">
                <span>
                    <i class="ri-refresh-line storage-browser-icon-spin"></i>
                </span>
            </div>

            <div class="storage-browser-list-view" storage-browser-view="list">
                <table class="storage-browser-table">
                    <thead>
                        <tr>
                            <td>
                                <i class="ri-checkbox-blank-line storage-browser-select-icon" storage-browser-select-all></i>
                            </td>
                            <th><?= _e('Name') ?></th>
                            <th width="150px"><?= _e('Size') ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="storage-browser-table-notfound">
                                <i class="ri-error-warning-line"></i>
                                <div class="h4"><?= _e('Files not found!') ?></div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="storage-browser-grid-view" storage-browser-view="grid">
                <div class="storage-browser-grid-in"></div>
                <div class="storage-browser-table-notfound">
                    <i class="ri-error-warning-line"></i>
                    <div class="h4"><?= _e('Files not found!') ?></div>
                </div>
            </div>
        </div>

        <div class="storage-browser-info-popup"></div>
    </div>
</div>
