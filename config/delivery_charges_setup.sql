-- Create delivery_settings table
CREATE TABLE IF NOT EXISTS delivery_settings (
    setting_id INT AUTO_INCREMENT PRIMARY KEY,
    delivery_charge DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    free_delivery_enabled TINYINT(1) DEFAULT 0,
    free_delivery_threshold DECIMAL(10, 2) DEFAULT 0.00,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default settings
INSERT INTO delivery_settings (delivery_charge, free_delivery_enabled, free_delivery_threshold) 
VALUES (200.00, 0, 5000.00)
ON DUPLICATE KEY UPDATE setting_id = setting_id;

-- Add delivery_charge column to orders table if it doesn't exist
ALTER TABLE orders ADD COLUMN IF NOT EXISTS delivery_charge DECIMAL(10, 2) DEFAULT 0.00 AFTER total_amount;
