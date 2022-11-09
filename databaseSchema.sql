CREATE DATABASE `hydroponic_test_system`;

CREATE TABLE hydroponic_test_system.log (
  `log_date` datetime DEFAULT NULL,
  `log_type` varchar(16) DEFAULT NULL,
  `log_message` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE hydroponic_test_system.parameters (
  `parameter_name` varchar(100) DEFAULT NULL,
  `parameter_value` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO hydroponic_test_system.parameters VALUES("state", "0");

INSERT INTO hydroponic_test_system.log VALUES(CURRENT_DATE, "INFO", "This is a test message");
