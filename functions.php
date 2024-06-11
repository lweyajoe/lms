<?php
require_once("config.php");

function generatePassword() {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr(str_shuffle($chars), 0, 10);
}

function managerExists($conn, $email, $phone, $nationalId) {
    $stmt = $conn->prepare("SELECT * FROM managers WHERE email = ? OR phone_number = ? OR national_id = ?");
    $stmt->bind_param("sss", $email, $phone, $nationalId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

function clientExists($conn, $email, $phone, $nationalId) {
    $stmt = $conn->prepare("SELECT * FROM clients WHERE email = ? OR phone_number = ? OR national_id = ?");
    $stmt->bind_param("sss", $email, $phone, $nationalId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

function getOnboardingOfficer($conn) {
    if (isset($_SESSION['role'])) {
        if ($_SESSION['role'] === 'admin') {
            return 'admin';
        } elseif ($_SESSION['role'] === 'manager' && isset($_SESSION['email'])) {
            return $_SESSION['email'];
        }
    }
    return '';
}

// Function to generate a summary report for business performance
function generateBusinessPerformanceReport($conn) {
    // Total loans disbursed
    $totalLoansSql = "
        SELECT COUNT(*) AS total_loans, SUM(requested_amount) AS total_amount_disbursed
        FROM loan_info WHERE loan_status IN ('Active', 'Cleared', 'Defaulted')
    ";
    $totalLoansResult = $conn->query($totalLoansSql);
    $totalLoansData = $totalLoansResult->fetch_assoc();

    // Total interest earned
    $totalInterestSql = "
        SELECT SUM(
            CASE
                WHEN interest_rate_period = 'Yearly' THEN requested_amount * (interest_rate / 100) * duration * 
                    CASE duration_period
                        WHEN 'Year' THEN 1
                        WHEN 'Month' THEN 1/12
                        WHEN 'Week' THEN 1/52
                    END
                WHEN interest_rate_period = 'Monthly' THEN requested_amount * (interest_rate / 100) * duration * 
                    CASE duration_period
                        WHEN 'Year' THEN 12
                        WHEN 'Month' THEN 1
                        WHEN 'Week' THEN 1/4
                    END
                WHEN interest_rate_period = 'Weekly' THEN requested_amount * (interest_rate / 100) * duration * 
                    CASE duration_period
                        WHEN 'Year' THEN 52
                        WHEN 'Month' THEN 4
                        WHEN 'Week' THEN 1
                    END
            END
        ) AS total_interest_earned
        FROM loan_info
        WHERE loan_status IN ('Active', 'Cleared', 'Defaulted')
    ";
    $totalInterestResult = $conn->query($totalInterestSql);
    $totalInterestData = $totalInterestResult->fetch_assoc();

    // Total payments received
    $totalPaymentsSql = "SELECT SUM(amount) AS total_payments_received FROM payments";
    $totalPaymentsResult = $conn->query($totalPaymentsSql);
    $totalPaymentsData = $totalPaymentsResult->fetch_assoc();

    // Return the report data
    return [
        'total_loans' => $totalLoansData['total_loans'],
        'total_amount_disbursed' => $totalLoansData['total_amount_disbursed'],
        'total_interest_earned' => $totalInterestData['total_interest_earned'],
        'total_payments_received' => $totalPaymentsData['total_payments_received']
    ];
}

function calculateAccruedInterest($requested_amount, $interest_rate, $interest_rate_period, $created_at, $duration_period) {
    $current_date = new DateTime();
    $loan_start_date = new DateTime($created_at);
    $interval = $loan_start_date->diff($current_date);

    // Determine the number of periods that have elapsed
    switch ($interest_rate_period) {
        case 'Yearly':
            $elapsed_periods = $interval->y + $interval->m / 12 + $interval->d / 365;
            break;
        case 'Monthly':
            $elapsed_periods = $interval->y * 12 + $interval->m + $interval->d / 30;
            break;
        case 'Weekly':
            $elapsed_periods = $interval->days / 7;
            break;
        default:
            $elapsed_periods = 0;
            break;
    }

    // Calculate accrued interest
    $accrued_interest = $requested_amount * ($interest_rate / 100) * $elapsed_periods;

    return $accrued_interest;
}

function getAccruedInterestData($conn) {
    $sql = "SELECT loan_id, client_id, requested_amount, interest_rate, interest_rate_period, created_at, duration, duration_period FROM active_loans WHERE loan_status = 'Active'";
    $result = $conn->query($sql);
    $accruedInterestData = [];

    while ($row = $result->fetch_assoc()) {
        $accrued_interest = calculateAccruedInterest(
            $row['requested_amount'],
            $row['interest_rate'],
            $row['interest_rate_period'],
            $row['created_at'],
            $row['duration_period']
        );

        $accruedInterestData[] = [
            'loan_id' => $row['loan_id'],
            'client_id' => $row['client_id'],
            'requested_amount' => $row['requested_amount'],
            'interest_rate' => $row['interest_rate'],
            'interest_rate_period' => $row['interest_rate_period'],
            'created_at' => $row['created_at'],
            'accrued_interest' => $accrued_interest
        ];
    }

    return $accruedInterestData;
}

function generateTodayBusinessPerformanceReport($conn) {
    $today = date('Y-m-d');

    // Total loans disbursed today
    $totalLoansTodaySql = "
        SELECT COUNT(*) AS total_loans_today, SUM(requested_amount) AS total_amount_disbursed_today
        FROM active_loans
        WHERE DATE(created_at) = '$today'
    ";
    $totalLoansTodayResult = $conn->query($totalLoansTodaySql);
    $totalLoansTodayData = $totalLoansTodayResult->fetch_assoc();

    // Total interest earned today
    $totalInterestTodaySql = "
        SELECT SUM(
            CASE
                WHEN interest_rate_period = 'Yearly' THEN requested_amount * (interest_rate / 100) / 365
                WHEN interest_rate_period = 'Monthly' THEN requested_amount * (interest_rate / 100) / 30
                WHEN interest_rate_period = 'Weekly' THEN requested_amount * (interest_rate / 100) / 7
            END
        ) AS total_interest_earned_today
        FROM active_loans
        WHERE DATE(created_at) = '$today' AND loan_status IN ('Active', 'Cleared')
    ";
    $totalInterestTodayResult = $conn->query($totalInterestTodaySql);
    $totalInterestTodayData = $totalInterestTodayResult->fetch_assoc();

    // Total payments received today
    $totalPaymentsTodaySql = "SELECT SUM(amount) AS total_payments_received_today FROM payments WHERE DATE(payment_date) = '$today'";
    $totalPaymentsTodayResult = $conn->query($totalPaymentsTodaySql);
    $totalPaymentsTodayData = $totalPaymentsTodayResult->fetch_assoc();

    return [
        'total_loans_today' => $totalLoansTodayData['total_loans_today'],
        'total_amount_disbursed_today' => $totalLoansTodayData['total_amount_disbursed_today'],
        'total_interest_earned_today' => $totalInterestTodayData['total_interest_earned_today'],
        'total_payments_received_today' => $totalPaymentsTodayData['total_payments_received_today']
    ];
}

function generateManagerBusinessPerformanceReport($conn, $onboardingOfficer) {
    // Total loans disbursed by the specific manager
    $totalLoansSql = "
        SELECT COUNT(*) AS total_loans, SUM(requested_amount) AS total_amount_disbursed
        FROM active_loans
        WHERE onboarding_officer = '$onboardingOfficer'
    ";
    $totalLoansResult = $conn->query($totalLoansSql);
    $totalLoansData = $totalLoansResult->fetch_assoc();

    // Total interest earned by the specific manager
    $totalInterestSql = "
        SELECT SUM(
            CASE
                WHEN interest_rate_period = 'Yearly' THEN requested_amount * (interest_rate / 100) / 365 * DATEDIFF(CURRENT_DATE, created_at)
                WHEN interest_rate_period = 'Monthly' THEN requested_amount * (interest_rate / 100) / 30 * DATEDIFF(CURRENT_DATE, created_at)
                WHEN interest_rate_period = 'Weekly' THEN requested_amount * (interest_rate / 100) / 7 * DATEDIFF(CURRENT_DATE, created_at)
            END
        ) AS total_interest_earned
        FROM active_loans
        WHERE onboarding_officer = '$onboardingOfficer' AND loan_status IN ('Active', 'Cleared')
    ";
    $totalInterestResult = $conn->query($totalInterestSql);
    $totalInterestData = $totalInterestResult->fetch_assoc();

    // Total payments received
    $totalPaymentsSql = "
        SELECT SUM(amount) AS total_payments_received 
        FROM payments 
        WHERE loan_id IN (SELECT loan_id FROM active_loans WHERE onboarding_officer = '$onboardingOfficer')
    ";
    $totalPaymentsResult = $conn->query($totalPaymentsSql);
    $totalPaymentsData = $totalPaymentsResult->fetch_assoc();

    return [
        'total_loans' => $totalLoansData['total_loans'],
        'total_amount_disbursed' => $totalLoansData['total_amount_disbursed'],
        'total_interest_earned' => $totalInterestData['total_interest_earned'],
        'total_payments_received' => $totalPaymentsData['total_payments_received']
    ];
}

function generateTodayManagerBusinessPerformanceReport($conn, $onboardingOfficer) {
    $today = date('Y-m-d');

    // Total loans disbursed today by the specific manager
    $totalLoansTodaySql = "
        SELECT COUNT(*) AS total_loans_today, SUM(requested_amount) AS total_amount_disbursed_today
        FROM active_loans
        WHERE DATE(created_at) = '$today' AND onboarding_officer = '$onboardingOfficer'
    ";
    $totalLoansTodayResult = $conn->query($totalLoansTodaySql);
    $totalLoansTodayData = $totalLoansTodayResult->fetch_assoc();

    // Total interest earned today by the specific manager
    $totalInterestTodaySql = "
        SELECT SUM(
            CASE
                WHEN interest_rate_period = 'Yearly' THEN requested_amount * (interest_rate / 100) / 365
                WHEN interest_rate_period = 'Monthly' THEN requested_amount * (interest_rate / 100) / 30
                WHEN interest_rate_period = 'Weekly' THEN requested_amount * (interest_rate / 100) / 7
            END
        ) AS total_interest_earned_today
        FROM active_loans
        WHERE DATE(created_at) = '$today' AND onboarding_officer = '$onboardingOfficer' AND loan_status IN ('Active', 'Cleared')
    ";
    $totalInterestTodayResult = $conn->query($totalInterestTodaySql);
    $totalInterestTodayData = $totalInterestTodayResult->fetch_assoc();

    // Total payments received today
    $totalPaymentsTodaySql = "
        SELECT SUM(amount) AS total_payments_received_today 
        FROM payments 
        WHERE DATE(payment_date) = '$today' AND loan_id IN (SELECT loan_id FROM active_loans WHERE onboarding_officer = '$onboardingOfficer')
    ";
    $totalPaymentsTodayResult = $conn->query($totalPaymentsTodaySql);
    $totalPaymentsTodayData = $totalPaymentsTodayResult->fetch_assoc();

    return [
        'total_loans_today' => $totalLoansTodayData['total_loans_today'],
        'total_amount_disbursed_today' => $totalLoansTodayData['total_amount_disbursed_today'],
        'total_interest_earned_today' => $totalInterestTodayData['total_interest_earned_today'],
        'total_payments_received_today' => $totalPaymentsTodayData['total_payments_received_today']
    ];
}

function getTransactionsData($conn) {
    $sql = "
        SELECT 
            p.loan_id, 
            c.first_name, 
            c.last_name, 
            c.national_id, 
            p.amount AS received_payment, 
            p.payment_date AS transaction_date, 
            p.transaction_reference, 
            l.requested_amount AS principal
        FROM 
            payments p
        JOIN 
            loan_info l ON p.loan_id = l.loan_id
        JOIN 
            clients c ON l.client_id = c.client_id
        ORDER BY 
            p.payment_date DESC
    ";
    $result = $conn->query($sql);
    $transactions = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }
    }
    return $transactions;
}

function getManagerTransactionsData($conn, $onboardingOfficer) {
    $sql = "
        SELECT 
            p.loan_id, 
            c.first_name, 
            c.last_name, 
            c.national_id, 
            p.amount AS received_payment, 
            p.payment_date AS transaction_date, 
            p.transaction_reference, 
            l.requested_amount AS principal
        FROM 
            payments p
        JOIN 
            loan_info l ON p.loan_id = l.loan_id
        JOIN 
            clients c ON l.client_id = c.client_id
        WHERE 
            l.onboarding_officer = ?
        ORDER BY 
            p.payment_date DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $onboardingOfficer);
    $stmt->execute();
    $result = $stmt->get_result();
    $transactions = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }
    }
    $stmt->close();
    return $transactions;
}

function calculateEMI($principal, $interest_rate, $duration, $interest_rate_period, $duration_period) {
    if ($interest_rate_period === 'Yearly') {
        $interest = $principal * ($interest_rate / 100);
    } elseif ($interest_rate_period === 'Monthly') {
        $interest = $principal * ($interest_rate / 100);
    } elseif ($interest_rate_period === 'Weekly') {
        $interest = $principal * ($interest_rate / 100);
    }

    if ($duration_period === 'Year') {
        $emi = ($principal / $duration) + $interest;
    } elseif ($duration_period === 'Month') {
        $emi = ($principal / $duration) + $interest;
    } elseif ($duration_period === 'Week') {
        $emi = ($principal / $duration) + $interest;
    }

    return $emi;
}


// Function to calculate loan status info
function getLoanStatusInfo($conn, $loan_info) {
    $loan_id = $loan_info['loan_id'];
    $loan_status_info = [];

    // Activation date
    $activation_date_query = $conn->prepare("SELECT status_change FROM loan_info WHERE loan_id = ? AND loan_status = 'Active'");
    $activation_date_query->bind_param("s", $loan_id);
    $activation_date_query->execute();
    $activation_date_result = $activation_date_query->get_result();
    $activation_date_row = $activation_date_result->fetch_assoc();
    $activation_date = $activation_date_row ? $activation_date_row['status_change'] : null;

    // Calculate interest accrued
    $principal = $loan_info['requested_amount'];
    $interest_rate = $loan_info['interest_rate'];
    $interest_period = $loan_info['interest_rate_period'];
    $duration = $loan_info['duration'];
    $duration_period = $loan_info['duration_period'];

    $periods_passed = calculatePeriodsPassed($activation_date, $duration_period);
    $interest_accrued = $principal * ($interest_rate / 100) * $periods_passed;

    // Calculate equal installments
    $total_interest = $principal * ($interest_rate / 100) * $duration;
    $total_amount_due = $principal + $total_interest;
    $equal_installments = $total_amount_due / $duration;

    // Calculate total payments
    $total_payments_query = $conn->prepare("SELECT SUM(amount) AS total_payments FROM payments WHERE loan_id = ?");
    $total_payments_query->bind_param("s", $loan_id);
    $total_payments_query->execute();
    $total_payments_result = $total_payments_query->get_result();
    $total_payments_row = $total_payments_result->fetch_assoc();
    $total_payments = $total_payments_row ? $total_payments_row['total_payments'] : 0;

    // Calculate balance
    $balance = $total_amount_due - $total_payments;

    // Calculate if client wants to clear loan today
    $clear_today = $principal + $interest_accrued - $total_payments;

    $loan_status_info = [
        'Activation Date' => $activation_date,
        'Interest accrued' => number_format($interest_accrued, 2),
        'Equal Installments' => number_format($equal_installments, 2),
        'Total EIs' => number_format($total_amount_due, 2),
        'EIs due to date' => number_format($equal_installments * $periods_passed, 2),
        'Total Payments' => number_format($total_payments, 2),
        'Balance' => number_format($balance, 2),
        'If client wants to clear loan today' => number_format($clear_today, 2),
    ];

    return $loan_status_info;
}

// Function to calculate periods passed based on duration period
function calculatePeriodsPassed($activation_date, $duration_period) {
    $start_date = new DateTime($activation_date);
    $current_date = new DateTime();
    $interval = $current_date->diff($start_date);

    switch ($duration_period) {
        case 'Week':
            return floor($interval->days / 7);
        case 'Month':
            return $interval->m + ($interval->y * 12);
        case 'Year':
            return $interval->y;
        default:
            return 0;
    }
}


?>
