<?php
require_once '../config/dbconnect.php';

if (isset($_GET['token'])) {
    $token = trim($_GET['token']);
    
    // Log to PHP error log
    error_log("Verification attempt with token: " . $token);
    
    $database = new Database();
    $conn = $database->getConnection();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get database connection details
    $debugOutput = "Database settings check:\n";
    try {
        $debugOutput .= "PHP Version: " . phpversion() . "\n";
        $debugOutput .= "PDO Drivers: " . implode(", ", PDO::getAvailableDrivers()) . "\n";
        $debugOutput .= "Error Mode: " . $conn->getAttribute(PDO::ATTR_ERRMODE) . "\n";
    } catch (Exception $e) {
        $debugOutput .= "Error getting database details: " . $e->getMessage() . "\n";
    }

    // Initialize variables to avoid undefined variable warnings
    $success = false;
    $message = '';
    $iconHtml = '<i class="fas fa-exclamation-circle"></i>';
    $debugInfo = '';

    try {
        $cleanedToken = trim($token);
        
        // Output for debugging
        $debugOutput = "Cleaned token: '$cleanedToken'\n";

        // Check for database connectivity first
        try {
            $testQuery = "SELECT 1";
            $testStmt = $conn->query($testQuery);
            $debugOutput .= "Database connection successful\n";
        } catch (PDOException $e) {
            $debugOutput .= "Database connection test failed: " . $e->getMessage() . "\n";
        }

        // First, check if the token exists without any conditions
        $debugOutput .= "Checking if token exists in database...\n";
        
        // Get database schema information
        $debugOutput .= "Checking database schema...\n";
        try {
            // List all tables to verify the correct table name
            $tablesQuery = "SELECT TABLE_SCHEMA, TABLE_NAME FROM INFORMATION_SCHEMA.TABLES";
            $tablesStmt = $conn->query($tablesQuery);
            if ($tablesStmt) {
                $debugOutput .= "Available tables in database:\n";
                while ($tableRow = $tablesStmt->fetch(PDO::FETCH_ASSOC)) {
                    $debugOutput .= "- " . $tableRow['TABLE_SCHEMA'] . "." . $tableRow['TABLE_NAME'] . "\n";
                }
            } else {
                $debugOutput .= "Could not retrieve table list\n";
            }
        } catch (PDOException $e) {
            $debugOutput .= "Error checking schema: " . $e->getMessage() . "\n";
        }
        
        // Try with different table name formats
        $possibleTableNames = [
            "taaltourismdb.users", // This is the most likely one based on the schema output
            "[taaltourismdb].[users]",
            "users", 
            "dbo.users", 
            "[users]", 
            "[dbo].[users]",
            "taaltourismdb.dbo.users",
            "[taaltourismdb].[dbo].[users]"
        ];
        
        // Find the first table name that works
        $tableName = null;
        foreach ($possibleTableNames as $testTableName) {
            try {
                $testQuery = "SELECT TOP 1 1 FROM $testTableName";
                $testStmt = $conn->query($testQuery);
                if ($testStmt && $testStmt->rowCount() >= 0) {
                    $tableName = $testTableName;
                    $debugOutput .= "Successfully connected to table: $tableName\n";
                    break;
                }
            } catch (PDOException $e) {
                $debugOutput .= "Table '$testTableName' test failed: " . $e->getMessage() . "\n";
            }
        }
        
        // Based on the schema listing, we know the table is taaltourismdb.users
        if (!$tableName) {
            $tableName = "taaltourismdb.users";
            $debugOutput .= "Using 'taaltourismdb.users' based on schema listing.\n";
        }

        // Replace the missing check statements after finding the table name

        // Now check for the token in the table we found
        try {
            $checkQuery = "SELECT * FROM $tableName WHERE emailveriftoken = ?";
            $debugOutput .= "Executing query: $checkQuery with token: " . substr($cleanedToken, 0, 10) . "...\n";
            
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bindParam(1, $cleanedToken, PDO::PARAM_STR);
            $executed = $checkStmt->execute();
            
            if (!$executed) {
                $debugOutput .= "Failed to execute token check query\n";
            }
            
            $debugOutput .= "Rows returned: " . $checkStmt->rowCount() . "\n";
        } catch (PDOException $e) {
            $debugOutput .= "Error executing token check: " . $e->getMessage() . "\n";
            $checkStmt = null;
        }
        
        // Get connection info for debugging
        $debugOutput .= "Database driver: " . $conn->getAttribute(PDO::ATTR_DRIVER_NAME) . "\n";
        $debugOutput .= "Server version: " . $conn->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n";
        $debugOutput .= "Client version: " . $conn->getAttribute(PDO::ATTR_CLIENT_VERSION) . "\n";
        
        // Check that the table actually has data
        try {
            $countQuery = "SELECT COUNT(*) as total FROM $tableName";
            $countStmt = $conn->query($countQuery);
            if ($countStmt) {
                $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
                $debugOutput .= "Total rows in $tableName: " . $countResult['total'] . "\n";
                
                if ($countResult['total'] == 0) {
                    $debugOutput .= "WARNING: Table is empty!\n";
                }
            }
        } catch (PDOException $ce) {
            $debugOutput .= "Error counting rows: " . $ce->getMessage() . "\n";
        }
        
        // Examine table structure
        try {
            $columnQuery = "SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH 
                          FROM INFORMATION_SCHEMA.COLUMNS 
                          WHERE TABLE_SCHEMA = 'taaltourismdb' 
                          AND TABLE_NAME = 'users'";
            $columnStmt = $conn->query($columnQuery);
            
            if ($columnStmt && $columnStmt->rowCount() > 0) {
                $debugOutput .= "Columns in taaltourismdb.users table:\n";
                while ($col = $columnStmt->fetch(PDO::FETCH_ASSOC)) {
                    $debugOutput .= "- " . $col['COLUMN_NAME'] . " (" . $col['DATA_TYPE'];
                    if (!empty($col['CHARACTER_MAXIMUM_LENGTH'])) {
                        $debugOutput .= "(" . $col['CHARACTER_MAXIMUM_LENGTH'] . ")";
                    }
                    $debugOutput .= ")\n";
                }
            } else {
                $debugOutput .= "Could not get column information\n";
            }
        } catch (PDOException $ce) {
            $debugOutput .= "Error getting column information: " . $ce->getMessage() . "\n";
        }
        
        $debugOutput .= "Check token query executed\n";
        
        if ($checkStmt && $checkStmt->rowCount() > 0) {
            $userData = $checkStmt->fetch(PDO::FETCH_ASSOC);
            $debugOutput .= "Token found in database!\n";
            $debugOutput .= "User ID: " . (isset($userData['userid']) ? $userData['userid'] : 'N/A') . "\n";
            $debugOutput .= "User status: " . (isset($userData['status']) ? $userData['status'] : 'N/A') . "\n";
            $debugOutput .= "Token expiry: " . (isset($userData['token_expiry']) ? $userData['token_expiry'] : 'N/A') . "\n";
            $debugOutput .= "Current date: " . date('Y-m-d H:i:s') . "\n";
            
            // Check specific conditions
            if (isset($userData['status']) && $userData['status'] !== 'inactive') {
                $debugOutput .= "User is already active!\n";
                $message = 'Your email is already verified.';
                $success = false;
            } else if (isset($userData['token_expiry']) && strtotime($userData['token_expiry']) < time()) {
                $debugOutput .= "Token has expired!\n";
                $message = 'Your verification link has expired. Please request a new one.';
                $success = false;
            } else {
                // Update the user status to active
                $updateQuery = "UPDATE $tableName 
                    SET status = 'active', emailveriftoken = NULL, token_expiry = NULL 
                    WHERE emailveriftoken = ?";
                    
                $updateStmt = $conn->prepare($updateQuery);

                if ($updateStmt && $updateStmt->execute([$cleanedToken])) {
                    $success = true;
                    $iconHtml = '<i class="fas fa-check-circle"></i>';
                    $message = 'Your email has been successfully verified!';
                    $debugInfo = 'User account activated successfully';
                } else {
                    $message = 'Failed to verify your email. Please try again later.';
                    $debugInfo = 'Database update failed: ' . ($updateStmt ? implode(', ', $updateStmt->errorInfo()) : 'Unknown error');
                }
            }
        } else {
            $debugOutput .= "Token not found in database or error occurred!\n";
            if ($checkStmt) {
                $debugOutput .= "PDO Error Info: " . print_r($checkStmt->errorInfo(), true) . "\n";
            }
            
            // Try to get sample data from the users table to verify structure
            $debugOutput .= "Trying to get a sample row...\n";
            try {
                $altQuery = "SELECT TOP 1 * FROM $tableName";
                $altStmt = $conn->query($altQuery);
                
                if ($altStmt && $altStmt->rowCount() > 0) {
                    $sampleRow = $altStmt->fetch(PDO::FETCH_ASSOC);
                    $debugOutput .= "Sample row from users table: " . print_r($sampleRow, true) . "\n";
                    
                    // Check if token column exists
                    if (isset($sampleRow['emailveriftoken'])) {
                        $debugOutput .= "Token column exists in database\n";
                    } else {
                        $debugOutput .= "WARNING: emailveriftoken column not found in sample row\n";
                        
                        // Check all column names to find similar ones
                        $debugOutput .= "Available columns in users table:\n";
                        foreach ($sampleRow as $column => $value) {
                            $debugOutput .= "- $column\n";
                            
                            // Look for columns that might contain token in their name
                            if (stripos($column, 'token') !== false || 
                                stripos($column, 'verification') !== false || 
                                stripos($column, 'verify') !== false || 
                                stripos($column, 'email') !== false) {
                                $debugOutput .= "  Possible token column: $column\n";
                            }
                        }
                    }
                } else {
                    $debugOutput .= "Could not get a sample row from the users table\n";
                    
                    // Try to get table structure if we can't get a row
                    try {
                        $columnQuery = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$tableName'";
                        $columnStmt = $conn->query($columnQuery);
                        
                        if ($columnStmt && $columnStmt->rowCount() > 0) {
                            $debugOutput .= "Columns in '$tableName' table:\n";
                            while ($columnRow = $columnStmt->fetch(PDO::FETCH_ASSOC)) {
                                $debugOutput .= "- " . $columnRow['COLUMN_NAME'] . "\n";
                            }
                        } else {
                            $debugOutput .= "No columns found for table '$tableName'\n";
                        }
                    } catch (PDOException $colEx) {
                        $debugOutput .= "Error getting column information: " . $colEx->getMessage() . "\n";
                    }
                }
            } catch (PDOException $e) {
                $debugOutput .= "Error getting sample row: " . $e->getMessage() . "\n";
                
                // Try an alternative approach with COUNT
                try {
                    $countQuery = "SELECT COUNT(*) as row_count FROM $tableName";
                    $countStmt = $conn->query($countQuery);
                    if ($countStmt) {
                        $countRow = $countStmt->fetch(PDO::FETCH_ASSOC);
                        $debugOutput .= "Total rows in table: " . $countRow['row_count'] . "\n";
                    }
                } catch (PDOException $countEx) {
                    $debugOutput .= "Error counting rows: " . $countEx->getMessage() . "\n";
                }
            }
            
            $message = 'Invalid verification link. Please request a new one.';
            $debugInfo = 'No matching token found in database';
        }
    } catch (PDOException $e) {
        $message = 'An error occurred during verification. Please contact support.';
        $debugInfo = 'Database error: ' . $e->getMessage();
        
        $debugOutput .= "PDO Exception caught: " . $e->getMessage() . "\n";
        $debugOutput .= "Error code: " . $e->getCode() . "\n";
        $debugOutput .= "Stack trace: " . $e->getTraceAsString() . "\n";
    }

    // Escape quotes for JavaScript
    $message = str_replace("'", "\\'", $message);
    $debugInfo = str_replace("'", "\\'", $debugInfo);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Verify Email Address</title>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'>
    <link href='https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap' rel='stylesheet'>
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fa;
            color: #434343;
            padding: 20px;
        }
        pre {
            background: #f4f4f4;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
        .debug-container {
            margin-top: 40px;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .swal2-icon {
            background: none !important;
            border: none !important;
            box-shadow: none !important;
        }
        .swal2-icon-custom {
            font-size: 10px; 
            color: #EC6350; 
        }
        .swal2-title-custom {
            font-size: 24px !important;
            font-weight: bold;
            color: #434343 !important;
        }
        .swal-custom-popup {
            padding: 20px;
            border-radius: 25px;
            font-family: 'Nunito', sans-serif !important;
        }
    </style>
</head>
<body>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js' integrity='sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz' crossorigin='anonymous'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Debug information in console
            console.log('Verification page loaded');
            console.log('Success:', <?php echo $success ? 'true' : 'false'; ?>);
            console.log('Message:', '<?php echo $message; ?>');
            console.log('Debug info:', '<?php echo $debugInfo; ?>');
            
            // Try-catch to catch and log any errors during SweetAlert execution
            try {
                Swal.fire({
                    iconHtml: '<?php echo $iconHtml; ?>', 
                    customClass: {
                        title: 'swal2-title-custom',
                        icon: 'swal2-icon-custom',
                        popup: 'swal-custom-popup'
                    },
                    title: '<?php echo $message; ?>',
                    showConfirmButton: true,
                    confirmButtonText: 'Go to Login'
                }).then(() => {
                    console.log('Redirecting to login page...');
                    window.location.href = '../views/frontend/login.php'; 
                }).catch(error => {
                    console.error('SweetAlert error:', error);
                });
            } catch (error) {
                console.error('Error in verification process:', error);
            }
        });
    </script>
    
    <!-- Debug information section - will be visible on the page -->
    <div class='debug-container'>
        <h2>Debug Information</h2>
        <p>This section is for debugging purposes and should be removed in production.</p>
        <pre id='debug-output'>
Token: <?php echo htmlspecialchars($token); ?>

Success: <?php echo $success ? 'true' : 'false'; ?>

Message: <?php echo htmlspecialchars($message); ?>

Debug Info: <?php echo htmlspecialchars($debugInfo); ?>

Detailed Debug Output:
<?php echo htmlspecialchars($debugOutput); ?>
        </pre>
    </div>
</body>
</html>
<?php
}
?>