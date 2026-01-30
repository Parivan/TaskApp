CREATE DATABASE IF NOT EXISTS task_app_test
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'task_app_test'@'%' IDENTIFIED BY 'task_app_test';
GRANT ALL PRIVILEGES ON task_app_test.* TO 'task_app_test'@'%';

FLUSH PRIVILEGES;
