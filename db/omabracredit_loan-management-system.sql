-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 06, 2024 at 10:22 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `omabracredit_loan-management-system`
--

-- --------------------------------------------------------

--
-- Table structure for table `active_loans`
--

CREATE TABLE `active_loans` (
  `loan_id` varchar(10) NOT NULL,
  `client_id` varchar(10) NOT NULL,
  `national_id` varchar(20) NOT NULL,
  `requested_amount` decimal(10,2) NOT NULL,
  `loan_purpose` text NOT NULL,
  `duration` int(11) NOT NULL,
  `duration_period` enum('Week','Month','Year') NOT NULL,
  `date_applied` date NOT NULL,
  `collateral_name` varchar(100) NOT NULL,
  `collateral_value` decimal(10,2) NOT NULL,
  `collateral_pic1` varchar(255) NOT NULL,
  `collateral_pic2` varchar(255) NOT NULL,
  `guarantor1_name` varchar(100) NOT NULL,
  `guarantor1_phone` varchar(20) NOT NULL,
  `guarantor2_name` varchar(100) NOT NULL,
  `guarantor2_phone` varchar(20) NOT NULL,
  `loan_status` set('Active') NOT NULL DEFAULT 'Active',
  `onboarding_officer` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `call_back_contacts`
--

CREATE TABLE `call_back_contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cleared_loans`
--

CREATE TABLE `cleared_loans` (
  `loan_id` varchar(10) NOT NULL,
  `client_id` varchar(10) NOT NULL,
  `national_id` varchar(20) NOT NULL,
  `requested_amount` decimal(10,2) NOT NULL,
  `loan_purpose` text NOT NULL,
  `duration` int(11) NOT NULL,
  `duration_period` enum('Week','Month','Year') NOT NULL,
  `date_applied` date NOT NULL,
  `collateral_name` varchar(100) NOT NULL,
  `collateral_value` decimal(10,2) NOT NULL,
  `collateral_pic1` varchar(255) NOT NULL,
  `collateral_pic2` varchar(255) NOT NULL,
  `guarantor1_name` varchar(100) NOT NULL,
  `guarantor1_phone` varchar(20) NOT NULL,
  `guarantor2_name` varchar(100) NOT NULL,
  `guarantor2_phone` varchar(20) NOT NULL,
  `loan_status` enum('Pending','Active','Rejected','Cleared','Defaulted') NOT NULL DEFAULT 'Cleared',
  `onboarding_officer` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `client_id` varchar(10) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `county` varchar(50) NOT NULL,
  `town_centre` varchar(100) NOT NULL,
  `national_id` varchar(20) NOT NULL,
  `id_photo_front` varchar(255) NOT NULL,
  `id_photo_back` varchar(255) NOT NULL,
  `work_economic_activity` varchar(100) NOT NULL,
  `residence_nearest_building` varchar(100) NOT NULL,
  `residence_nearest_road` varchar(100) NOT NULL,
  `date_of_onboarding` date NOT NULL,
  `onboarding_officer` varchar(100) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `next_of_kin_name` varchar(100) DEFAULT NULL,
  `next_of_kin_phone_number` varchar(20) DEFAULT NULL,
  `next_of_kin_relation` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `next_of_kin_name_number_1` varchar(100) DEFAULT NULL,
  `next_of_kin_name_number_2` varchar(100) DEFAULT NULL,
  `next_of_kin_name_number_3` varchar(100) DEFAULT NULL,
  `next_of_kin_name_number_4` varchar(100) DEFAULT NULL,
  `next_of_kin_name_number_5` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`client_id`, `first_name`, `last_name`, `email`, `phone_number`, `county`, `town_centre`, `national_id`, `id_photo_front`, `id_photo_back`, `work_economic_activity`, `residence_nearest_building`, `residence_nearest_road`, `date_of_onboarding`, `onboarding_officer`, `age`, `gender`, `next_of_kin_name`, `next_of_kin_phone_number`, `next_of_kin_relation`, `created_at`, `next_of_kin_name_number_1`, `next_of_kin_name_number_2`, `next_of_kin_name_number_3`, `next_of_kin_name_number_4`, `next_of_kin_name_number_5`) VALUES
('cl000001', 'Joseph', 'Lweya', 'joseph.lweya@outlook.com', '254717158091', 'Meru', 'Dubai', '28351507', 'uploads/front_6702aa51a20d0.jpg', 'uploads/back_6702aa51a2526.jpg', 'Farm', 'Roysambu', 'Kwetu', '2024-10-06', 'admin', 27, 'Baringo', 'Alex', '0785385963', 'bro', '2024-10-06 15:18:41', NULL, NULL, NULL, NULL, NULL);

--
-- Triggers `clients`
--
DELIMITER $$
CREATE TRIGGER `generate_client_id` BEFORE INSERT ON `clients` FOR EACH ROW BEGIN
    DECLARE next_id INT;
    SET next_id = COALESCE((SELECT MAX(CAST(SUBSTRING(client_id, 3) AS UNSIGNED)) FROM clients), 0) + 1;
    SET NEW.client_id = CONCAT('cl', LPAD(next_id, 6, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `company_settings`
--

CREATE TABLE `company_settings` (
  `id` int(11) NOT NULL,
  `company_name` varchar(50) NOT NULL,
  `company_address` varchar(50) NOT NULL,
  `company_email` varchar(50) NOT NULL,
  `company_website` varchar(50) NOT NULL,
  `company_phone` varchar(20) NOT NULL,
  `tax_rate` int(5) NOT NULL,
  `interest_rate` decimal(5,2) DEFAULT NULL,
  `interest_billing_period` varchar(10) DEFAULT NULL,
  `bank_name` varchar(50) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `account_reference` varchar(50) DEFAULT NULL,
  `payee_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_settings`
--

INSERT INTO `company_settings` (`id`, `company_name`, `company_address`, `company_email`, `company_website`, `company_phone`, `tax_rate`, `interest_rate`, `interest_billing_period`, `bank_name`, `account_number`, `account_reference`, `payee_name`) VALUES
(1, 'Omabra Credit', 'Nairobi', 'support@omabracredit.co.ke', 'https://omabracredit.co.ke', '254700000890', 30, 14.00, 'Month', 'Omabra Investments', '800900', 'Loan Account', 'Omabra Investments');

-- --------------------------------------------------------

--
-- Table structure for table `defaulted_loans`
--

CREATE TABLE `defaulted_loans` (
  `loan_id` varchar(10) NOT NULL,
  `client_id` varchar(10) NOT NULL,
  `national_id` varchar(20) NOT NULL,
  `requested_amount` decimal(10,2) NOT NULL,
  `loan_purpose` text NOT NULL,
  `duration` int(11) NOT NULL,
  `duration_period` enum('Week','Month','Year') NOT NULL,
  `date_applied` date NOT NULL,
  `collateral_name` varchar(100) NOT NULL,
  `collateral_value` decimal(10,2) NOT NULL,
  `collateral_pic1` varchar(255) NOT NULL,
  `collateral_pic2` varchar(255) NOT NULL,
  `guarantor1_name` varchar(100) NOT NULL,
  `guarantor1_phone` varchar(20) NOT NULL,
  `guarantor2_name` varchar(100) NOT NULL,
  `guarantor2_phone` varchar(20) NOT NULL,
  `loan_status` enum('Pending','Active','Rejected','Cleared','Defaulted') NOT NULL DEFAULT 'Defaulted',
  `onboarding_officer` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expected_payments`
--

CREATE TABLE `expected_payments` (
  `id` int(11) NOT NULL,
  `loan_id` varchar(10) NOT NULL,
  `installment_amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_status` enum('paid','not paid') NOT NULL DEFAULT 'not paid',
  `interest_income` decimal(10,2) NOT NULL,
  `principal` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expected_payments`
--

INSERT INTO `expected_payments` (`id`, `loan_id`, `installment_amount`, `payment_date`, `payment_status`, `interest_income`, `principal`, `created_at`) VALUES
(1, 'ln000002', 1900.00, '2024-10-13', 'not paid', 234.00, 1667.00, '2024-10-06 15:54:43'),
(2, 'ln000002', 1900.00, '2024-10-20', 'not paid', 234.00, 1667.00, '2024-10-06 15:54:43'),
(3, 'ln000002', 1900.00, '2024-10-27', 'not paid', 234.00, 1667.00, '2024-10-06 15:54:43'),
(4, 'ln000002', 1900.00, '2024-11-03', 'not paid', 234.00, 1667.00, '2024-10-06 15:54:43'),
(5, 'ln000002', 1900.00, '2024-11-10', 'not paid', 234.00, 1667.00, '2024-10-06 15:54:43'),
(6, 'ln000002', 1900.00, '2024-11-17', 'not paid', 234.00, 1667.00, '2024-10-06 15:54:43');

--
-- Triggers `expected_payments`
--
DELIMITER $$
CREATE TRIGGER `after_expected_payments_update` AFTER UPDATE ON `expected_payments` FOR EACH ROW BEGIN
    DECLARE loan_number BIGINT;
    DECLARE interest_amount DECIMAL(25,2);
    DECLARE principal_amount DECIMAL(25,2);
    DECLARE last_entry_id BIGINT;

    -- Check if payment_status has changed from 'not paid' to 'paid'
    IF OLD.payment_status = 'not paid' AND NEW.payment_status = 'paid' THEN
        -- Extract loan number (remove 'ln' prefix)
        SET loan_number = CAST(SUBSTRING(NEW.loan_id, 3) AS UNSIGNED);
        
        -- Get interest and principal amounts
        SET interest_amount = NEW.interest_income;
        SET principal_amount = NEW.principal;

        -- Insert into amentries for interest payment
        INSERT INTO amentries (entrytype_id, number, date, dr_total, cr_total, narration)
        VALUES 
            (1, loan_number, CURRENT_DATE(), interest_amount, interest_amount, CONCAT('Payment towards earned interest for loan ', NEW.loan_id)),
            (1, loan_number, CURRENT_DATE(), principal_amount, principal_amount, CONCAT('Payment towards principal for loan ', NEW.loan_id));
        
        -- Get the last inserted entry ID
        SET last_entry_id = LAST_INSERT_ID();

        -- Insert into amentryitems for the interest entry
        INSERT INTO amentryitems (entry_id, ledger_id, amount, dc)
        VALUES 
            (last_entry_id, 11, interest_amount, 'D'),
            (last_entry_id, 13, interest_amount, 'C');

        -- Insert into amentryitems for the principal entry
        INSERT INTO amentryitems (entry_id, ledger_id, amount, dc)
        VALUES 
            (last_entry_id + 1, 11, principal_amount, 'D'),
            (last_entry_id + 1, 12, principal_amount, 'C');
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `expense_item` varchar(255) NOT NULL,
  `expense_category` enum('Operating Expenses','Salaries','Interest on Loan','General Admin Costs','Other Office Costs') NOT NULL,
  `expense_date` date NOT NULL,
  `expense_amount` decimal(10,2) NOT NULL,
  `expense_status` enum('Paid','Not Paid') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `incomes`
--

CREATE TABLE `incomes` (
  `id` int(11) NOT NULL,
  `income_item` varchar(255) NOT NULL,
  `income_category` enum('Operating Income','Services Offered','Interest Income','Other Income') NOT NULL,
  `income_date` date NOT NULL,
  `income_amount` decimal(12,2) NOT NULL,
  `income_status` enum('Cash Received','Cash Not Received') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_applications`
--

CREATE TABLE `loan_applications` (
  `loan_id` varchar(10) NOT NULL,
  `client_id` varchar(10) NOT NULL,
  `national_id` varchar(20) NOT NULL,
  `requested_amount` decimal(10,2) NOT NULL,
  `loan_purpose` text NOT NULL,
  `duration` int(11) NOT NULL,
  `duration_period` enum('Week','Month','Year') NOT NULL,
  `date_applied` date NOT NULL,
  `collateral_name` varchar(100) NOT NULL,
  `collateral_value` decimal(10,2) NOT NULL,
  `collateral_pic1` varchar(255) NOT NULL,
  `collateral_pic2` varchar(255) NOT NULL,
  `guarantor1_name` varchar(100) NOT NULL,
  `guarantor1_phone` varchar(20) NOT NULL,
  `guarantor2_name` varchar(100) NOT NULL,
  `guarantor2_phone` varchar(20) NOT NULL,
  `loan_status` enum('Pending','Active','Rejected','Cleared','Defaulted') NOT NULL DEFAULT 'Pending',
  `onboarding_officer` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `loan_applications`
--
DELIMITER $$
CREATE TRIGGER `generate_loan_id` BEFORE INSERT ON `loan_applications` FOR EACH ROW BEGIN
    DECLARE next_id INT;
    
    -- Retrieve the last generated loan_id
    SET next_id = (SELECT last_id FROM loan_id_tracker FOR UPDATE) + 1;
    
    -- Update the tracker with the new loan_id
    UPDATE loan_id_tracker SET last_id = next_id;
    
    -- Set the new loan_id in the desired format
    SET NEW.loan_id = CONCAT('ln', LPAD(next_id, 6, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `loan_id_tracker`
--

CREATE TABLE `loan_id_tracker` (
  `id` int(11) NOT NULL,
  `last_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_id_tracker`
--

INSERT INTO `loan_id_tracker` (`id`, `last_id`) VALUES
(1, '2');

-- --------------------------------------------------------

--
-- Table structure for table `loan_info`
--

CREATE TABLE `loan_info` (
  `loan_id` varchar(10) NOT NULL,
  `client_id` varchar(10) NOT NULL,
  `national_id` varchar(20) NOT NULL,
  `requested_amount` decimal(10,2) NOT NULL,
  `loan_purpose` text NOT NULL,
  `duration` int(11) NOT NULL,
  `duration_period` enum('Week','Month','Year') NOT NULL,
  `date_applied` date NOT NULL,
  `collateral_name` varchar(100) NOT NULL,
  `collateral_value` decimal(10,2) NOT NULL,
  `collateral_pic1` varchar(255) NOT NULL,
  `collateral_pic2` varchar(255) NOT NULL,
  `guarantor1_name` varchar(100) NOT NULL,
  `guarantor1_phone` varchar(20) NOT NULL,
  `guarantor2_name` varchar(100) NOT NULL,
  `guarantor2_phone` varchar(20) NOT NULL,
  `loan_status` enum('Pending','Active','Rejected','Cleared','Defaulted') NOT NULL DEFAULT 'Active',
  `onboarding_officer` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_change` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_info`
--

INSERT INTO `loan_info` (`loan_id`, `client_id`, `national_id`, `requested_amount`, `loan_purpose`, `duration`, `duration_period`, `date_applied`, `collateral_name`, `collateral_value`, `collateral_pic1`, `collateral_pic2`, `guarantor1_name`, `guarantor1_phone`, `guarantor2_name`, `guarantor2_phone`, `loan_status`, `onboarding_officer`, `created_at`, `status_change`) VALUES
('ln000002', 'cl000001', '28351507', 10000.00, 'food', 6, 'Week', '2024-10-06', 'mtBike', 270000.00, '', '', '', '', '', '', 'Active', 'admin', '2024-10-06 15:54:43', '2024-10-06 15:54:43');

-- --------------------------------------------------------

--
-- Table structure for table `loan_stage`
--

CREATE TABLE `loan_stage` (
  `loan_id` varchar(10) NOT NULL,
  `stage` enum('Green Zone','Early Delinquency','Loan Loss Provision Recognised','Loan Loss Provision Unrecognised','Default Unrecognised','Default Recognised') NOT NULL DEFAULT 'Green Zone'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_status`
--

CREATE TABLE `loan_status` (
  `id` int(11) NOT NULL,
  `loan_id` varchar(10) NOT NULL,
  `national_id` varchar(50) NOT NULL,
  `collateral_status` varchar(50) NOT NULL,
  `loan_status` enum('Pending','Active','Rejected','Cleared','Defaulted') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `managers`
--

CREATE TABLE `managers` (
  `manager_id` varchar(10) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `county` varchar(100) NOT NULL,
  `town_centre` varchar(100) NOT NULL,
  `national_id` varchar(20) NOT NULL,
  `id_photo_front` varchar(255) NOT NULL,
  `id_photo_back` varchar(255) NOT NULL,
  `nssf` varchar(20) NOT NULL,
  `nhif` varchar(20) NOT NULL,
  `kra_pin` varchar(20) NOT NULL,
  `date_of_onboarding` date NOT NULL,
  `residence_nearest_building` varchar(255) NOT NULL,
  `residence_nearest_road` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `next_of_kin_name` varchar(255) NOT NULL,
  `next_of_kin_phone_number` varchar(20) NOT NULL,
  `next_of_kin_relation` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `managers`
--
DELIMITER $$
CREATE TRIGGER `generate_manager_id` BEFORE INSERT ON `managers` FOR EACH ROW BEGIN
    DECLARE next_id INT;
    SET next_id = COALESCE((SELECT MAX(CAST(SUBSTRING(manager_id, 3) AS UNSIGNED)) FROM managers), 0) + 1;
    SET NEW.manager_id = CONCAT('mn', LPAD(next_id, 3, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `mpesa_transactions`
--

CREATE TABLE `mpesa_transactions` (
  `id` int(11) NOT NULL,
  `transaction_id` varchar(50) DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `mpesa_receipt_number` varchar(20) DEFAULT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Completed','Failed') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `heading` varchar(255) NOT NULL DEFAULT 'Topic'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `created_at`, `heading`) VALUES
(1, 20, 'Hi, Joseph Lweya! Your application has been received and is awaiting approval.', 0, '2024-10-06 15:53:55', 'Loan Application for Joseph Lweya'),
(2, 17, 'Loan application has been received and is awaiting approval. Please APPROVE.', 0, '2024-10-06 15:53:55', 'Loan Application for Joseph Lweya'),
(3, 20, 'Welcome, Joseph Lweya! Your loan account has been credited.', 0, '2024-10-06 15:54:43', 'Loan Approved!');

-- --------------------------------------------------------

--
-- Table structure for table `oc24entries`
--

CREATE TABLE `oc24entries` (
  `id` bigint(18) NOT NULL,
  `tag_id` bigint(18) DEFAULT NULL,
  `entrytype_id` bigint(18) NOT NULL,
  `number` bigint(18) DEFAULT NULL,
  `date` date NOT NULL,
  `dr_total` decimal(25,2) NOT NULL DEFAULT 0.00,
  `cr_total` decimal(25,2) NOT NULL DEFAULT 0.00,
  `narration` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `oc24entries`
--

INSERT INTO `oc24entries` (`id`, `tag_id`, `entrytype_id`, `number`, `date`, `dr_total`, `cr_total`, `narration`) VALUES
(1, NULL, 2, 2, '2024-10-06', 10000.00, 10000.00, 'Disbursement to client Joseph Lweya for loan number ln000002');

-- --------------------------------------------------------

--
-- Table structure for table `oc24entryitems`
--

CREATE TABLE `oc24entryitems` (
  `id` bigint(18) NOT NULL,
  `entry_id` bigint(18) NOT NULL,
  `ledger_id` bigint(18) NOT NULL,
  `amount` decimal(25,2) NOT NULL DEFAULT 0.00,
  `dc` char(1) NOT NULL,
  `reconciliation_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `oc24entryitems`
--

INSERT INTO `oc24entryitems` (`id`, `entry_id`, `ledger_id`, `amount`, `dc`, `reconciliation_date`) VALUES
(1, 1, 11, 10000.00, 'C', NULL),
(2, 1, 12, 10000.00, 'D', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `oc24entrytypes`
--

CREATE TABLE `oc24entrytypes` (
  `id` bigint(18) NOT NULL,
  `label` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `base_type` int(2) NOT NULL DEFAULT 0,
  `numbering` int(2) NOT NULL DEFAULT 1,
  `prefix` varchar(255) NOT NULL,
  `suffix` varchar(255) NOT NULL,
  `zero_padding` int(2) NOT NULL DEFAULT 0,
  `restriction_bankcash` int(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `oc24entrytypes`
--

INSERT INTO `oc24entrytypes` (`id`, `label`, `name`, `description`, `base_type`, `numbering`, `prefix`, `suffix`, `zero_padding`, `restriction_bankcash`) VALUES
(1, 'receipt', 'Receipt', 'Received in Bank account or Cash account', 1, 1, '', '', 0, 2),
(2, 'payment', 'Payment', 'Payment made from Bank account or Cash account', 1, 1, '', '', 0, 3),
(3, 'contra', 'Contra', 'Transfer between Bank account and Cash account', 1, 1, '', '', 0, 4),
(4, 'journal', 'Journal', 'Transaction that does not involve a Bank account or Cash account', 1, 1, '', '', 0, 5);

-- --------------------------------------------------------

--
-- Table structure for table `oc24groups`
--

CREATE TABLE `oc24groups` (
  `id` bigint(18) NOT NULL,
  `parent_id` bigint(18) DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `affects_gross` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `oc24groups`
--

INSERT INTO `oc24groups` (`id`, `parent_id`, `name`, `code`, `affects_gross`) VALUES
(1, NULL, 'Assets', NULL, 0),
(2, NULL, 'Liabilities and Owners Equity', NULL, 0),
(3, NULL, 'Incomes', NULL, 0),
(4, NULL, 'Expenses', NULL, 0),
(5, 1, 'Fixed Assets', NULL, 0),
(6, 1, 'Current Assets', NULL, 0),
(7, 1, 'Investments', NULL, 0),
(8, 2, 'Capital Account', NULL, 0),
(9, 2, 'Current Liabilities', NULL, 0),
(10, 2, 'Loans (Liabilities)', NULL, 0),
(11, 3, 'Direct Incomes', NULL, 1),
(12, 4, 'Direct Expenses', NULL, 1),
(13, 3, 'Indirect Incomes', NULL, 0),
(14, 4, 'Indirect Expenses', NULL, 0),
(15, 3, 'Sales', NULL, 1),
(16, 4, 'Purchases', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `oc24ledgers`
--

CREATE TABLE `oc24ledgers` (
  `id` bigint(18) NOT NULL,
  `group_id` bigint(18) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `op_balance` decimal(25,2) NOT NULL DEFAULT 0.00,
  `op_balance_dc` char(1) NOT NULL,
  `type` int(2) NOT NULL DEFAULT 0,
  `reconciliation` int(1) NOT NULL DEFAULT 0,
  `notes` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `oc24ledgers`
--

INSERT INTO `oc24ledgers` (`id`, `group_id`, `name`, `code`, `op_balance`, `op_balance_dc`, `type`, `reconciliation`, `notes`) VALUES
(1, 11, 'Interest Income on Loans', NULL, 0.00, 'C', 0, 0, ''),
(7, 14, 'Depreciation', NULL, 0.00, 'D', 0, 0, ''),
(8, 12, 'Interest Expense', NULL, 0.00, 'D', 0, 0, ''),
(9, 12, 'Commissions/Broker fees', NULL, 0.00, 'D', 0, 0, ''),
(10, 6, 'Cash In Hand', NULL, 0.00, 'D', 1, 0, ''),
(11, 6, 'Cash At Bank', NULL, 0.00, 'D', 1, 0, ''),
(12, 6, 'Loans Receivable (Current Portion)', NULL, 0.00, 'D', 0, 0, ''),
(13, 6, 'Interest Receivable', NULL, 0.00, 'D', 0, 0, ''),
(14, 6, 'Prepaid Expenses', NULL, 0.00, 'D', 0, 0, ''),
(15, 5, 'Loans Receivable (Non-Current Portion)', NULL, 0.00, 'D', 0, 0, ''),
(16, 5, 'Property and Equipment', NULL, 0.00, 'D', 0, 0, ''),
(17, 9, 'Accounts Payable', NULL, 0.00, 'C', 0, 0, ''),
(18, 9, 'Interest Payable', NULL, 0.00, 'C', 0, 0, ''),
(19, 9, 'Accrued Expenses', NULL, 0.00, 'C', 0, 0, ''),
(20, 9, 'Loan Payable (Current Portion)', NULL, 0.00, 'C', 0, 0, ''),
(21, 10, 'Loan Payable (Non-Current Portion)', NULL, 0.00, 'C', 0, 0, ''),
(22, 8, 'Owner\'s Capital', NULL, 0.00, 'C', 0, 0, ''),
(23, 8, 'Retained Earnings', NULL, 0.00, 'C', 0, 0, ''),
(24, 11, 'Fee Income (Processing/Administrative Fees)', NULL, 0.00, 'C', 0, 0, ''),
(25, 14, 'Salaries and Wages', NULL, 0.00, 'D', 0, 0, ''),
(26, 14, 'Rent Expense', NULL, 0.00, 'D', 0, 0, ''),
(27, 14, 'Utilities Expense', NULL, 0.00, 'D', 0, 0, ''),
(28, 14, 'Office Supplies', NULL, 0.00, 'D', 0, 0, ''),
(29, 14, 'Loan Loss Provision (Reserve for Bad Debts)', NULL, 0.00, 'D', 0, 0, ''),
(30, 9, 'Allowance for Doubtful Accounts', NULL, 0.00, 'C', 0, 0, ''),
(31, 13, 'Gain on Sale of Collateral', NULL, 0.00, 'C', 0, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `oc24logs`
--

CREATE TABLE `oc24logs` (
  `id` bigint(18) NOT NULL,
  `date` datetime NOT NULL,
  `level` int(1) NOT NULL,
  `host_ip` varchar(25) NOT NULL,
  `user` varchar(25) NOT NULL,
  `url` varchar(255) NOT NULL,
  `user_agent` varchar(100) NOT NULL,
  `message` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oc24settings`
--

CREATE TABLE `oc24settings` (
  `id` int(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `fy_start` date NOT NULL,
  `fy_end` date NOT NULL,
  `currency_symbol` varchar(100) NOT NULL,
  `currency_format` varchar(100) NOT NULL,
  `decimal_places` int(2) NOT NULL DEFAULT 2,
  `date_format` varchar(100) NOT NULL,
  `timezone` varchar(100) NOT NULL,
  `manage_inventory` int(1) NOT NULL DEFAULT 0,
  `account_locked` int(1) NOT NULL DEFAULT 0,
  `email_use_default` int(1) NOT NULL DEFAULT 0,
  `email_protocol` varchar(10) NOT NULL,
  `email_host` varchar(255) NOT NULL,
  `email_port` int(5) NOT NULL,
  `email_tls` int(1) NOT NULL DEFAULT 0,
  `email_username` varchar(255) NOT NULL,
  `email_password` varchar(255) NOT NULL,
  `email_from` varchar(255) NOT NULL,
  `print_paper_height` decimal(10,3) NOT NULL DEFAULT 0.000,
  `print_paper_width` decimal(10,3) NOT NULL DEFAULT 0.000,
  `print_margin_top` decimal(10,3) NOT NULL DEFAULT 0.000,
  `print_margin_bottom` decimal(10,3) NOT NULL DEFAULT 0.000,
  `print_margin_left` decimal(10,3) NOT NULL DEFAULT 0.000,
  `print_margin_right` decimal(10,3) NOT NULL DEFAULT 0.000,
  `print_orientation` char(1) NOT NULL,
  `print_page_format` char(1) NOT NULL,
  `database_version` int(10) NOT NULL,
  `settings` varchar(2048) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `oc24settings`
--

INSERT INTO `oc24settings` (`id`, `name`, `address`, `email`, `fy_start`, `fy_end`, `currency_symbol`, `currency_format`, `decimal_places`, `date_format`, `timezone`, `manage_inventory`, `account_locked`, `email_use_default`, `email_protocol`, `email_host`, `email_port`, `email_tls`, `email_username`, `email_password`, `email_from`, `print_paper_height`, `print_paper_width`, `print_margin_top`, `print_margin_bottom`, `print_margin_left`, `print_margin_right`, `print_orientation`, `print_page_format`, `database_version`, `settings`) VALUES
(1, 'Omabra Credit', '', 'support@omabracredit.co.ke', '2024-01-01', '2024-12-31', 'Kes', '###,###.##', 2, 'd-M-Y|dd-M-yy', 'UTC', 0, 0, 1, 'Smtp', '', 0, 0, '', '', '', 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 'P', 'H', 6, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `oc24tags`
--

CREATE TABLE `oc24tags` (
  `id` bigint(18) NOT NULL,
  `title` varchar(255) NOT NULL,
  `color` char(6) NOT NULL,
  `background` char(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `loan_id` varchar(10) NOT NULL,
  `national_id` varchar(20) NOT NULL,
  `transaction_reference` varchar(50) NOT NULL,
  `payment_mode` varchar(50) NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `payments`
--
DELIMITER $$
CREATE TRIGGER `after_payment_insert` AFTER INSERT ON `payments` FOR EACH ROW BEGIN
    DECLARE remaining_amount DECIMAL(10,2);
    DECLARE next_installment DECIMAL(10,2);
    DECLARE next_id INT DEFAULT 0;
    DECLARE done INT DEFAULT 0;

    -- Declare a cursor to select the next unpaid installment
    DECLARE cur CURSOR FOR
        SELECT id, installment_amount
        FROM expected_payments
        WHERE loan_id = NEW.loan_id AND payment_status = 'not paid'
        ORDER BY payment_date ASC;

    -- Handler to exit the loop when no more rows are found
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    -- Step 1: Calculate the remaining amount (total payments minus paid installments)
    SELECT (IFNULL(SUM(amount), 0) - IFNULL(SUM(installment_amount), 0)) INTO remaining_amount
    FROM payments p
    JOIN expected_payments ep ON p.loan_id = ep.loan_id
    WHERE p.loan_id = NEW.loan_id AND ep.payment_status = 'paid';

    -- Step 2: Open the cursor to loop through unpaid installments
    OPEN cur;
    
    payment_loop: LOOP
        FETCH cur INTO next_id, next_installment;

        -- Exit loop when done
        IF done THEN
            LEAVE payment_loop;
        END IF;

        -- Step 3: Check if remaining amount is greater than or equal to the next installment
        IF remaining_amount >= next_installment THEN
            -- Mark the installment as 'paid'
            UPDATE expected_payments
            SET payment_status = 'paid'
            WHERE id = next_id;

            -- Deduct the installment amount from remaining amount
            SET remaining_amount = remaining_amount - next_installment;
        ELSE
            -- If remaining amount is smaller than the next installment, stop the loop
            LEAVE payment_loop;
        END IF;
    END LOOP;
    
    -- Close the cursor after looping through unpaid installments
    CLOSE cur;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `rejected_loans`
--

CREATE TABLE `rejected_loans` (
  `loan_id` varchar(10) NOT NULL,
  `client_id` varchar(10) NOT NULL,
  `national_id` varchar(20) NOT NULL,
  `requested_amount` decimal(10,2) NOT NULL,
  `loan_purpose` text NOT NULL,
  `duration` int(11) NOT NULL,
  `duration_period` enum('Week','Month','Year') NOT NULL,
  `date_applied` date NOT NULL,
  `collateral_name` varchar(100) NOT NULL,
  `collateral_value` decimal(10,2) NOT NULL,
  `collateral_pic1` varchar(255) NOT NULL,
  `collateral_pic2` varchar(255) NOT NULL,
  `guarantor1_name` varchar(100) NOT NULL,
  `guarantor1_phone` varchar(20) NOT NULL,
  `guarantor2_name` varchar(100) NOT NULL,
  `guarantor2_phone` varchar(20) NOT NULL,
  `loan_status` enum('Pending','Active','Rejected','Cleared','Defaulted') NOT NULL DEFAULT 'Rejected',
  `onboarding_officer` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sqwzaccounts`
--

CREATE TABLE `sqwzaccounts` (
  `id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `db_datasource` varchar(255) DEFAULT NULL,
  `db_database` varchar(255) DEFAULT NULL,
  `db_host` varchar(255) DEFAULT NULL,
  `db_port` int(11) DEFAULT NULL,
  `db_login` varchar(255) DEFAULT NULL,
  `db_password` varchar(255) DEFAULT NULL,
  `db_prefix` varchar(255) DEFAULT NULL,
  `db_persistent` varchar(255) DEFAULT NULL,
  `db_schema` varchar(255) DEFAULT NULL,
  `db_unixsocket` varchar(255) DEFAULT NULL,
  `db_settings` varchar(255) DEFAULT NULL,
  `ssl_key` varchar(255) DEFAULT NULL,
  `ssl_cert` varchar(255) DEFAULT NULL,
  `ssl_ca` varchar(255) DEFAULT NULL,
  `hidden` int(1) NOT NULL DEFAULT 0,
  `others` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sqwzaccounts`
--

INSERT INTO `sqwzaccounts` (`id`, `label`, `db_datasource`, `db_database`, `db_host`, `db_port`, `db_login`, `db_password`, `db_prefix`, `db_persistent`, `db_schema`, `db_unixsocket`, `db_settings`, `ssl_key`, `ssl_cert`, `ssl_ca`, `hidden`, `others`) VALUES
(1, 'omabraaccounts2024', 'Database/Mysql', 'omabracredit_loan-management-system', 'localhost', 3306, 'root', '', 'oc24', '0', '', '', '', NULL, NULL, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sqwzsettings`
--

CREATE TABLE `sqwzsettings` (
  `id` int(11) NOT NULL,
  `sitename` varchar(255) DEFAULT NULL,
  `drcr_toby` varchar(255) DEFAULT NULL,
  `enable_logging` int(1) NOT NULL DEFAULT 0,
  `row_count` int(11) NOT NULL DEFAULT 10,
  `user_registration` int(1) NOT NULL DEFAULT 0,
  `admin_verification` int(1) NOT NULL DEFAULT 0,
  `email_verification` int(1) NOT NULL DEFAULT 0,
  `email_protocol` varchar(255) DEFAULT NULL,
  `email_host` varchar(255) DEFAULT NULL,
  `email_port` int(11) DEFAULT 0,
  `email_tls` int(1) DEFAULT 0,
  `email_username` varchar(255) DEFAULT NULL,
  `email_password` varchar(255) DEFAULT NULL,
  `email_from` varchar(255) DEFAULT NULL,
  `others` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sqwzsettings`
--

INSERT INTO `sqwzsettings` (`id`, `sitename`, `drcr_toby`, `enable_logging`, `row_count`, `user_registration`, `admin_verification`, `email_verification`, `email_protocol`, `email_host`, `email_port`, `email_tls`, `email_username`, `email_password`, `email_from`, `others`) VALUES
(1, 'Omabra Credit', 'drcr', 0, 10, 0, 1, 0, 'Smtp', 'smtp.omabracredit.co.ke', 0, 0, '', '', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sqwzuseraccounts`
--

CREATE TABLE `sqwzuseraccounts` (
  `id` int(11) NOT NULL,
  `wzuser_id` int(11) NOT NULL,
  `wzaccount_id` int(11) NOT NULL,
  `role` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sqwzusers`
--

CREATE TABLE `sqwzusers` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `timezone` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT 0,
  `verification_key` varchar(255) DEFAULT NULL,
  `email_verified` int(1) NOT NULL DEFAULT 0,
  `admin_verified` int(1) NOT NULL DEFAULT 0,
  `retry_count` int(1) NOT NULL DEFAULT 0,
  `all_accounts` int(1) NOT NULL DEFAULT 0,
  `default_account` int(11) NOT NULL DEFAULT 0,
  `others` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sqwzusers`
--

INSERT INTO `sqwzusers` (`id`, `username`, `password`, `fullname`, `email`, `timezone`, `role`, `status`, `verification_key`, `email_verified`, `admin_verified`, `retry_count`, `all_accounts`, `default_account`, `others`) VALUES
(1, 'admin', 'daedeb986e6f2645835bb839bbf81559813c956d', 'Omabra Credit', 'admin@squarehaul.online', 'UTC', 'admin', 1, '', 1, 1, 0, 1, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `file_no` varchar(10) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manager','client') NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `file_no`, `email`, `password`, `role`, `reset_token`, `created_at`) VALUES
(17, 'ADMIN', '001', 'ad001', 'admin@admin.com', '$2y$10$Ov7qrLEA2VsZic5r4Xix1ew8QGfwUVkcZsfw.i2/kyDl3uQ5.Frli', 'admin', '', '2024-09-04 11:15:01'),
(19, 'Manager', '001', 'mn001', 'manager@manager.com', '$2y$10$xF8slDUNAPecehvpPgkqcetg9vgkV1Tqjd7fcLE5Uh/OZgU3/T1Qu', 'manager', '263851ae088285298e857ef741a01167', '2024-09-04 11:39:15'),
(20, 'Joseph', 'Lweya', 'cl000001', 'joseph.lweya@outlook.com', '$2y$10$4frsbV7HVtrNIy9.s5GyZ.SeM4vg4q4jawlMsvJ8EVAYlrEELk8De', 'client', '', '2024-09-04 12:05:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `active_loans`
--
ALTER TABLE `active_loans`
  ADD PRIMARY KEY (`loan_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `call_back_contacts`
--
ALTER TABLE `call_back_contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cleared_loans`
--
ALTER TABLE `cleared_loans`
  ADD PRIMARY KEY (`loan_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`client_id`),
  ADD UNIQUE KEY `national_id` (`national_id`);

--
-- Indexes for table `company_settings`
--
ALTER TABLE `company_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `defaulted_loans`
--
ALTER TABLE `defaulted_loans`
  ADD PRIMARY KEY (`loan_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `expected_payments`
--
ALTER TABLE `expected_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_loan_id` (`loan_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `incomes`
--
ALTER TABLE `incomes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_applications`
--
ALTER TABLE `loan_applications`
  ADD PRIMARY KEY (`loan_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `loan_id_tracker`
--
ALTER TABLE `loan_id_tracker`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_info`
--
ALTER TABLE `loan_info`
  ADD PRIMARY KEY (`loan_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `loan_stage`
--
ALTER TABLE `loan_stage`
  ADD PRIMARY KEY (`loan_id`);

--
-- Indexes for table `loan_status`
--
ALTER TABLE `loan_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loan_id` (`loan_id`);

--
-- Indexes for table `managers`
--
ALTER TABLE `managers`
  ADD PRIMARY KEY (`manager_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `national_id` (`national_id`),
  ADD UNIQUE KEY `kra_pin` (`kra_pin`);

--
-- Indexes for table `mpesa_transactions`
--
ALTER TABLE `mpesa_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_ibfk_1` (`user_id`);

--
-- Indexes for table `oc24entries`
--
ALTER TABLE `oc24entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`id`),
  ADD KEY `id` (`id`),
  ADD KEY `tag_id` (`tag_id`),
  ADD KEY `entrytype_id` (`entrytype_id`);

--
-- Indexes for table `oc24entryitems`
--
ALTER TABLE `oc24entryitems`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`id`),
  ADD KEY `id` (`id`),
  ADD KEY `entry_id` (`entry_id`),
  ADD KEY `ledger_id` (`ledger_id`);

--
-- Indexes for table `oc24entrytypes`
--
ALTER TABLE `oc24entrytypes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`id`),
  ADD UNIQUE KEY `label` (`label`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `oc24groups`
--
ALTER TABLE `oc24groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `id` (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `oc24ledgers`
--
ALTER TABLE `oc24ledgers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `id` (`id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `oc24logs`
--
ALTER TABLE `oc24logs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `oc24settings`
--
ALTER TABLE `oc24settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `oc24tags`
--
ALTER TABLE `oc24tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`id`),
  ADD UNIQUE KEY `title` (`title`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `loan_id` (`loan_id`);

--
-- Indexes for table `rejected_loans`
--
ALTER TABLE `rejected_loans`
  ADD PRIMARY KEY (`loan_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `sqwzaccounts`
--
ALTER TABLE `sqwzaccounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sqwzsettings`
--
ALTER TABLE `sqwzsettings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sqwzuseraccounts`
--
ALTER TABLE `sqwzuseraccounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sqwzuseraccounts_fk_check_wzuser_id` (`wzuser_id`),
  ADD KEY `sqwzuseraccounts_fk_check_wzaccount_id` (`wzaccount_id`);

--
-- Indexes for table `sqwzusers`
--
ALTER TABLE `sqwzusers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `file_no` (`file_no`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `call_back_contacts`
--
ALTER TABLE `call_back_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_settings`
--
ALTER TABLE `company_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `expected_payments`
--
ALTER TABLE `expected_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `incomes`
--
ALTER TABLE `incomes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loan_id_tracker`
--
ALTER TABLE `loan_id_tracker`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `loan_status`
--
ALTER TABLE `loan_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mpesa_transactions`
--
ALTER TABLE `mpesa_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `oc24entries`
--
ALTER TABLE `oc24entries`
  MODIFY `id` bigint(18) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `oc24entryitems`
--
ALTER TABLE `oc24entryitems`
  MODIFY `id` bigint(18) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `oc24entrytypes`
--
ALTER TABLE `oc24entrytypes`
  MODIFY `id` bigint(18) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `oc24groups`
--
ALTER TABLE `oc24groups`
  MODIFY `id` bigint(18) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `oc24ledgers`
--
ALTER TABLE `oc24ledgers`
  MODIFY `id` bigint(18) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `oc24logs`
--
ALTER TABLE `oc24logs`
  MODIFY `id` bigint(18) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `oc24tags`
--
ALTER TABLE `oc24tags`
  MODIFY `id` bigint(18) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sqwzaccounts`
--
ALTER TABLE `sqwzaccounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sqwzsettings`
--
ALTER TABLE `sqwzsettings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sqwzuseraccounts`
--
ALTER TABLE `sqwzuseraccounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sqwzusers`
--
ALTER TABLE `sqwzusers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `active_loans`
--
ALTER TABLE `active_loans`
  ADD CONSTRAINT `active_loans_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `cleared_loans`
--
ALTER TABLE `cleared_loans`
  ADD CONSTRAINT `cleared_loans_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `defaulted_loans`
--
ALTER TABLE `defaulted_loans`
  ADD CONSTRAINT `defaulted_loans_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `expected_payments`
--
ALTER TABLE `expected_payments`
  ADD CONSTRAINT `fk_loan_id` FOREIGN KEY (`loan_id`) REFERENCES `loan_info` (`loan_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `loan_applications`
--
ALTER TABLE `loan_applications`
  ADD CONSTRAINT `loan_applications_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `loan_info`
--
ALTER TABLE `loan_info`
  ADD CONSTRAINT `loan_info_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `loan_stage`
--
ALTER TABLE `loan_stage`
  ADD CONSTRAINT `fk_loan_stage_loan` FOREIGN KEY (`loan_id`) REFERENCES `loan_info` (`loan_id`);

--
-- Constraints for table `loan_status`
--
ALTER TABLE `loan_status`
  ADD CONSTRAINT `loan_status_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loan_info` (`loan_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `oc24entries`
--
ALTER TABLE `oc24entries`
  ADD CONSTRAINT `oc24entries_fk_check_entrytype_id` FOREIGN KEY (`entrytype_id`) REFERENCES `oc24entrytypes` (`id`),
  ADD CONSTRAINT `oc24entries_fk_check_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `oc24tags` (`id`);

--
-- Constraints for table `oc24entryitems`
--
ALTER TABLE `oc24entryitems`
  ADD CONSTRAINT `24entryitems_fk_check_entry_id` FOREIGN KEY (`entry_id`) REFERENCES `oc24entries` (`id`),
  ADD CONSTRAINT `24entryitems_fk_check_ledger_id` FOREIGN KEY (`ledger_id`) REFERENCES `oc24ledgers` (`id`);

--
-- Constraints for table `oc24groups`
--
ALTER TABLE `oc24groups`
  ADD CONSTRAINT `24groups_fk_check_parent_id` FOREIGN KEY (`parent_id`) REFERENCES `oc24groups` (`id`);

--
-- Constraints for table `oc24ledgers`
--
ALTER TABLE `oc24ledgers`
  ADD CONSTRAINT `amledgers_fk_check_group_id` FOREIGN KEY (`group_id`) REFERENCES `amgroups` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loan_info` (`loan_id`);

--
-- Constraints for table `rejected_loans`
--
ALTER TABLE `rejected_loans`
  ADD CONSTRAINT `rejected_loans_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `sqwzuseraccounts`
--
ALTER TABLE `sqwzuseraccounts`
  ADD CONSTRAINT `sqwzuseraccounts_fk_check_wzaccount_id` FOREIGN KEY (`wzaccount_id`) REFERENCES `sqwzaccounts` (`id`),
  ADD CONSTRAINT `sqwzuseraccounts_fk_check_wzuser_id` FOREIGN KEY (`wzuser_id`) REFERENCES `sqwzusers` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
