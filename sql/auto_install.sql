-- +--------------------------------------------------------------------+
-- | Copyright CiviCRM LLC. All rights reserved.                        |
-- |                                                                    |
-- | This work is published under the GNU AGPLv3 license with some      |
-- | permitted exceptions and without any warranty. For full license    |
-- | and copyright information, see https://civicrm.org/licensing       |
-- +--------------------------------------------------------------------+
--
-- Generated from schema.tpl
-- DO NOT EDIT.  Generated by CRM_Core_CodeGen
--
-- /*******************************************************
-- *
-- * Clean up the existing tables - this section generated from drop.tpl
-- *
-- *******************************************************/

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `civicrm_api_log`;
DROP TABLE IF EXISTS `civicrm_api_log_config`;

SET FOREIGN_KEY_CHECKS=1;
-- /*******************************************************
-- *
-- * Create new tables
-- *
-- *******************************************************/

-- /*******************************************************
-- *
-- * civicrm_api_log_config
-- *
-- * Entity which contains Api Log configurations
-- *
-- *******************************************************/
CREATE TABLE `civicrm_api_log_config` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique ApiLogConfig ID',
  `title` varchar(255) NOT NULL,
  `entity_filter` varchar(255) NOT NULL,
  `action_filter` longtext NOT NULL,
  `request_filter` longtext NOT NULL,
  `response_filter` longtext NOT NULL,
  `success_filter` int unsigned NULL DEFAULT 1 COMMENT 'Success filter based on option group',
  PRIMARY KEY (`id`)
)
ENGINE=InnoDB;

-- /*******************************************************
-- *
-- * civicrm_api_log
-- *
-- * FIXME
-- *
-- *******************************************************/
CREATE TABLE `civicrm_api_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique ApiLog ID',
  `contact_id` int unsigned COMMENT 'FK to Contact',
  `api_log_config_id` int unsigned COMMENT 'FK to ApiLogConfig',
  `api_entity` varchar(255),
  `api_action` varchar(255),
  `request` longtext,
  `response` longtext,
  `success` tinyint,
  `api_version` varchar(255),
  `created_date` datetime,
  PRIMARY KEY (`id`),
  CONSTRAINT FK_civicrm_api_log_contact_id FOREIGN KEY (`contact_id`) REFERENCES `civicrm_contact`(`id`) ON DELETE SET NULL,
  CONSTRAINT FK_civicrm_api_log_api_log_config_id FOREIGN KEY (`api_log_config_id`) REFERENCES `civicrm_api_log_config`(`id`) ON DELETE SET NULL
)
ENGINE=InnoDB;
