-- Fix work_orders foreign keys so admin (from users table) can create work orders
-- Run this in phpMyAdmin on your IT38A-Enterprise-System1 database

-- Step 1: Drop the bad FK that points created_by to personnel
ALTER TABLE `work_orders` DROP FOREIGN KEY `work_orders_ibfk_2`;

-- Step 2: Re-add it pointing to users instead
ALTER TABLE `work_orders`
  ADD CONSTRAINT `work_orders_ibfk_2`
  FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- Also fix tasks.assigned_to — should allow users too, not just personnel
-- (optional but recommended)
-- ALTER TABLE `tasks` DROP FOREIGN KEY `tasks_ibfk_1`;
