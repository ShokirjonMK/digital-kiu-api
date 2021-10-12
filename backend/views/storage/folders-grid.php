<?php
if ($folders) : ?>
    <?php foreach ($folders as $folder) : ?>
        <div class="storage-browser-grid-item storage-browser-grid-folder">
            <div class="storage-browser-grid-item-in">
                <div class="storage-browser-grid-item-top">
                    <i class="ri-checkbox-blank-line storage-browser-select-icon" storage-browser-select="<?= $folder['relative_path_name']; ?>" storage-browser-select-item="folder"></i>
                    <div class="storage-browser-grid-icon" storage-browser-open-dir="<?= trim($path_name, '/') . '/' . $folder['relative_path_name']; ?>">
                        <i class="ri-folder-3-fill"></i>
                    </div>
                </div>
                <div class="storage-browser-grid-item-bottom" storage-browser-path-info="<?= $folder['relative_path_name']; ?>">
                    <i class="ri-information-fill mr-2" data-toggle="tooltip" data-placement="top" title="<?= _e('Informations'); ?>"></i>
                    <span title="<?= $folder['name']; ?>"><?= $folder['name']; ?></span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>