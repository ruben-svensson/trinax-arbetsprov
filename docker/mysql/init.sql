SET NAMES utf8mb4;

-- Mock data to simulate the Trinax API in development
CREATE TABLE IF NOT EXISTS `mock_workplaces` (
  `id` INT NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `created_time` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `mock_workplaces` (`id`, `name`, `created_time`) VALUES
(1, 'Köksrenovering Kungsgatan 19', '2017-11-05 20:52:49'),
(2, 'Takläggning Skolgatan 8', '2017-11-05 20:52:49');

CREATE TABLE IF NOT EXISTS `mock_timereports` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `workplace_id` INT NOT NULL,
  `date` DATE NOT NULL,
  `hours` DECIMAL(5,2) NOT NULL,
  `info` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `workplace_id` (`workplace_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `mock_timereports` (`id`, `workplace_id`, `date`, `hours`, `info`) VALUES
(1, 2, '2017-10-27', 5.50, NULL),
(2, 1, '2017-10-28', 2.00, 'Testing'),
(3, 1, '2017-10-29', 2.00, NULL);

CREATE TABLE IF NOT EXISTS `mock_timereport_images` (
  `timereport_id` INT PRIMARY KEY,
  `filename` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Stores uploaded images for MOCK time reports';

-- Real application data (used in both development and production)
CREATE TABLE IF NOT EXISTS `timereport_images` (
  `timereport_id` INT PRIMARY KEY,
  `filename` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Stores uploaded images for time reports';