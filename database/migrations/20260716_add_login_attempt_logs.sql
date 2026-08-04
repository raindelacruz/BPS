CREATE TABLE IF NOT EXISTS `login_attempt_logs` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NULL,
    `username_entered` VARCHAR(100) NULL,
    `event_type` ENUM('login_attempt', 'csrf_failure', 'logout') NOT NULL DEFAULT 'login_attempt',
    `outcome` ENUM('success', 'failure') NOT NULL,
    `failure_reason` VARCHAR(50) NULL,
    `message` VARCHAR(255) NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(255) NULL,
    `request_method` VARCHAR(10) NULL,
    `request_uri` VARCHAR(255) NULL,
    `context` JSON NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_login_attempt_logs_user` (`user_id`),
    KEY `idx_login_attempt_logs_created` (`created_at`),
    KEY `idx_login_attempt_logs_outcome_reason` (`outcome`, `failure_reason`),
    KEY `idx_login_attempt_logs_username` (`username_entered`),
    CONSTRAINT `fk_login_attempt_logs_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TRIGGER IF EXISTS `tr_login_attempt_logs_no_update`;
DROP TRIGGER IF EXISTS `tr_login_attempt_logs_no_delete`;

DELIMITER $$

CREATE TRIGGER `tr_login_attempt_logs_no_update`
BEFORE UPDATE ON `login_attempt_logs`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Login attempt logs are append-only.';
END $$

CREATE TRIGGER `tr_login_attempt_logs_no_delete`
BEFORE DELETE ON `login_attempt_logs`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Login attempt logs cannot be deleted.';
END $$

DELIMITER ;
