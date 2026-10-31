-- ============================================================
--  Odik Shop — Pixel & Tracking + Steadfast Courier
--  Run once in cPanel > phpMyAdmin > sameerth_odikshop > SQL
-- ============================================================

-- Steadfast consignment details on each order.
ALTER TABLE `orders`
  ADD COLUMN `courier_consignment_id` VARCHAR(255) NULL AFTER `payment_status`,
  ADD COLUMN `courier_tracking_code`  VARCHAR(255) NULL AFTER `courier_consignment_id`,
  ADD COLUMN `courier_status`         VARCHAR(255) NULL AFTER `courier_tracking_code`,
  ADD COLUMN `courier_booked_at`      TIMESTAMP    NULL AFTER `courier_status`,
  ADD COLUMN `courier_note`           VARCHAR(255) NULL AFTER `courier_booked_at`;

-- Tell Laravel this migration is already applied, so a future `migrate`
-- does not try to add the same columns again.
SET @b = (SELECT IFNULL(MAX(batch), 0) + 1 FROM `migrations`);
INSERT INTO `migrations` (`migration`, `batch`) VALUES
  ('2026_10_29_000001_add_courier_fields_to_orders_table', @b);

-- No table is needed for the tracking settings: they are stored as ordinary
-- rows in the existing `site_settings` table the first time you press Save.
