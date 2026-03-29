ALTER TABLE `users`
    ADD COLUMN `branch` VARCHAR(100) NULL AFTER `region`;

UPDATE `users`
SET `branch` = NULL
WHERE `branch` = '';
