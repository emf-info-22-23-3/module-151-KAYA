<?php

// Including necessary files for functionality
require_once 'workers/userManager.php';
require_once 'workers/articleManager.php';
require_once 'helpers/dBArticleManager.php';
require_once 'helpers/dBUserManager.php';
require_once 'helpers/dBConfig.php';
require_once 'helpers/dBConnection.php';
require_once 'beans/set.php';
require_once 'beans/source.php';
require_once 'beans/user.php';
require_once 'beans/sourceType.php';

// Start the session to manage user state
session_start();

/**
 * @author Kaya
 */

/**
 * Helper function to generate consistent XML responses that can handle array data
 * 
 * @param bool $success Whether the operation was successful
 * @param string $message Optional message to include in response
 * @param array|null $data Optional data to include in response
 */
function sendXMLResponse($success, $message = '', $data = null) {
    echo "<response>\n";
    // Output the success status
    echo "  <success>" . ($success ? 'true' : 'false') . "</success>\n";

    // If there's a message, add it to the response
    if ($message) {
        echo "  <message>" . htmlspecialchars($message, ENT_XML1, 'UTF-8') . "</message>\n";
    }

    // If there is data to return, process it
    if ($data) {
        // Handle the 'set' data if available
        if (isset($data['set'])) {
            $set = $data['set'];
            echo "  <setWanted>\n";
            // Loop through the set data and output each key-value pair as XML
            foreach ($set as $key => $value) {
                echo "    <" . htmlspecialchars($key, ENT_XML1, 'UTF-8') . ">" 
                     . htmlspecialchars($value, ENT_XML1, 'UTF-8') 
                     . "</" . htmlspecialchars($key, ENT_XML1, 'UTF-8') . ">\n";
            }

            // Loop through and handle sources (CapSource, TunicSource, TrousersSource) if they exist
            foreach (['CapSource', 'TunicSource', 'TrousersSource'] as $sourceKey) {
                if (isset($data[$sourceKey])) {
                    echo "    <" . htmlspecialchars($sourceKey, ENT_XML1, 'UTF-8') . ">\n";
                    // Loop through the source data and output each key-value pair as XML
                    foreach ($data[$sourceKey] as $key => $value) {
                        echo "      <" . htmlspecialchars($key, ENT_XML1, 'UTF-8') . ">" 
                             . htmlspecialchars($value, ENT_XML1, 'UTF-8') 
                             . "</" . htmlspecialchars($key, ENT_XML1, 'UTF-8') . ">\n";
                    }
                    echo "    </" . htmlspecialchars($sourceKey, ENT_XML1, 'UTF-8') . ">\n";
                }
            }
            echo "  </setWanted>\n";
        }

        // Handle source types data if available
        if (isset($data['sourceTypes']) && is_array($data['sourceTypes']) && count($data['sourceTypes']) > 0) {
            echo "  <sourceTypes>\n";
            // Loop through and output each sourceType as XML
            foreach ($data['sourceTypes'] as $item) {
                if (is_array($item)) {
                    echo "    <sourceType>\n";
                    foreach ($item as $itemKey => $itemValue) {
                        echo "      <" . htmlspecialchars($itemKey, ENT_XML1, 'UTF-8') . ">" 
                             . htmlspecialchars($itemValue, ENT_XML1, 'UTF-8') 
                             . "</" . htmlspecialchars($itemKey, ENT_XML1, 'UTF-8') . ">\n";
                    }
                    echo "    </sourceType>\n";
                }
            }
            echo "  </sourceTypes>\n";
        }

        // Handle other armor-related data
        foreach ($data as $key => $items) {
            // Skip 'set' and 'sourceTypes' data
            if ($key !== 'set' && $key !== 'sourceTypes' && !in_array($key, ['CapSource', 'TunicSource', 'TrousersSource'])) {
                echo "  <" . htmlspecialchars($key, ENT_XML1, 'UTF-8') . ">\n";
                if (is_array($items)) {
                    // If the items are in an array, loop through them and output each one
                    foreach ($items as $item) {
                        if (is_array($item)) {
                            echo "    <armor>\n";
                            foreach ($item as $itemKey => $itemValue) {
                                echo "      <" . htmlspecialchars($itemKey, ENT_XML1, 'UTF-8') . ">" 
                                     . htmlspecialchars($itemValue, ENT_XML1, 'UTF-8') 
                                     . "</" . htmlspecialchars($itemKey, ENT_XML1, 'UTF-8') . ">\n";
                            }
                            echo "    </armor>\n";
                        }
                    }
                }
                echo "  </" . htmlspecialchars($key, ENT_XML1, 'UTF-8') . ">\n";
            }
        }
    }

    // End the response
    echo "</response>";
    exit;
}

/**
 * Function to send XML responses specifically for login-related actions
 * 
 * @param bool $success Whether the operation was successful
 * @param string $message Optional message to include in response
 * @param array|null $data Optional data to include in response
 */
function sendXMLResponseLogin($success, $message = '', $data = null) {
    // Set the content type to XML for the response
    header('Content-Type: text/xml');
    echo "<?xml version='1.0' encoding='UTF-8'?>\n";
    echo "<response>\n";
    // Output the success status
    echo "  <success>" . ($success ? 'true' : 'false') . "</success>\n";

    // If there's a message, include it
    if ($message) {
        echo "  <message>" . htmlspecialchars($message) . "</message>\n";
    }

    // If there is data, output it as XML
    if ($data) {
        foreach ($data as $key => $value) {
            echo "  <" . htmlspecialchars($key) . ">" . htmlspecialchars($value) . "</" . htmlspecialchars($key) . ">\n";
        }
    }
    echo "</response>";
    exit;
}

// Session management functions

/**
 * Checks if the user is logged in
 * 
 * @return bool True if the user is logged in, otherwise false
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Checks if the logged-in user has an admin role
 * 
 * @return bool True if the user is an admin, otherwise false
 */
function isAdmin() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'Administrator';
}

/**
 * Logs a user in by setting session variables
 * 
 * @param User $user The user to log in
 */
function login($user) {
    $_SESSION['user_id'] = $user->getPK();
    $_SESSION['email'] = $user->getEmail();
    $_SESSION['role'] = $user->getRole();
}

/**
 * Logs the user out by clearing the session
 */
function logout() {
    session_unset();
    session_destroy();
}

// Initialize managers for user and articles
$userManager = new UserManager();
$articleManager = new ArticleManager();
// Handle different HTTP methods (POST in this case)
switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        // Get the action from the POST data, defaulting to an empty string if not set
        $action = $_POST['action'] ?? '';
        
        // Switch based on the action received in the POST data
        switch($action) {
            case 'login':
                // Collect login credentials
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                
                // Check if the credentials match a user
                $user = $userManager->checkCredentials($email, $password);
                if ($user) {
                    // If valid, log the user in and send a success response with their details
                    login($user);
                    sendXMLResponseLogin(true, 'Login successful', [
                        'isAdmin' => $user->getRole() === 'Administrator' ? 'true' : 'false',
                        'email' => $user->getEmail(),
                        'role' => $user->getRole()
                    ]);
                } else {
                    // If invalid, send an error response
                    sendXMLResponseLogin(false, 'Invalid credentials');
                }
                break;

            case 'disconnect':
                // Log out the user by calling the logout function
                logout();
                sendXMLResponseLogout(true, 'Logout successful');
                break;

            case 'addSet':
                // Ensure the user is logged in before proceeding
                if (!isLoggedIn()) {
                    http_response_code(401);
                    sendXMLResponse(false, 'Please log in first');
                    break;
                }
            
                // Ensure the user is an admin before allowing set creation
                if (!isAdmin()) {
                    http_response_code(403);
                    sendXMLResponse(false, 'Administrator access required');
                    break;
                }

                // Collect armor set data from POST
                $armorName = $_POST['armorName'] ?? '';
                $armorCapName = $_POST['armorCapName'] ?? '';
                $armorTunicName = $_POST['armorTunicName'] ?? '';
                $armorTrousersName = $_POST['armorTrousersName'] ?? '';
                $armorEffect = $_POST['armorEffect'] ?? '';
                $armorDescription = $_POST['armorDescription'] ?? '';

                // Collect source data for armor pieces
                $armorCapSourceType = $_POST['armorCapSourceType'] ?? '';
                $armorCapSource = $_POST['armorCapSource'] ?? '';
                $armorTunicSourceType = $_POST['armorTunicSourceType'] ?? '';
                $armorTunicSource = $_POST['armorTunicSource'] ?? '';
                $armorTrousersSourceType = $_POST['armorTrousersSourceType'] ?? '';
                $armorTrousersSource = $_POST['armorTrousersSource'] ?? '';
            
                // Handle the uploaded image, if it exists
                if (isset($_FILES['armorImage']) && $_FILES['armorImage']['error'] === UPLOAD_ERR_OK) {
                    $imageTempPath = $_FILES['armorImage']['tmp_name']; // Temporary path for the uploaded image
                    $imageName = $_FILES['armorImage']['name']; // Original image name
                    $imagePath = 'uploads/' . $imageName; // Path where the image will be saved
                    move_uploaded_file($imageTempPath, $imagePath); // Move image to final location
                } else {
                    $imagePath = ''; // No image uploaded, set to an empty string
                }

                // Create new source objects for the armor pieces
                $armorCapSourceNew = new Source(
                    null, // Auto-generate ID
                    $armorCapSource, // Source name
                    $armorCapSourceType // Source type
                );

                $armorTunicSourceNew = new Source(
                    null, 
                    $armorTunicSource, 
                    $armorTunicSourceType
                );

                $armorTrousersSourceNew = new Source(
                    null, 
                    $armorTrousersSource, 
                    $armorTrousersSourceType
                );

                // Create the Set object with the provided data
                $set = new Set(
                    null, // Auto-generate ID
                    $_SESSION['user_id'], // User ID from session
                    $armorName, // Armor name
                    $armorCapName, // Cap name
                    $armorTunicName, // Tunic name
                    $armorTrousersName, // Trousers name
                    $armorDescription, // Description
                    $armorEffect, // Effect
                    null, // Placeholder for Cap source ID
                    null, // Placeholder for Tunic source ID
                    null, // Placeholder for Trousers source ID
                    $imagePath // Image path (if any)
                );
            
                // Call the manager to add the set to the database
                $result = $articleManager->addSet($set, $armorCapSourceNew, $armorTunicSourceNew, $armorTrousersSourceNew);
            
                // Send a response based on the success of the addition
                sendXMLResponse($result !== false, $result ? 'Set added successfully' : 'Failed to add set');
                break;

            default:
                // Handle unknown actions
                sendXMLResponse(false, 'Invalid action');
                break;
        }
        break;

        case 'GET':
            // Get the action from the GET data, defaulting to an empty string if not set
            $action = $_GET['action'] ?? '';
            
            // Switch based on the action received in the GET data
            switch($action) {
                case 'getAnnoncesForArmor':
                    // Ensure the user is logged in before proceeding
                    if (!isLoggedIn()) {
                        http_response_code(401);
                        sendXMLResponse(false, 'Please log in first');
                        break;
                    }
    
                    // Get the armor set ID from the GET request
                    $id = $_GET['id'] ?? '';
                    
                    // Retrieve the armor set from the database using the ID
                    $set = $articleManager->getSet($id);
    
                    if ($set) {
                        // If the set is found, prepare the set data
                        $setData = array(
                            'id' => $set->getPkSet(), // Primary key of the set
                            'name' => $set->getNom(), // Name of the armor set
                            'cap_name' => $set->getCapNom(), // Name of the cap
                            'tunic_name' => $set->getTunicNom(), // Name of the tunic
                            'trousers_name' => $set->getTrousersNom(), // Name of the trousers
                            'description' => $set->getDescription(), // Set description
                            'effect' => $set->getEffet(), // Set effect
                            'cap_source' => $set->getFkCapSource(), // Foreign key for the cap source
                            'tunic_source' => $set->getFkTunicSource(), // Foreign key for the tunic source
                            'trousers_source' => $set->getFkTrousersSource(), // Foreign key for the trousers source
                            'image' => $set->getImageSet() // Image of the set
                        );
    
                        // Retrieve the source objects by their foreign keys
                        $CapSource = $articleManager->readSourceByID($set->getFkCapSource()); 
                        $TunicSource = $articleManager->readSourceByID($set->getFkTunicSource()); 
                        $TrousersSource = $articleManager->readSourceByID($set->getFkTrousersSource()); 
                
                        // Prepare the response array, including set and source objects
                        $response = [
                            'set' => $setData,
                            'CapSource' => $CapSource ? [
                                'id' => $CapSource->getId(),
                                'source' => $CapSource->getSource(),
                                'type_source' => $CapSource->getTypeSourceId()
                            ] : null,
                            'TunicSource' => $TunicSource ? [
                                'id' => $TunicSource->getId(),
                                'source' => $TunicSource->getSource(),
                                'type_source' => $TunicSource->getTypeSourceId()
                            ] : null,
                            'TrousersSource' => $TrousersSource ? [
                                'id' => $TrousersSource->getId(),
                                'source' => $TrousersSource->getSource(),
                                'type_source' => $TrousersSource->getTypeSourceId()
                            ] : null
                        ];                    
                
                        // Send the response with the set and the associated sources
                        sendXMLResponse(true, '', $response);
                        break;
                    } else {
                        // Handle the case where the set is not found
                        sendXMLResponse(false, 'Set not found');
                        break;
                    }
                    break;
                
                case 'getArmorNames':
                    // Ensure the user is logged in before proceeding
                    if (!isLoggedIn()) {
                        http_response_code(401);
                        sendXMLResponse(false, 'Please log in first');
                        break;
                    }
    
                    // Retrieve the list of armor names from the database
                    $armorNames = $articleManager->getArmorNames();
                    if ($armorNames) {
                        // If armor names are found, send them in the response
                        sendXMLResponse(true, '', array('armorNames' => $armorNames));
                        break;
                    } else {
                        // Handle the case where no armor names are found
                        sendXMLResponse(false, 'Armor names not found');
                        break;
                    }
                    break;
    
                case 'getSourceTypes':
                    // Ensure the user is logged in before proceeding
                    if (!isLoggedIn()) {
                        http_response_code(401);
                        sendXMLResponse(false, 'Please log in first');
                        break;
                    }
    
                    // Retrieve the list of source types from the database
                    $sourceTypes = $articleManager->getSourceTypes();
                    if ($sourceTypes) {
                        // If source types are found, send them in the response
                        sendXMLResponse(true, 'Source types found', array('sourceTypes' => $sourceTypes));
                        break;
                    } else {
                        // Handle the case where no source types are found
                        sendXMLResponse(false, 'Source types not found');
                        break;
                    }
                break;
            }
            break;
    // Case for PUT request - used for updating an existing armor set
    case 'PUT':
        // Ensure the user is logged in
        if (!isLoggedIn()) {
            http_response_code(401);
            sendXMLResponse(false, 'Please log in first');
            break;
        }

        // Ensure the user has admin access for this operation
        if (!isAdmin()) {
            http_response_code(403);
            sendXMLResponse(false, 'Administrator access required');
            break;
        }

        // Read XML data from the incoming request
        $xmlData = file_get_contents('php://input');
        
        // Parse the XML data into a SimpleXML object
        $xml = simplexml_load_string($xmlData);
        if ($xml === false) {
            sendXMLResponse(false, 'Invalid XML data');
            break;
        }

        // Extract values from the parsed XML for armor set details
        $armorName = (string) $xml->armorName;
        $armorCapName = (string) $xml->armorCapName;
        $armorTunicName = (string) $xml->armorTunicName;
        $armorTrousersName = (string) $xml->armorTrousersName;
        $armorEffect = (string) $xml->armorEffect;
        $armorDescription = (string) $xml->armorDescription;
        $armorCapSourceType = (string) $xml->armorCapSourceType;
        $armorCapSource = (string) $xml->armorCapSource;
        $armorTunicSourceType = (string) $xml->armorTunicSourceType;
        $armorTunicSource = (string) $xml->armorTunicSource;
        $armorTrousersSourceType = (string) $xml->armorTrousersSourceType;
        $armorTrousersSource = (string) $xml->armorTrousersSource;

        // Extract source IDs and the selected armor ID
        $idCapSource = (string) $xml->idCapSource;
        $idTunicSource = (string) $xml->idTunicSource;
        $idTrousersSource = (string) $xml->idTrousersSource;
        $selectedArmorId = (string) $xml->selectedArmorId;    

        // Handle the uploaded image if present
        $imagePath = isset($_FILES['armorImage']) && $_FILES['armorImage']['error'] === UPLOAD_ERR_OK 
            ? 'uploads/' . $_FILES['armorImage']['name'] 
            : '';

        // Create Source objects for each piece of the armor set if necessary
        $armorCapSourceObj = ($armorCapSource && $armorCapSourceType) ? new Source($idCapSource, $armorCapSource, $armorCapSourceType) : null;
        $armorTunicSourceObj = ($armorTunicSource && $armorTunicSourceType) ? new Source($idTunicSource, $armorTunicSource, $armorTunicSourceType) : null;
        $armorTrousersSourceObj = ($armorTrousersSource && $armorTrousersSourceType) ? new Source($idTrousersSource, $armorTrousersSource, $armorTrousersSourceType) : null;

        // Create a Set object to represent the updated armor set
        $set = new Set(
            $selectedArmorId,
            $_SESSION['user_id'],  // Using the current logged-in user's ID
            $armorName,
            $armorCapName,
            $armorTunicName,
            $armorTrousersName,
            $armorDescription,
            $armorEffect,
            $idCapSource,
            $idTunicSource,
            $idTrousersSource,
            $imagePath
        );

        // Attempt to update the set in the database
        $result = $articleManager->updateSet($set, $armorCapSourceObj, $armorTunicSourceObj, $armorTrousersSourceObj);

        // Return a response based on the result of the update
        sendXMLResponse($result, $result ? 'Set updated successfully' : 'Failed to update set');
            break;

    // Case for DELETE request - used for deleting an armor set
    case 'DELETE':
        // Ensure the user is logged in
        if (!isLoggedIn()) {
            http_response_code(401);
            sendXMLResponse(false, 'Please log in first');
            break;
        }

        // Ensure the user has admin access for this operation
        if (!isAdmin()) {
            http_response_code(403);
            sendXMLResponse(false, 'Administrator access required');
            break;
        }
        
            // Read and parse the XML data for the deletion request
        $xmlData = file_get_contents('php://input');
        $xml = simplexml_load_string($xmlData);

        if (!$xml) {
            sendXMLResponse(false, 'Invalid XML format');
            break;
        }

        // Extract values needed for deletion
        $idSet = (string) ($xml->idSet ?? '');
        $idCapSource = (string) ($xml->idCapSource ?? '');
        $idTunicSource = (string) ($xml->idTunicSource ?? '');
        $idTrousersSource = (string) ($xml->idTrousersSource ?? '');

        // Check if any required parameters are missing
        if (empty($idSet) || empty($idCapSource) || empty($idTunicSource) || empty($idTrousersSource)) {
            sendXMLResponse(false, 'Missing required parameters');
            break;
        }

        // Proceed with deleting the set
        $result = $articleManager->deleteSet($idSet, $idCapSource, $idTunicSource, $idTrousersSource);
        
        // Send the response based on the deletion result
        sendXMLResponse($result, $result ? 'Set and associated sources deleted successfully' : 'Failed to delete set');
        break;

    // Default case - invalid request method
    default:
        sendXMLResponse(false, 'Invalid request method');
        break;

    }   
?>
