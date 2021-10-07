<?php

use common\models\StorageBrowser;

if ($files) : ?>
    <?php foreach ($files as $file) : ?>
        <?php $image_preview = StorageBrowser::checkImagePreview($file); ?>
        <tr>
            <td>
                <i class="ri-checkbox-blank-line storage-browser-select-icon" storage-browser-select="<?= $file['name']; ?>" storage-browser-file-url="<?= $file['file_url']; ?>" storage-browser-select-item="file"></i>
            </td>
            <td>
                <div class="storage-browser-table-file" storage-browser-info="<?= $file['name']; ?>">
                    <div class="storage-browser-table-file-icon">
                        <i class="<?= StorageBrowser::iconConvert($file); ?>"></i>
                        <i class="ri-information-fill storage-browser-table-item-info" data-toggle="tooltip" data-placement="top" title="<?= _e('Informations'); ?>"></i>
                    </div>

                    <?php if ($image_preview) : ?>
                        <div class="storage-browser-list-image-preview" storage-browser-list-image-preview="<?= $image_preview; ?>">
                            <?= $file['name']; ?>
                        </div>
                    <?php else : ?>
                        <span><?= $file['name']; ?></span>
                    <?php endif; ?>
                </div>
            </td>
            <td><?= $file['size']; ?></td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>