-- Create the test database if it doesn't exist
CREATE DATABASE IF NOT EXISTS loyalty_test;

-- Grant all privileges on the test database to our user
GRANT ALL PRIVILEGES ON loyalty_test.* TO 'loyalty_user'@'%';

FLUSH PRIVILEGES;