<?php if ($folders) : ?>
    <?php foreach ($folders as $folder) : ?>
        <tr>
            <td>
                <i class="ri-checkbox-blank-line storage-browser-select-icon" storage-browser-select="<?= $folder['relative_path_name']; ?>" storage-browser-select-item="folder"></i>
            </td>
            <td>
                <div class="storage-browser-table-folder" storage-browser-open-dir="<?= trim($path_name, '/') . '/' . $folder['relative_path_name']; ?>">
                    <div class="storage-browser-table-folder-icon">
                        <i class="ri-folder-3-fill"></i>
                        <i class="ri-information-fill storage-browser-table-item-info" storage-browser-path-info="<?= $folder['relative_path_name']; ?>" data-toggle="tooltip" data-placement="top" title="<?= _e('Informations'); ?>"></i>
                    </div>

                    <span>
                        <?= $folder['name']; ?>
                    </span>
                </div>
            </td>
            <td>-</td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>